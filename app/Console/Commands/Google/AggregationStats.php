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
        $startPeriod = Carbon::now()->setSeconds(0)->subMinutes(15)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->subMinutes(10)->toDateTimeString();

        $rows = DB::select("
            SELECT
                MAX(impressions) - MIN(impressions) as impressions,
                MAX(pageviews) - MIN(pageviews) as pageviews,
                MAX(unique_pageviews) - MIN(unique_pageviews) as unique_pageviews,
                MAX(ad_clicks) - MIN(ad_clicks) as ad_clicks,
                MAX(ad_cost) - MIN(ad_cost) as ad_cost,
                count(*) as count,
                connect_id,
                unique_table_id,
                MAX(end_period) as period
            FROM ga_stats
            WHERE start_period >= '{$startPeriod}' and end_period <= '{$endPeriod}'
            GROUP BY connect_id, unique_table_id
        ");

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
                    'period' => $row->period
                ];
            }
        }

        AggregationGaStat::insert($aggregationGaStats);

        return true;
    }
}
