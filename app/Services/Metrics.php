<?php

namespace App\Services;

use App\Models\Metric;
use Carbon\Carbon;

class Metrics
{
    /**
     * @param int $userId
     * @return array
     */
    public static function getActualData(int $userId): array
    {
        $metrics = Metric::select(array_merge(Metric::METRICS, ['end_period']))
            ->period(Metric::PERIOD_HOUR)
            ->where('user_id', $userId)
            ->orderByDesc('end_period')
            ->limit(2)
            ->get();

        $previousMetric = null;
        $prepared = [];
        if (count($metrics)) {
            $lastMetric = $metrics->first();
            if (count($metrics) > 1) {
                $previousMetric = $metrics->last();
            }

            $previousMetricCorrect = false;
            if ($previousMetric && Carbon::parse($previousMetric->end_period)->addHour()->toDateTimeString() === Carbon::parse($lastMetric->end_period)->toDateTimeString()) {
                $previousMetricCorrect = true;
            }

            foreach (Metric::METRICS as $metric) {
                $last = (float)$lastMetric->$metric ?? null;
                $previous = $previousMetricCorrect ? ((float)$previousMetric->$metric ?? null) : null;
                $sign = $last > 0 ? 1 : -1;
                $prepared[$metric] = [
                    'last' => $last,
                    'previous' => $previous,
                    'percent' => ($previous && $last) ? $sign * round(((abs($last) / abs($previous)) * 100) - 100, 2) : null
                ];
            }
        }

        return $prepared;
    }

    /**
     * @param int $userId
     * @param string $start
     * @param string $end
     * @return array
     */
    public static function getChartData(int $userId, string $start, string $end): array
    {
        // 2021-01-01 00:00:00
        $start = Carbon::parse($start)->startOfDay();
        // 2021-01-01 23:59:59
        $end = Carbon::parse($end)->endOfDay();
        $diffInDays = ceil($start->diffInHours($end) / 24);

        $previousStart = $start->clone()->subDays($diffInDays);
        $previousEnd = $start->clone()->endOfDay()->subDay();

        $currentMetrics = Metric::select(array_merge(Metric::METRICS, ['end_period']))
            ->period(Metric::PERIOD_HOUR)
            ->where('user_id', $userId)
            ->where('end_period', '>', $start->toDateTimeString())
            ->where('end_period', '<=', $end->toDateTimeString())
            ->orderBy('end_period')
            ->get()
            ->toArray();

        $previousMetrics = Metric::select(array_merge(Metric::METRICS, ['end_period']))
            ->period(Metric::PERIOD_HOUR)
            ->where('user_id', $userId)
            ->where('end_period', '>', $previousStart->toDateTimeString())
            ->where('end_period', '<=', $previousEnd->toDateTimeString())
            ->orderBy('end_period')
            ->get()
            ->toArray();

        $metrics = [];
        foreach ($currentMetrics as $currentMetric) {
            foreach (Metric::METRICS as $key) {
                $metrics[$key]['current'][] = [
                    Carbon::parse($currentMetric['end_period'])->getTimestampMs(),
                    $currentMetric[$key]
                ];
            }
        }
        unset($currentMetrics);

        foreach ($previousMetrics as $previousMetric) {
            foreach (Metric::METRICS as $key) {
                $metrics[$key]['previous'][] = [
                    Carbon::parse($previousMetric['end_period'])->addDays(8)->getTimestampMs(),
                    $previousMetric[$key]
                ];
            }
        }
        unset($previousMetrics);

        return $metrics;
    }
}
