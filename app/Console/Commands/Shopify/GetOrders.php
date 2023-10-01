<?php

namespace App\Console\Commands\Shopify;

use App\Models\Connect;
use App\Services\Demo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Shopify\GetOrders as GetOrdersJobs;

class GetOrders extends Command
{
    protected $signature = 'shopify:get-orders {type?}';

    public function handle(): bool
    {
        $type = $this->argument('type');

        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(5)->seconds(0)->toDateTimeString();
        $endPeriod = Carbon::parse($startPeriod)->addMinutes(5)->toDateTimeString();

        if (empty($type)) {
            $connects = Connect::where('platform', 'shopify')
                ->get();

            foreach ($connects as $connect) {
                GetOrdersJobs::dispatch($connect, $startPeriod, $endPeriod);
            }
        } elseif ($type === 'demo') {
            Demo::createOrders($startPeriod, $endPeriod);
        }

        return true;
    }
}
