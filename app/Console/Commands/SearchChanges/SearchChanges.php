<?php

namespace App\Console\Commands\SearchChanges;

use App\Models\Alert;
use App\Models\Analysis;
use App\Models\Metric;
use App\Models\User;
use App\Services\Notifications;
use App\Services\SixSigma;
use Carbon\Carbon;
use Illuminate\Console\Command;

abstract class SearchChanges extends Command
{
    protected $period;

    public function handle(): bool
    {
        if (!in_array($this->period, [Metric::PERIOD_HOUR, Metric::PERIOD_DAY])) {
            return true;
        }

        // Logic for old data
        $endPeriod = $this->argument('endPeriod');

        // Search only demo
        $type = $this->argument('type');

        // Search actual metric by current hour
        if (!$endPeriod) {
            $endPeriod = Carbon::now();
        } else {
            $endPeriod = Carbon::parse($endPeriod);
        }

        if ($this->period === Metric::PERIOD_HOUR) {
            $endPeriod = $endPeriod->setMinutes(0)->setSeconds(0)->toDateTimeString();
        } elseif ($this->period === Metric::PERIOD_DAY) {
            $endPeriod = $endPeriod->startOfDay()->toDateTimeString();
        }

        $endPeriodStr = $endPeriod;
        $metrics = Metric::query()
            ->period($this->period)
            ->where('end_period', '=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('user_id', User::DEMO_ID);
            })
            ->get();

        // Search previous analyzes
        $endPeriod = Carbon::parse($endPeriod);
        if ($this->period === Metric::PERIOD_HOUR) {
            $endPeriod = $endPeriod->subHour()->setMinutes(0)->setSeconds(0)->toDateTimeString();
            $bigPeriod = '60_hours';
        } elseif ($this->period === Metric::PERIOD_DAY) {
            $endPeriod = $endPeriod->subDay()->startOfDay()->toDateTimeString();
            $bigPeriod = '30_days';
        }
        $analyzes = Analysis::query()
            ->where('period', $bigPeriod)
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
                        $alert['period'] = $this->period;
                        $alert['created_at'] = Carbon::now();
                        $alert['updated_at'] = Carbon::now();
                        $alert['start_period'] = $startPeriodStr;
                        $alert['end_period'] = $endPeriodStr;
                        $alert['user_id'] = $lastMetric->user_id;

                        $alertModel = Alert::create($alert);

                        if ($this->period === Metric::PERIOD_HOUR) {
                            Notifications::sendAlert($alertModel->user_id, $alertModel);
                        }
                    }
                }
            }
        }

        return true;
    }
}
