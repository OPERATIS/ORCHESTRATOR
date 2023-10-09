<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Metrics;
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

        return response()->json([
            'status' => true,
            'metrics' => Metrics::getActualData($user->id)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function chartData(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validateChartData = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validateChartData->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateChartData->errors()
            ], 401);
        }

        return response()->json([
            'status' => true,
            'metrics' => Metrics::getChartData($user->id, $request->get('start'), $request->get('end'))
        ]);
    }
}
