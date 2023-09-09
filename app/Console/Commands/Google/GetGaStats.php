<?php

namespace App\Console\Commands\Google;

use App\Models\Connect;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Google\GetGaStats as GetGaStatsJobs;

class GetGaStats extends Command
{
    protected $signature = 'google:get-ga-stats';

    public function handle(): bool
    {
        $connects = Connect::where('platform', 'google')
            ->get();

        // Every five minutes
        $startPeriod = Carbon::now()->setSeconds(0)->subMinutes(5)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->toDateString();
        foreach ($connects as $connect) {
            GetGaStatsJobs::dispatch($connect, $startPeriod, $endPeriod);
        }

        return true;
    }
}
