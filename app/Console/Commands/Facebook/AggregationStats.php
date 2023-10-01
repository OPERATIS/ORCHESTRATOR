<?php

namespace App\Console\Commands\Facebook;

use App\Models\AggregationFbStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\AggregationStats as CommandsAggregationStats;

class AggregationStats extends CommandsAggregationStats
{
    protected $signature = 'facebook:aggregation-stats {endPeriod?} {connectId?}';

    public function handle(): bool
    {
        // Search period
        $this->beforeHandle();

        $andWhere = '';
        if ($this->connectId) {
            $andWhere = ' AND connect_id = ' . $this->connectId;
        }

        if (Carbon::parse($this->endPeriod)->minute === 0 and Carbon::parse($this->endPeriod)->hour === 0) {
            // 23:35 [00:00] 00:05
            $rows = DB::select("
                SELECT
                    MAX(clicks) - MIN(clicks) as clicks,
                    MAX(impressions) - MIN(impressions) as impressions,
                    MAX(spend) - MIN(spend) as spend,
                    MAX(unique_clicks) - MIN(unique_clicks) as unique_clicks,
                    count(*) as count,
                    connect_id,
                    ad_id
                FROM fb_stats
                WHERE end_period >= '{$this->startPeriod}' and end_period < '{$this->endPeriod}' {$andWhere}
                GROUP BY connect_id, ad_id
            ");
        } elseif (Carbon::parse($this->endPeriod)->minute === 5 and Carbon::parse($this->endPeriod)->hour === 0) {
            // 23:35 00:00 [00:05]
            $rows = DB::select("
                SELECT
                    MAX(clicks) as clicks,
                    MAX(impressions) as impressions,
                    MAX(spend) as spend,
                    MAX(unique_clicks) as unique_clicks,
                    2 as count,
                    connect_id,
                    ad_id
                FROM fb_stats
                WHERE end_period = '{$this->endPeriod}' {$andWhere}
                GROUP BY connect_id, ad_id
            ");
        } else {
            // 10:00:00 [10:01:01 10:03:03 10:04:00 10:05:00] 10:05:01
            $rows = DB::select("
                SELECT
                    MAX(clicks) - MIN(clicks) as clicks,
                    MAX(impressions) - MIN(impressions) as impressions,
                    MAX(spend) - MIN(spend) as spend,
                    MAX(unique_clicks) - MIN(unique_clicks) as unique_clicks,
                    count(*) as count,
                    connect_id,
                    ad_id
                FROM fb_stats
                WHERE end_period >= '{$this->startPeriod}' and end_period <= '{$this->endPeriod}' {$andWhere}
                GROUP BY connect_id, ad_id
            ");
        }

        $aggregationGaStats = [];
        foreach ($rows as $row) {
            if ($row->count === 2) {
                $aggregationGaStats[] = [
                    'clicks' => $row->clicks,
                    'impressions' => $row->impressions,
                    'spend' => $row->spend,
                    'unique_clicks' => $row->unique_clicks,
                    'connect_id' => $row->connect_id,
                    'ad_id' => $row->ad_id,
                    'period' => '5_minutes',
                    'start_period' => $this->startPeriod,
                    'end_period' => $this->endPeriod,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        AggregationFbStat::insert($aggregationGaStats);

        return true;
    }
}
