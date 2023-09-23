<?php

namespace App\Console\Commands;

use App\Models\Analysis;
use App\Models\Metric;
use App\Services\SixSigma;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SaveAnalyzes extends Command
{
    protected $signature = 'save-analyzes';

    public function handle(): bool
    {
        // Previous 60 hours
        $startPeriod = Carbon::now()->subHours(60)->setMinutes(0)->setSeconds(0)->toDateTimeString();
        $endPeriod = Carbon::now()->setMinutes(0)->setSeconds(0)->toDateTimeString();

        // Search metrics for period
        $metrics = Metric::select(array_merge(SixSigma::METRICS, ['user_id']))
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->where('period', '1_hour')
            ->get();

        // Search unique users
        $userIds = array_unique($metrics->pluck('user_id')->toArray());

        $sixSigma = new SixSigma();
        foreach ($userIds as $userId) {
            $userMetrics = $metrics->where('user_id', $userId)->toArray();
            $analyzes = [];
            foreach (SixSigma::METRICS as $metric) {
                $cls = $sixSigma->getCLs(array_column($userMetrics, 'c'));
                $analyzes[$metric . '_' . 'ucl'] = $cls['ucl'];
                $analyzes[$metric . '_' . 'lcl'] = $cls['lcl'];
            }
            $analyzes['start_period'] = $startPeriod;
            $analyzes['end_period'] = $endPeriod;
            $analyzes['period'] = '60_hours';
            $analyzes['period'] = '60_hours';
            $analyzes['created_at'] = Carbon::now();
            $analyzes['updated_at'] = Carbon::now();

            Analysis::insert($analyzes);
        }

        return true;
    }
}
