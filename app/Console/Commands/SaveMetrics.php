<?php

namespace App\Console\Commands;

use App\Models\Connect;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Metric;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SaveMetrics extends Command
{
    protected $signature = 'save-metrics';

    public function handle(): bool
    {
        // Every five minutes
        $startPeriod = Carbon::now()->setMinutes(0)->setSeconds(0)->subHour()->toDateTimeString();
        $endPeriod = Carbon::now()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        $now = Carbon::now()->setMinutes(0)->setSeconds(0);

        $connects = Connect::get();
        $connectIds = [];
        $preparedMetrics = [];
        foreach ($connects as $connect) {
            $connectIds[$connect->id] = $connect->user_id;
            if (!isset($preparedMetrics[$connect->user_id])) {
                $preparedMetrics[$connect->user_id] = [
                    'user_id' => $connect->user_id,
                    'period' => $endPeriod,
                    'reach' => 0,
                    'l' => 0,
                    'p' => 0,
                    'pu' => 0,
                    'd' => 0,
                    'q1' => 0,
                    'cls' => 0,
                    'returns' => 0,
                    'q' => 0,
                    'count_customers' => 0,
                    'ads_cls' => 0,
                    'ltv' => 0,
                    'r' => 0,
                    'c1' => 0,
                    'c' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        // Facebook
        $fbMetrics = FbStat::selectRaw("
            sum(impressions) as REACH,
            sum(clicks) as L,
            connect_id
        ")
            ->where('start_period', '>=', $startPeriod)
            ->where('end_period', '<', $endPeriod)
            ->groupBy(['connect_id'])
            ->get();


        foreach ($fbMetrics as $fbMetric) {
            $userId = $connectIds[$fbMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['reach'] = $fbMetric->reach;
                $preparedMetrics[$userId]['l'] = $fbMetric->l;
            }
        }

        // Google
        $gaMetrics = GaStat::selectRaw("
            sum(pageviews) as REACH,
            sum(ad_clicks) as L,
            connect_id
        ")
            ->where('start_period', '>=', $startPeriod)
            ->where('end_period', '<', $endPeriod)
            ->groupBy(['connect_id'])
            ->get();

        foreach ($gaMetrics as $gaMetric) {
            $userId = $connectIds[$gaMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['reach'] += $gaMetric->reach;
                $preparedMetrics[$userId]['l'] += $gaMetric->l;
            }
        }

        // Order
        $orderMetrics = Order::selectRaw("
            sum(total_line_items_price) as P,
            sum(count_line_items) as sumD,
            count(*) as countOrders,
            count(DISTINCT(customer_id)) + count(DISTINCT CASE WHEN customer_id IS NULL THEN 1 END) as countCustomers,
            count(CASE WHEN ads THEN 1 END) as AdsCLs,
            connect_id
        ")
            ->where('order_created_at', '>=', $startPeriod)
            ->where('order_created_at', '<', $endPeriod)
            ->groupBy(['connect_id'])
            ->get();

        foreach ($orderMetrics as $orderMetric) {
            $userId = $connectIds[$orderMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['p'] = $orderMetric->p;
                $preparedMetrics[$userId]['pu'] = $orderMetric->sumd ? ($orderMetric->p / $orderMetric->sumd) : 0;
                $preparedMetrics[$userId]['d'] = $orderMetric->countorders ? ($orderMetric->sumd / $orderMetric->countorders) : 0;
                $preparedMetrics[$userId]['q1'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
                $preparedMetrics[$userId]['count_customers'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['cls'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['ads_cls'] = $orderMetric->adscls;
                $preparedMetrics[$userId]['returns'] = 0;
                $preparedMetrics[$userId]['q'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
            }
        }

        $orderMetricsReturns = Order::selectRaw("
            count(*) as countOrders,
            connect_id
        ")
            ->where('financial_status', 'refunded')
            ->groupBy(['connect_id'])
            ->get();

        foreach ($orderMetricsReturns as $orderMetric) {
            $userId = $connectIds[$orderMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['returns'] = $preparedMetrics[$userId]['count_customers'] ? ($orderMetric->countorders / $preparedMetrics[$userId]['count_customers']) : 0;
                $preparedMetrics[$userId]['q'] = $preparedMetrics[$userId]['q1'] - $preparedMetrics[$userId]['returns'];
                $preparedMetrics[$userId]['ltv'] = $preparedMetrics[$userId]['p'] * $preparedMetrics[$userId]['q'];
                $preparedMetrics[$userId]['r'] = $preparedMetrics[$userId]['cls'] * $preparedMetrics[$userId]['ltv'];
                $preparedMetrics[$userId]['c1'] = $preparedMetrics[$userId]['reach'] ? $preparedMetrics[$userId]['l']/$preparedMetrics[$userId]['reach'] : 0;
                $preparedMetrics[$userId]['c'] = $preparedMetrics[$userId]['l'] ? $preparedMetrics[$userId]['cls']/$preparedMetrics[$userId]['l'] : 0;
            }
        }

        Metric::insert($preparedMetrics);

        return true;
    }
}
