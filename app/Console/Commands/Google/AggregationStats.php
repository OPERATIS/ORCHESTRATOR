<?php

namespace App\Console\Commands\Google;

use App\Models\AggregationGaStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\AggregationStats as CommandsAggregationStats;

class AggregationStats extends CommandsAggregationStats
{
    protected $signature = 'google:aggregation-stats {endPeriod?} {integrationId?}';

    public function handle(): bool
    {
        $this->beforeHandle();

        $andWhere = '';
        if ($this->integrationId) {
            $andWhere = ' AND integration_id = ' . $this->integrationId;
        }

        if (Carbon::parse($this->endPeriod)->minute === 0 and Carbon::parse($this->endPeriod)->hour === 0) {
            // 23:35 [00:00] 00:05
            $rows = DB::select("
                SELECT
                    MAX(impressions) - MIN(impressions) as impressions,
                    MAX(pageviews) - MIN(pageviews) as pageviews,
                    MAX(unique_pageviews) - MIN(unique_pageviews) as unique_pageviews,
                    MAX(ad_clicks) - MIN(ad_clicks) as ad_clicks,
                    MAX(ad_cost) - MIN(ad_cost) as ad_cost,
                    count(*) as count,
                    integration_id,
                    unique_table_id
                FROM ga_stats
                WHERE end_period >= '{$this->startPeriod}' and end_period < '{$this->endPeriod}' {$andWhere}
                GROUP BY integration_id, unique_table_id
            ");
        } elseif (Carbon::parse($this->endPeriod)->minute === 5 and Carbon::parse($this->endPeriod)->hour === 0) {
            // 23:35 00:00 [00:05]
            $rows = DB::select("
                SELECT
                    MAX(impressions) as impressions,
                    MAX(pageviews) as pageviews,
                    MAX(unique_pageviews) as unique_pageviews,
                    MAX(ad_clicks) as ad_clicks,
                    MAX(ad_cost) as ad_cost,
                    2 as count,
                    integration_id,
                    unique_table_id
                FROM ga_stats
                WHERE end_period = '{$this->endPeriod}' {$andWhere}
                GROUP BY integration_id, unique_table_id
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
                    integration_id,
                    unique_table_id
                FROM ga_stats
                WHERE end_period >= '{$this->startPeriod}' and end_period <= '{$this->endPeriod}' {$andWhere}
                GROUP BY integration_id, unique_table_id
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
                    'integration_id' => $row->integration_id,
                    'unique_table_id' => $row->unique_table_id,
                    'period' => '5_minutes',
                    'start_period' => $this->startPeriod,
                    'end_period' => $this->endPeriod,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        AggregationGaStat::insert($aggregationGaStats);

        return true;
    }
}
