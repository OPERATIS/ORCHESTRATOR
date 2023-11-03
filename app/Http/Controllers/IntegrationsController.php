<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Models\GaProfile;
use App\Models\User;
use Carbon\Carbon;
use Google\Service\Analytics;
use Google\Service\Localservices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationsController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $integrations = Integration::where('user_id', $user->id)->get();
        $shopify = $integrations->where('platform', 'shopify')->first();
        $facebook = $integrations->where('platform', 'facebook')->first();
        $google = $integrations->where('platform', 'google')->first();

        if ($google) {
            $scope = explode(',', $google->scope);
            if (in_array(Analytics::ANALYTICS, $scope)) {
                $googleAnalytics = $google;
            }

            if (in_array(Localservices::ADWORDS, $scope)) {
                $googleAdwords = $google;
            }

            $gaProfiles = GaProfile::where('integration_id', $google->id)->get();
        }

        return view('integrations.index')
            ->with('user', $user)
            ->with('shopify', $shopify ?? null)
            ->with('facebook', $facebook ?? null)
            ->with('googleAnalytics', $googleAnalytics ?? null)
            ->with('googleAdwords', $googleAdwords ?? null)
            ->with('gaProfiles', $gaProfiles ?? null);
    }

    /**
     * @param $platform
     * @return JsonResponse
     */
    public function getPlatform($platform): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (in_array($platform, ['shopify', 'facebook', 'google'])) {
            $integrations = Integration::where('user_id', $user->id)
                ->where('platform', $platform)
                ->get();

            $integrationIds = [];
            foreach ($integrations as $integration) {
                $integrationIds[] = $integration->id;
            }

            if ($platform === 'google') {
                $gaProfiles = GaProfile::whereIn('integration_id', $integrationIds)->get();

                foreach ($gaProfiles as $gaProfile) {
                    $prepared = [
                        'type' => 'ga_profiles',
                        'id' => $gaProfile->id,
                        'name' => $gaProfile->name,
                        'actual' => $gaProfile->actual,
                        'created_at' => $gaProfile->created_at,
                    ];
                };
            } else {
                foreach ($integrations as $integration) {
                    $prepared = [
                        'type' => 'integrations',
                        'id' => $integration->id,
                        'app_user_slug' => $integration->app_user_slug,
                        'created_at' => $integration->created_at,
                        'deleted_at' => $integration->deleted_at
                    ];
                }
            }
        }

        return response()->json([
            'status' => true,
            'info' => $prepared ?? []
        ]);
    }

    /**
     * @param $platform
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePlatform($platform, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $platforms = $request->get('platform', []);
        foreach ($platforms as $id => $currentPlatform) {
            if ($currentPlatform['type'] === 'integrations') {
                $integration = Integration::where('user_id', $user->id)
                    ->where('platform', $platform)
                    ->where('id', $id)
                    ->first();

                if ($integration) {
                    $integration->deleted_at = isset($currentPlatform['delete']) ? Carbon::now() : null;
                    $integration->save();
                }
            } elseif ($currentPlatform['type'] === 'ga_profiles') {
                $gaProfile = GaProfile::where('id', $id)
                    ->first();

                $integration = Integration::where('id', $gaProfile->integration_id)
                    ->where('platform', $platform)
                    ->where('user_id', $user->id)
                    ->first();

                if ($gaProfile && $integration) {
                    $gaProfile->actual = $currentPlatform['actual'] ?? false;
                    $gaProfile->save();
                }
            }
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
