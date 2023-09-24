<?php

namespace App\Http\Controllers;

use App\Models\Metric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function metrics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            $jsonMessage = $validator->errors()->first();
            $jsonStatus = false;
        } else {
            $user = $request->user();

            // 2021-01-01 00:00:00
            $start = Carbon::parse($request->get('start'))->startOfDay();
            // 2021-01-01 23:59:59
            $end = Carbon::parse($request->get('end'))->endOfDay();
            $diffInDays = ceil($start->diffInHours($end) / 24);

            $previousStart = $start->clone()->subDays($diffInDays);
            $previousEnd = $start->clone()->endOfDay()->subDay();

            $metrics = Metric::select(['l', 'c', 'p', 'q', 'ltv', 'period'])
                ->where('user_id', $user->id)
                ->where('end_period', '>', $start->toDateTimeString())
                ->where('end_period', '<=', $end->toDateTimeString())
                ->where('period', '1_hour')
                ->orderBy('period')
                ->get()
                ->toArray();

            $previousMetrics = Metric::select(['l', 'c', 'p', 'q', 'ltv', 'period'])
                ->where('user_id', $user->id)
                ->where('end_period', '>', $previousStart->toDateTimeString())
                ->where('end_period', '<=', $previousEnd->toDateTimeString())
                ->where('period', '1_hour')
                ->orderBy('period')
                ->get()
                ->toArray();

            $jsonStatus = true;
            $jsonData = [
                'metrics' => [
                    'current' => $metrics,
                    'previous' => $previousMetrics,
                ]
            ];
        }

        return response()->json([
            'status' => $jsonStatus ?? false,
            'message' => $jsonMessage ?? null,
            'data' => $jsonData ?? null
        ]);
    }
}
