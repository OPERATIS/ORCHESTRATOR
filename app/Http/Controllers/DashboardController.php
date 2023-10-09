<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Metrics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $metricsActualData = Metrics::getActualData($user->id);

        return view('dashboard.index')
            ->with('user', $user)
            ->with('metricsActualData', $metricsActualData);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function metricsChart(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validatorMetricsChart = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validatorMetricsChart->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validatorMetricsChart->errors()
            ], 401);
        }

        return response()->json([
            'status' => true,
            'metrics' => Metrics::getChartData($user->id, $request->get('start'), $request->get('end'))
        ]);
    }
}
