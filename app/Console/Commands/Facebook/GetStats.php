<?php

namespace App\Console\Commands\Facebook;

use App\Models\Connect;
use App\Services\Demo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Facebook\GetStats as GetStatsJobs;

class GetStats extends Command
{
    protected $signature = 'facebook:get-stats {type?}';

    public function handle(): bool
    {
        $type = $this->argument('type');

        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(5)->setSeconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->toDateTimeString();

        if (empty($type)) {
            $connects = Connect::where('platform', 'facebook')
                ->get();

            foreach ($connects as $connect) {
                GetStatsJobs::dispatch($connect, $startPeriod, $endPeriod);
            }
        } elseif ($type === 'demo') {
            Demo::createFbStats($startPeriod, $endPeriod);
        }

        return true;
    }
}
