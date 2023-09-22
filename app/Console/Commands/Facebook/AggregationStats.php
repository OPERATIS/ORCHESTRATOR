<?php

namespace App\Console\Commands\Facebook;

use App\Models\AggregationFbStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregationStats extends Command
{
    protected $signature = 'facebook:aggregation-stats';

    public function handle(): bool
    {
        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(10)->setSeconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->subMinutes(5)->setSeconds(0)->toDateTimeString();

        if (Carbon::parse($endPeriod)->minute === 0 and Carbon::parse($endPeriod)->hour === 0) {
            // 23:35 [00:00] 00:05
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
                WHERE end_period = '{$endPeriod}'
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
                WHERE start_period >= '{$startPeriod}' and end_period <= '{$endPeriod}'
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
                    'add_id' => $row->add_id,
                    'period' => '5_minutes',
                    'start_period' => $startPeriod,
                    'end_period' => $endPeriod,
                ];
            }
        }

        AggregationFbStat::insert($aggregationGaStats);

        return true;
    }
}
