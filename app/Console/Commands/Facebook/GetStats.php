<?php

namespace App\Console\Commands\Facebook;

use App\Models\Connect;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Facebook\GetStats as GetStatsJobs;

class GetStats extends Command
{
    protected $signature = 'facebook:get-stats';

    public function handle(): bool
    {
        $connects = Connect::where('platform', 'facebook')
            ->get();

        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(5)->setSeconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->toDateTimeString();
        foreach ($connects as $connect) {
            GetStatsJobs::dispatch($connect, $startPeriod, $endPeriod);
        }

        return true;
    }
}
