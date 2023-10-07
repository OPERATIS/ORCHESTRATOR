<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Analysis;
use App\Models\Metric;
use App\Models\User;
use App\Services\SixSigma;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchAlerts extends Command
{
    protected $signature = 'search-alerts {endPeriod?} {type?}';

    public function handle(): bool
    {
        // Logic for old data
        $endPeriod = $this->argument('endPeriod');

        // Search only demo
        $type = $this->argument('type');

        // Search actual metric by current hour
        if (!$endPeriod) {
            $endPeriod = Carbon::now()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        } else {
            $endPeriod = Carbon::parse($endPeriod)->setMinutes(0)->setSeconds(0)->toDateTimeString();
        }
        $endPeriodStr = $endPeriod;
        $metrics = Metric::query()
            ->where('period', '1_hour')
            ->where('end_period', '=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('user_id', User::DEMO_ID);
            })
            ->get();

        // Search previous analyzes
        $endPeriod = Carbon::parse($endPeriod)->subHour()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        $analyzes = Analysis::query()
            ->where('period', '60_hours')
            ->where('end_period', '=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                $query->where('user_id', User::DEMO_ID);
            })
            ->get();

        $startPeriodStr = $endPeriod;
        foreach ($analyzes as $analysis) {
            $lastMetric = $metrics->where('user_id', $analysis->user_id)
                ->when($type === 'demo', function ($query) {
                    return $query->where('user_id', User::DEMO_ID);
                })
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
                        $alert['start_period'] = $startPeriodStr;
                        $alert['end_period'] = $endPeriodStr;
                        $alert['user_id'] = $lastMetric->user_id;

                        Alert::insert($alert);
                    }
                }
            }
        }

        return true;
    }
}
