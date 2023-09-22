<?php

namespace App\Console\Commands\Shopify;

use App\Models\Connect;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Shopify\GetOrders as GetOrdersJobs;

class GetOrders extends Command
{
    protected $signature = 'shopify:get-orders';

    public function handle(): bool
    {
        $connects = Connect::where('platform', 'shopify')
            ->get();

        // Every five minutes
        $startPeriod = Carbon::now()->subMinutes(5)->seconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->seconds(0)->toDateTimeString();
        foreach ($connects as $connect) {
            GetOrdersJobs::dispatch($connect, $startPeriod, $endPeriod);
        }

        return true;
    }
}
