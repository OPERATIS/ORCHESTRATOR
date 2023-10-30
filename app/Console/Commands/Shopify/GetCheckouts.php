<?php

namespace App\Console\Commands\Shopify;

use App\Models\Integration;
use App\Services\Demo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Shopify\GetCheckouts as GetCheckoutsJobs;

class GetCheckouts extends Command
{
    protected $signature = 'shopify:get-checkouts {type?}';

    public function handle(): bool
    {
        $type = $this->argument('type');

        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(5)->seconds(0)->toDateTimeString();
        $endPeriod = Carbon::parse($startPeriod)->addMinutes(5)->toDateTimeString();

        if (empty($type)) {
            $integrations = Integration::where('platform', 'shopify')
                ->ignoreDemo()
                ->get();

            foreach ($integrations as $integration) {
                GetCheckoutsJobs::dispatch($integration, $startPeriod, $endPeriod);
            }
        } elseif ($type === 'demo') {
            Demo::createCheckouts($startPeriod, $endPeriod);
        }

        return true;
    }
}
