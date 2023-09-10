<?php

namespace App\Console\Commands\Google;

use App\Models\Connect;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Google\GetStats as GetStatsJobs;

class GetStats extends Command
{
    protected $signature = 'google:get-stats';

    public function handle(): bool
    {
        $connects = Connect::where('platform', 'google')
            ->get();

        // Every five minutes
        $startPeriod = Carbon::now()->setSeconds(0)->subMinutes(5)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->toDateTimeString();
        foreach ($connects as $connect) {
            GetStatsJobs::dispatch($connect, $startPeriod, $endPeriod);
        }

        return true;
    }
}
