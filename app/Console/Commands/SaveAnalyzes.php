<?php

namespace App\Console\Commands;

use App\Models\Analysis;
use App\Models\Metric;
use App\Models\User;
use App\Services\SixSigma;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SaveAnalyzes extends Command
{
    protected $signature = 'save-analyzes {period} {endPeriod?} {type?}';

    public function handle(): bool
    {
        $period = $this->argument('period');

        if (!in_array($period, [Analysis::PERIOD_30_DAYS, Analysis::PERIOD_60_HOURS])) {
            return true;
        }

        // Logic for old data
        $endPeriod = $this->argument('endPeriod');

        // Search only demo
        $type = $this->argument('type');

        // Previous 60 hours
        if (!$endPeriod) {
            $startPeriod = Carbon::now();
        } else {
            $startPeriod = Carbon::parse($endPeriod);
        }

        if ($period === Analysis::PERIOD_60_HOURS) {
            $startPeriod = $startPeriod->subHours(60)->setMinutes(0)->setSeconds(0)->toDateTimeString();
            $endPeriod = Carbon::parse($startPeriod)->addHours(60)->toDateTimeString();
        } elseif ($period === Analysis::PERIOD_30_DAYS) {
            $startPeriod = $startPeriod->subDays(30)->startOfDay()->toDateTimeString();
            $endPeriod = Carbon::parse($startPeriod)->addDays(30)->toDateTimeString();
        }

        $countUserMetrics = 0;
        if ($period === Analysis::PERIOD_60_HOURS) {
            $metricPeriod = Metric::PERIOD_HOUR;
            $countUserMetrics = 60;
        } elseif ($period === Analysis::PERIOD_30_DAYS) {
            $metricPeriod = Metric::PERIOD_DAY;
            $countUserMetrics = 30;
        }

        // Search metrics for period
        $metrics = Metric::select(array_merge(SixSigma::METRICS, ['user_id']))
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->where('period', $metricPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('user_id', User::DEMO_ID);
            })
            ->get();

        // Search unique users
        $userIds = array_unique($metrics->pluck('user_id')->toArray());

        $sixSigma = new SixSigma();
        foreach ($userIds as $userId) {
            $userMetrics = $metrics->where('user_id', $userId)->toArray();
            $analyzes = [];

            if (count($userMetrics) === $countUserMetrics) {
                foreach (SixSigma::METRICS as $metric) {
                    $cls = $sixSigma->getCLs(array_column($userMetrics, $metric));
                    $analyzes[$metric . '_' . 'ucl'] = $cls['ucl'];
                    $analyzes[$metric . '_' . 'lcl'] = $cls['lcl'];
                }
                $analyzes['start_period'] = $startPeriod;
                $analyzes['end_period'] = $endPeriod;
                $analyzes['period'] = $period;
                $analyzes['created_at'] = Carbon::now();
                $analyzes['updated_at'] = Carbon::now();
                $analyzes['user_id'] = $userId;

                Analysis::insert($analyzes);
            }
        }

        return true;
    }
}
