<?php

namespace App\Console\Commands;

use App\Models\Analysis;
use App\Models\Integration;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Metric;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SaveMetrics extends Command
{
    protected $signature = 'save-metrics {period} {endPeriod?} {type?}';

    public function handle(): bool
    {
        $period = $this->argument('period');

        if (!in_array($period, [Metric::PERIOD_HOUR, Metric::PERIOD_DAY])) {
            return true;
        }

        // Logic for old data
        $endPeriod = $this->argument('endPeriod');

        // Search only demo
        $type = $this->argument('type');

        if (!$endPeriod) {
            $startPeriod = Carbon::now();
        } else {
            $startPeriod = Carbon::parse($endPeriod);
        }

        if ($period === Metric::PERIOD_HOUR) {
            $startPeriod = $startPeriod->subHour()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        } elseif ($period === Metric::PERIOD_DAY) {
            $startPeriod = $startPeriod->subDay()->startOfDay()->toDateTimeString();
        }

        if ($period === Metric::PERIOD_HOUR) {
            $endPeriod = Carbon::parse($startPeriod)->addHours()->toDateTimeString();
        } elseif ($period === Metric::PERIOD_DAY) {
            $endPeriod = Carbon::parse($startPeriod)->addDays()->toDateTimeString();
        }

        if (!$type) {
            $integrations = Integration::get();
        } else {
            $integrations = Integration::whereIn('id', [
                FbStat::DEMO_INTEGRATION_ID,
                GaStat::DEMO_INTEGRATION_ID,
                Order::DEMO_INTEGRATION_ID
            ])->get();
        }

        $integrationIds = [];
        $preparedMetrics = [];
        foreach ($integrations as $integration) {
            $integrationIds[$integration->id] = $integration->user_id;
            if (!isset($preparedMetrics[$integration->user_id])) {
                $preparedMetrics[$integration->user_id] = [
                    'user_id' => $integration->user_id,
                    'period' => $period,
                    'start_period' => $startPeriod,
                    'end_period' => $endPeriod,
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
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        // Facebook
        $fbMetrics = FbStat::selectRaw("
            sum(impressions) as REACH,
            sum(clicks) as L,
            integration_id
        ")
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', FbStat::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($fbMetrics as $fbMetric) {
            $userId = $integrationIds[$fbMetric->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['reach'] = $fbMetric->reach;
                $preparedMetrics[$userId]['l'] = $fbMetric->l;
            }
        }

        // Google
        $gaMetrics = GaStat::selectRaw("
            sum(pageviews) as REACH,
            sum(ad_clicks) as L,
            integration_id
        ")
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', GAStat::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($gaMetrics as $gaMetric) {
            $userId = $integrationIds[$gaMetric->integration_id] ?? null;
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
            count(DISTINCT CASE WHEN ads THEN customer_id END) as AdsCLs,
            integration_id
        ")
            ->where('order_created_at', '>', $startPeriod)
            ->where('order_created_at', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($orderMetrics as $orderMetric) {
            $userId = $integrationIds[$orderMetric->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['p'] = $orderMetric->p;
                $preparedMetrics[$userId]['pu'] = $orderMetric->sumd ? ($orderMetric->p / $orderMetric->sumd) : 0;
                $preparedMetrics[$userId]['d'] = $orderMetric->countorders ? ($orderMetric->sumd / $orderMetric->countorders) : 0;
                $preparedMetrics[$userId]['q1'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
                $preparedMetrics[$userId]['count_customers'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['cls'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['ads_cls'] = $orderMetric->adscls;
                $preparedMetrics[$userId]['returns'] = 0;
                $preparedMetrics[$userId]['c1'] = $preparedMetrics[$userId]['reach'] ? $preparedMetrics[$userId]['l'] / $preparedMetrics[$userId]['reach'] : 0;
                $preparedMetrics[$userId]['c'] = $preparedMetrics[$userId]['l'] ? $preparedMetrics[$userId]['cls'] / $preparedMetrics[$userId]['l'] : 0;
                // Recalculate
                $preparedMetrics[$userId]['q'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
                $preparedMetrics[$userId]['ltv'] = $preparedMetrics[$userId]['p'] * $preparedMetrics[$userId]['q'];
                $preparedMetrics[$userId]['r'] = $preparedMetrics[$userId]['cls'] * $preparedMetrics[$userId]['ltv'];
            }
        }

        $orderMetricsReturns = Order::selectRaw("
            count(*) as countOrders,
            integration_id
        ")
            ->where('financial_status', 'refunded')
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($orderMetricsReturns as $orderMetric) {
            $userId = $integrationIds[$orderMetric->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['returns'] = $preparedMetrics[$userId]['count_customers'] ? ($orderMetric->countorders / $preparedMetrics[$userId]['count_customers']) : 0;
                // Recalculate if found returns
                $preparedMetrics[$userId]['q'] = $preparedMetrics[$userId]['q1'] - $preparedMetrics[$userId]['returns'];
                $preparedMetrics[$userId]['ltv'] = $preparedMetrics[$userId]['p'] * $preparedMetrics[$userId]['q'];
                $preparedMetrics[$userId]['r'] = $preparedMetrics[$userId]['cls'] * $preparedMetrics[$userId]['ltv'];
            }
        }

        Metric::insert($preparedMetrics);

        // Search alerts
        if ($period === Metric::PERIOD_HOUR) {
            Artisan::call("search-alerts '{$endPeriod}' {$type}");
            $analysisPeriod = Analysis::PERIOD_60_HOURS;
        } elseif ($period === Metric::PERIOD_DAY) {
            Artisan::call("search-recommendations '{$endPeriod}' {$type}");
            $analysisPeriod = Analysis::PERIOD_30_DAYS;
        }

        Artisan::call("save-analyzes {$analysisPeriod} '{$endPeriod}' {$type}");

        return true;
    }
}
