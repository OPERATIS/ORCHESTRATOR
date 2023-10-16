<?php

namespace App\Http\Controllers;

use App\Helpers\Shorts;
use App\Models\Alert;
use App\Models\Metric;
use App\Models\User;
use App\Services\Metrics;
use App\Services\Recommendations;
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

        $metricsActualData = Metrics::getActualData($user->id);

        // Search recommendations
        $this->getRecommendationsInfo($user->id, $priorityRecommendation, $lastUpdateRecommendations, $lastAlertIdForRecommendation);

        // Search revenue attribution factors
        $this->getRevenueAttributionFactors($user->id, $revenueAttributionFactors, $lastUpdateRevenueAttributionFactors);

        SEOTools::setTitle('Dashboard - ORCHESTRATOR');

        return view('dashboard.index')
            ->with('user', $user)
            ->with('priorityRecommendation', $priorityRecommendation)
            ->with('lastUpdateRecommendations', $lastUpdateRecommendations)
            ->with('lastAlertIdForRecommendation', $lastAlertIdForRecommendation)
            ->with('revenueAttributionFactors', $revenueAttributionFactors)
            ->with('lastUpdateRevenueAttributionFactors', $lastUpdateRevenueAttributionFactors)
            ->with('metricsActualData', $metricsActualData);
    }

    /**
     * @param int $userId
     * @param $priorityRecommendation
     * @param $lastUpdateRecommendations
     * @param $lastAlertIdForRecommendation
     * @return void
     */
    protected function getRecommendationsInfo(int $userId, &$priorityRecommendation, &$lastUpdateRecommendations, &$lastAlertIdForRecommendation)
    {
        $alertsForRecommendations = Alert::query()
            ->where('user_id', $userId)
            ->forRecommendations()
            ->orderByDesc('end_period')
            ->limit(4)
            ->get();

        // Search last alert
        $endPeriod = $alertsForRecommendations->first();
        // Get only last alerts
        $alertsForRecommendations = $alertsForRecommendations->where('end_period', $endPeriod);

        $actualAlertsForRecommendations = [];
        $lastAlertIds = [];
        foreach ($alertsForRecommendations as $alertForRecommendations) {
            if (empty($lastUpdateRecommendations)) {
                // Logic for demo
                if ($userId === User::DEMO_ID) {
                    $lastUpdateRecommendations = Carbon::parse($alertForRecommendations->end_period)->addMinutes(30);
                } else {
                    $lastUpdateRecommendations = $alertForRecommendations->updated_at;
                }
            }

            $actualAlertsForRecommendations[] = $alertForRecommendations;

            if ($alertForRecommendations->result === Alert::UNCHANGED) {
                if (!isset($lastAlertIds[Alert::UNCHANGED])) {
                    $lastAlertIds[Alert::UNCHANGED] = $alertForRecommendations->id;
                }
            } else {
                if (!isset($lastAlertIds[Alert::INCREASED_DECREASED])) {
                    $lastAlertIds[Alert::INCREASED_DECREASED] = $alertForRecommendations->id;
                }
            }
        }

        if (isset($lastAlertIds[Alert::INCREASED_DECREASED])) {
            $lastAlertIdForRecommendation = $lastAlertIds[Alert::INCREASED_DECREASED];
        } elseif (isset($lastAlertIds[Alert::UNCHANGED])) {
            $lastAlertIdForRecommendation = $lastAlertIds[Alert::UNCHANGED];
        }

        $recommendations = Recommendations::getListAdvice($actualAlertsForRecommendations);

        $priorityRecommendation = null;
        if (isset($recommendations[Alert::INCREASED_DECREASED])) {
            $array = array_reverse($recommendations[Alert::INCREASED_DECREASED]);
            $priorityRecommendation = array_pop($array);
        } elseif (isset($recommendations[Alert::UNCHANGED])) {
            $array = array_reverse($recommendations[Alert::UNCHANGED]);
            $priorityRecommendation = array_pop($array);
        }
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
