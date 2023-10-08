<?php

namespace App\Console\Commands\SearchChanges;

use App\Models\Metric;

class SearchAlerts extends SearchChanges
{
    protected $signature = 'search-alerts {endPeriod?} {type?}';
    protected $period = Metric::PERIOD_HOUR;
}
