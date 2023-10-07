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
    protected $signature = 'save-analyzes {endPeriod?} {type?}';

    public function handle(): bool
    {
        // Logic for old data
        $endPeriod = $this->argument('endPeriod');

        // Search only demo
        $type = $this->argument('type');

        // Previous 60 hours
        if (!$endPeriod) {
            $startPeriod = Carbon::now()->subHours(60)->setMinutes(0)->setSeconds(0)->toDateTimeString();
        } else {
            $startPeriod = Carbon::parse($endPeriod)->subHours(60)->setMinutes(0)->setSeconds(0)->toDateTimeString();
        }

        $endPeriod = Carbon::parse($startPeriod)->addHours(60)->toDateTimeString();

        // Search metrics for period
        $metrics = Metric::select(array_merge(SixSigma::METRICS, ['user_id']))
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->where('period', '1_hour')
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

            if (count($userMetrics) === 60) {
                foreach (SixSigma::METRICS as $metric) {
                    $cls = $sixSigma->getCLs(array_column($userMetrics, $metric));
                    $analyzes[$metric . '_' . 'ucl'] = $cls['ucl'];
                    $analyzes[$metric . '_' . 'lcl'] = $cls['lcl'];
                }
                $analyzes['start_period'] = $startPeriod;
                $analyzes['end_period'] = $endPeriod;
                $analyzes['period'] = '60_hours';
                $analyzes['period'] = '60_hours';
                $analyzes['created_at'] = Carbon::now();
                $analyzes['updated_at'] = Carbon::now();
                $analyzes['user_id'] = $userId;

                Analysis::insert($analyzes);
            }
        }

        return true;
    }
}
