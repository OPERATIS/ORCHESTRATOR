<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Analysis;
use App\Models\Metric;
use App\Services\SixSigma;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchAlerts extends Command
{
    protected $signature = 'search-alerts';

    public function handle(): bool
    {
        // Search actual metric by current hour
        $endPeriod = Carbon::now()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        $metrics = Metric::query()
            ->where('period', '1_hour')
            ->where('end_period', '=', $endPeriod)
            ->get();

        // Search previous analyzes
        $endPeriod = Carbon::now()->subHour()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        $analyzes = Analysis::query()
            ->where('period', '60_hours')
            ->where('end_period', '=', $endPeriod)
            ->get();

        foreach ($analyzes as $analysis) {
            $lastMetric = $metrics->where('user_id', $analysis->user_id)
                ->first();

            if ($lastMetric) {
                foreach (SixSigma::METRICS as $metric) {
                    $alert = [];
                    $alert['metric'] = $metric;

                    if ($lastMetric->{$metric} > $analysis->{$metric . '_ucl'}) {
                        $alert['result'] = 'Increased';
                    } elseif ($lastMetric->{$metric} < $analysis->{$metric . '_lcl'}) {
                        $alert['result'] = 'Decreased';
                    }

                    if (isset($alert['result'])) {
                        $alert['period'] = '1_hour';
                        $alert['created_at'] = Carbon::now();
                        $alert['updated_at'] = Carbon::now();

                        Alert::insert($alert);
                    }
                }
            }
        }

        return true;
    }
}
