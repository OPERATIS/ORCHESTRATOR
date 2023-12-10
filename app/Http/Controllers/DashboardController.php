<?php

namespace App\Http\Controllers;

use App\Helpers\Shorts;
use App\Models\Alert;
use App\Models\Metric;
use App\Models\User;
use App\Services\Metrics;
use App\Services\Recommendations;
use App\Services\Warnings;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Artesaos\SEOTools\Facades\SEOTools;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect('login');
        }

        $metricsActualData = Metrics::getActualData($user->id);

        // Search recommendations
        $recommendations = $this->getRecommendationsInfo($user->id, $lastUpdateRecommendations);

        // Search revenue attribution factors
        $this->getRevenueAttributionFactors($user->id, $revenueAttributionFactors, $lastUpdateRevenueAttributionFactors);

        SEOTools::setTitle('Dashboard - ORCHESTRATOR');

        /** @var Warnings $warnings */
        $warnings = app()->make(Warnings::class);
        $warningWhenShopifyIntegrationNotFound = $warnings->getStatusShopifyIntegrationNotFound($user);
        $warningWhenShopifyIntegratedLess24Hours = $warnings->getStatusShopifyIntegratedLess24Hours($user);
        $warningWhenShopifyIntegratedLess1Hour = $warnings->getStatusShopifyIntegratedLess1Hour($user);

        return view('dashboard.index')
            ->with('user', $user)
            ->with('lastUpdateRecommendations', $lastUpdateRecommendations)
            ->with('recommendations', $recommendations)
            ->with('revenueAttributionFactors', $revenueAttributionFactors)
            ->with('lastUpdateRevenueAttributionFactors', $lastUpdateRevenueAttributionFactors)
            ->with('metricsActualData', $metricsActualData);
    }

    /**
     * @param int $userId
     * @param $lastUpdateRecommendations
     * @return array
     */
    protected function getRecommendationsInfo(int $userId, &$lastUpdateRecommendations): array
    {
        $alertsForRecommendations = Alert::query()
            ->where('user_id', $userId)
            ->forRecommendations()
            ->orderByDesc('end_period')
            ->limit(4)
            ->get();

        // Search last alert
        $lastRecommendation = $alertsForRecommendations->first();

        if ($lastRecommendation) {
            $lastUpdateRecommendations = $lastRecommendation->end_period;
            // Get only last alerts
            $alertsForRecommendations = $alertsForRecommendations->where('end_period', $lastRecommendation->end_period);
        } else {
            $alertsForRecommendations = collect([]);
        }

        $recommendations = [];
        foreach ($alertsForRecommendations as $alertForRecommendations) {
            $recommendations[$alertForRecommendations->id] = Recommendations::getListAdvice($alertForRecommendations);
        }

        return $recommendations;
    }

    /**
     * @param int $userId
     * @param $revenueAttributionFactors
     * @param $lastUpdateRevenueAttributionFactors
     */
    protected function getRevenueAttributionFactors(int $userId, &$revenueAttributionFactors, &$lastUpdateRevenueAttributionFactors)
    {
        $metrics = Metric::where('user_id', $userId)
            ->period(Metric::PERIOD_HOUR)
            ->orderByDesc('end_period')
            ->limit(2)
            ->get();

        if (count($metrics) > 1) {
            $lastMetric = $metrics->first();
            $previousMetric = $metrics->last();
            // Logic for demo
            if ($userId === User::DEMO_ID) {
                $lastUpdateRevenueAttributionFactors = Carbon::parse($lastMetric->end_period)->addMinutes(15);
            } else {
                $lastUpdateRevenueAttributionFactors = $lastMetric->updated_at;
            }

            $revenueAttributionFactors = [];
            foreach (Metric::METRICS as $metric) {
                if ($lastMetric->{$metric} < $previousMetric->{$metric}) {
                    $revenueAttributionFactors['negative'][] = strtoupper($metric) . ' is ' . Shorts::formatNumber($lastMetric->{$metric}, null, 0);
                } elseif ($lastMetric->{$metric} > $previousMetric->{$metric}) {
                    $revenueAttributionFactors['positive'][] = strtoupper($metric) . ' is ' . Shorts::formatNumber($lastMetric->{$metric}, null, 0);
                }
            }
        }
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
            ]);
        }

        return response()->json([
            'status' => true,
            'metrics' => Metrics::getChartData($user->id, $request->get('start'), $request->get('end'))
        ]);
    }
}
