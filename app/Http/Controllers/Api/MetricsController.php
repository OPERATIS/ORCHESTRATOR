<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MetricsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function actualData(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $metrics = Metric::select(array_merge(Metric::METRCIS, ['end_period']))
            ->where('user_id', $user->id)
            ->where('period', '1_hour')
            ->orderByDesc('end_period')
            ->limit(2)
            ->get();

        $previousMetric = null;
        $lastMetric = $metrics->first();
        if (count($metrics) > 1) {
            $previousMetric = $metrics->last();
        }

        $previousMetricCorrect = false;
        if (Carbon::parse($previousMetric->end_period)->addHour()->toDateTimeString() === Carbon::parse($lastMetric->end_period)->toDateTimeString()) {
            $previousMetricCorrect = true;
        }

        $prepared = [];
        foreach (Metric::METRCIS as $metric) {
            $prepared[$metric] = [
                'last' => (float)$lastMetric->$metric ?? null,
                'previous' => $previousMetricCorrect ? ((float)$previousMetric->$metric ?? null) : null
            ];
        }

        return response()->json([
            'status' => true,
            'metrics' => $prepared
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function chartData(Request $request): JsonResponse
    {
        $validateUser = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // 2021-01-01 00:00:00
        $start = Carbon::parse($request->get('start'))->startOfDay();
        // 2021-01-01 23:59:59
        $end = Carbon::parse($request->get('end'))->endOfDay();
        $diffInDays = ceil($start->diffInHours($end) / 24);

        $previousStart = $start->clone()->subDays($diffInDays);
        $previousEnd = $start->clone()->endOfDay()->subDay();

        $currentMetrics = Metric::select(array_merge(Metric::METRCIS, ['end_period']))
            ->where('user_id', $user->id)
            ->where('end_period', '>', $start->toDateTimeString())
            ->where('end_period', '<=', $end->toDateTimeString())
            ->where('period', '1_hour')
            ->orderBy('end_period')
            ->get()
            ->toArray();

        $previousMetrics = Metric::select(array_merge(Metric::METRCIS, ['end_period']))
            ->where('user_id', $user->id)
            ->where('end_period', '>', $previousStart->toDateTimeString())
            ->where('end_period', '<=', $previousEnd->toDateTimeString())
            ->where('period', '1_hour')
            ->orderBy('end_period')
            ->get()
            ->toArray();

        return response()->json([
            'status' => true,
            'metrics' => [
                'current' => $currentMetrics,
                'previous' => $previousMetrics
            ]
        ]);
    }
}
