<?php

namespace App\Console\Commands;

use App\Models\Connect;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Metrics extends Command
{
    protected $signature = 'metrics';

    public function handle(): bool
    {
        // Every five minutes
        $startPeriod = Carbon::now()->setSeconds(0)->subMinutes(15)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->subMinutes(10)->toDateTimeString();

        $connects = Connect::get();
        $connectIds = [];
        $preparedMetrics = [];
        foreach ($connects as $connect) {
            $connectIds[$connect->id] = $connect->user_id;
            if (!isset($preparedMetrics[$connect->user_id])) {
                $preparedMetrics[$connect->user_id] = [];
            }
        }

        // Facebook
        $fbMetrics = FbStat::selectRaw("
            sum(impressions) as REACH,
            sum(clicks) as L,
            connect_id
        ")
            ->where('start_period', $startPeriod)
            ->where('end_period', $endPeriod)
            ->groupBy(['connect_id'])
            ->get();


        foreach ($fbMetrics as $fbMetric) {
            $userId = $connectIds[$fbMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['REACH'] = $fbMetric->reach;
                $preparedMetrics[$userId]['L'] = $fbMetric->l;
            }
        }

        // Google
        $gaMetrics = GaStat::selectRaw("
            sum(pageviews) as REACH,
            sum(ad_clicks) as L,
            connect_id
        ")
            ->where('start_period', $startPeriod)
            ->where('end_period', $endPeriod)
            ->groupBy(['connect_id'])
            ->get();

        foreach ($gaMetrics as $gaMetric) {
            $userId = $connectIds[$gaMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['REACH'] = ($preparedMetrics[$userId]['REACH'] ?? 0) + $gaMetric->reach;
                $preparedMetrics[$userId]['L'] = ($preparedMetrics[$userId]['L'] ?? 0) + $gaMetric->l;
            }
        }

        // Order
        $orderMetrics = Order::selectRaw("
            sum(total_line_items_price) as P,
            sum(count_line_items) as sumD,
            count(*) as countOrders,
            count(DISTINCT(customer_id)) + count(DISTINCT CASE WHEN customer_id IS NULL THEN 1 END) as countCustomers,
            count(CASE WHEN ads THEN 1 END) as CLs,
            connect_id
        ")
            ->where('updated_at', $startPeriod)
            ->where('updated_at', $endPeriod)
            ->groupBy(['connect_id'])
            ->get();

        foreach ($orderMetrics as $orderMetric) {
            $userId = $connectIds[$orderMetric->connect_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['P'] = $orderMetric->p;
                $preparedMetrics[$userId]['Pu'] = $orderMetric->sumd ? ($orderMetric->p / $orderMetric->sumd) : 0;
                $preparedMetrics[$userId]['D'] = $orderMetric->countorders ? ($orderMetric->sumd / $orderMetric->countorders) : 0;
                $preparedMetrics[$userId]['Q1'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
                $preparedMetrics[$userId]['countCustomers'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['CLs'] = $orderMetric->cls;
                $preparedMetrics[$userId]['Returns'] = 0;
                $preparedMetrics[$userId]['Q'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
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
                $preparedMetrics[$userId]['Returns'] = $orderMetric->countOrders / $preparedMetrics[$userId]['countCustomers'];
                $preparedMetrics[$userId]['Q'] = $preparedMetrics[$userId]['Q1'] - $preparedMetrics[$userId]['Returns'];
            }
        }

        return true;
    }
}
