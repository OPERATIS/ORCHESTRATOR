<?php

namespace App\Console\Commands\Google;

use App\Models\Integration;
use App\Services\Demo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Google\GetStats as GetStatsJobs;

class GetStats extends Command
{
    protected $signature = 'google:get-stats {type?}';

    public function handle(): bool
    {
        $type = $this->argument('type');

        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(5)->setSeconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->setSeconds(0)->toDateTimeString();

        if (empty($type)) {
            $integrations = Integration::where('platform', 'google')
                ->actual()
                ->ignoreDemo()
                ->get();

            foreach ($integrations as $integration) {
                GetStatsJobs::dispatch($integration, $startPeriod, $endPeriod);
            }
        } elseif ($type === 'demo') {
            Demo::createGaStats($startPeriod, $endPeriod);
        }

        return true;
    }
}
