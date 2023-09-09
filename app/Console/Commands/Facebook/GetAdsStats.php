<?php

namespace App\Console\Commands\Facebook;

use App\Models\AdsStat;
use App\Models\Connect;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Facebook\GetAdsStats as GetAdsStatsJobs;

class GetAdsStats extends Command
{
    protected $signature = 'facebook:get-ads-stats';

    public function handle(): bool
    {
        $connects = Connect::where('platform', 'facebook')
            ->get();

        // Every five minutes
        $startPeriod = Carbon::now()->setSeconds(0)->subMinutes(5)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->toDateString();
        foreach ($connects as $connect) {
            GetAdsStatsJobs::dispatch($connect, $startPeriod, $endPeriod);
        }

        return true;
    }
}
