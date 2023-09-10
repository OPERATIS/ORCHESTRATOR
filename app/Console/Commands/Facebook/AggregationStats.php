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
        $startPeriod = Carbon::now()->setSeconds(0)->subMinutes(15)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->subMinutes(10)->toDateTimeString();

        $rows = DB::select("
            SELECT
                MAX(clicks) - MIN(clicks) as clicks,
                MAX(impressions) - MIN(impressions) as impressions,
                MAX(spend) - MIN(spend) as spend,
                MAX(unique_clicks) - MIN(unique_clicks) as unique_clicks,
                count(*) as count,
                connect_id,
                ad_id,
                MAX(end_period) as period
            FROM fb_stats
            WHERE start_period >= '{$startPeriod}' and end_period <= '{$endPeriod}'
            GROUP BY connect_id, ad_id
        ");

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
                    'period' => $row->period
                ];
            }
        }

        AggregationFbStat::insert($aggregationGaStats);

        return true;
    }
}
