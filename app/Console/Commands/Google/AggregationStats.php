<?php

namespace App\Console\Commands\Google;

use App\Models\AggregationGaStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregationStats extends Command
{
    protected $signature = 'google:aggregation-stats';

    public function handle(): bool
    {
        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(10)->setSeconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->subMinutes(5)->setSeconds(0)->toDateTimeString();

        if (Carbon::parse($endPeriod)->minute === 0 and Carbon::parse($endPeriod)->hour === 0) {
            // 23:35 [00:00] 00:05
            $rows = DB::select("
                SELECT
                    MAX(impressions) as impressions,
                    MAX(pageviews) as pageviews,
                    MAX(unique_pageviews) as unique_pageviews,
                    MAX(ad_clicks) as ad_clicks,
                    MAX(ad_cost) as ad_cost,
                    2 as count,
                    connect_id,
                    unique_table_id
                FROM ga_stats
                WHERE start_period >= '{$startPeriod}' and end_period <= '{$endPeriod}'
                GROUP BY connect_id, unique_table_id
            ");
        } else {
            // 10:00:00 [10:01:01 10:03:03 10:04:00 10:05:00] 10:05:01
            $rows = DB::select("
                SELECT
                    MAX(impressions) - MIN(impressions) as impressions,
                    MAX(pageviews) - MIN(pageviews) as pageviews,
                    MAX(unique_pageviews) - MIN(unique_pageviews) as unique_pageviews,
                    MAX(ad_clicks) - MIN(ad_clicks) as ad_clicks,
                    MAX(ad_cost) - MIN(ad_cost) as ad_cost,
                    count(*) as count,
                    connect_id,
                    unique_table_id
                FROM ga_stats
                WHERE start_period >= '{$startPeriod}' and end_period <= '{$endPeriod}'
                GROUP BY connect_id, unique_table_id
            ");
        }

        $aggregationGaStats = [];
        foreach ($rows as $row) {
            if ($row->count === 2) {
                $aggregationGaStats[] = [
                    'impressions' => $row->impressions,
                    'pageviews' => $row->pageviews,
                    'unique_pageviews' => $row->unique_pageviews,
                    'ad_clicks' => $row->ad_clicks,
                    'ad_cost' => $row->ad_cost,
                    'connect_id' => $row->connect_id,
                    'unique_table_id' => $row->unique_table_id,
                    'period' => '5_minutes',
                    'start_period' => $startPeriod,
                    'end_period' => $endPeriod,
                ];
            }
        }

        AggregationGaStat::insert($aggregationGaStats);

        return true;
    }
}
