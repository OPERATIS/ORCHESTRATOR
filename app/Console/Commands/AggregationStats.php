<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

abstract class AggregationStats extends Command
{
    protected $startPeriod;
    protected $endPeriod;
    protected $integrationId;

    public function beforeHandle()
    {
        // Logic for demo and old data
        $endPeriod = $this->argument('endPeriod');
        $integrationId = $this->argument('integrationId');

        // Every five minutes
        if (!$endPeriod) {
            $startPeriod = Carbon::now()->subMinutes(10)->setSeconds(0)->toDateTimeString();
        } else {
            $startPeriod = Carbon::parse($endPeriod)->subMinutes(10)->setSeconds(0)->toDateTimeString();
        }

        $endPeriod = Carbon::parse($startPeriod)->addMinutes(5)->toDateTimeString();

        $this->startPeriod = $startPeriod;
        $this->endPeriod = $endPeriod;
        $this->integrationId = $integrationId;
    }
}
