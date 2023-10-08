<?php

namespace App\Console\Commands\SearchChanges;

use App\Models\Metric;

class SearchRecommendations extends SearchAlerts
{
    protected $signature = 'search-recommendations {endPeriod?} {type?}';
    protected $period = Metric::PERIOD_DAY;
}
