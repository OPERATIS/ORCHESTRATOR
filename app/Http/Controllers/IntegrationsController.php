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
        $shopifyAccounts = $this->prepareAccounts($integrations->where('platform', 'shopify'));
        $shopifyFirstConnectedAt = null;
        foreach ($shopifyAccounts as $shopifyAccount) {
            if (empty($shopifyFirstConnectedAt) || Carbon::parse($shopifyFirstConnectedAt) < Carbon::parse($shopifyAccount->created_at)) {
                $shopifyFirstConnectedAt = $shopifyAccount->created_at;
            }
        }

        $facebookAccounts = $this->prepareAccounts($integrations->where('platform', 'facebook'));
        $facebookFirstConnectedAt = null;
        foreach ($facebookAccounts as $facebookAccount) {
            if (empty($facebookFirstConnectedAt) || Carbon::parse($facebookFirstConnectedAt) < Carbon::parse($facebookAccount->created_at)) {
                $facebookFirstConnectedAt = $facebookAccount->created_at;
            }
        }

        $googleAccounts = $this->prepareAccounts($integrations->where('platform', 'google'));
        $googleAnalyticsFirstConnectedAt = null;
        $googleAdwordsFirstConnectedAt = null;
        foreach ($googleAccounts as &$googleAccount) {
            if (isset($googleAccount->analytics)) {
                if (empty($googleAnalyticsFirstConnectedAt) || Carbon::parse($googleAnalyticsFirstConnectedAt) < Carbon::parse($googleAccount->created_at)) {
                    $googleAnalyticsFirstConnectedAt = $googleAccount->created_at;
                }
            }

            if (isset($googleAccount->adwords)) {
                if (empty($googleAdwordsFirstConnectedAt) || Carbon::parse($googleAdwordsFirstConnectedAt) < Carbon::parse($googleAccount->created_at)) {
                    $googleAdwordsFirstConnectedAt = $googleAccount->created_at;
                }
            }
        }

        return view('integrations.index')
            ->with('user', $user)
            ->with('shopifyAccounts', $shopifyAccounts)
            ->with('facebookAccounts', $facebookAccounts)
            ->with('googleAccounts', $googleAccounts)
            ->with('shopifyFirstConnectedAt', $shopifyFirstConnectedAt)
            ->with('facebookFirstConnectedAt', $facebookFirstConnectedAt)
            ->with('googleAnalyticsFirstConnectedAt', $googleAnalyticsFirstConnectedAt)
            ->with('googleAdwordsFirstConnectedAt', $googleAdwordsFirstConnectedAt);
    }

    /**
     * @param $integrations
     * @return mixed
     */
    protected function prepareAccounts($integrations)
    {
        foreach ($integrations as &$integration) {
            // Return platforms for google
            if ($integration->platform === 'google') {
                $scope = explode(',', $integration->scope);
                if (in_array(Analytics::ANALYTICS, $scope)) {
                    $integration->analytics = true;
                }

                if (in_array(Localservices::ADWORDS, $scope)) {
                    $integration->adwords = true;
                }

                $gaProfiles = GaProfile::where('integration_id', $integration->id)->orderBy('id')->get();
                $integration->profiles = $gaProfiles;
            }
            unset($integration->access_token);
            unset($integration->refresh_token);
            unset($integration->expires_in);
            unset($integration->scope);
        }

        return $integrations;
    }

    /**
     * @param $platform
     * @return JsonResponse
     */
    public function getAccounts($platform): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (in_array($platform, ['shopify', 'facebook', 'google'])) {
            $integrations = Integration::where('user_id', $user->id)
                ->where('platform', $platform)
                ->get();

            $accounts = $this->prepareAccounts($integrations);
        }

        return response()->json([
            'status' => true,
            'info' => $accounts ?? []
        ]);
    }

    /**
     * @param $platform
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAccounts($platform, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $accounts = $request->get('accounts', []);
        foreach ($accounts as $account) {
            $integration = Integration::where('user_id', $user->id)
                ->where('platform', $platform)
                ->where('id', $account['id'])
                ->first();

            if ($integration) {
                $integration->actual = $account['actual'] ?? false;
                $integration->deleted_at = isset($account['delete']) ? Carbon::now() : null;
                $integration->save();

                if ($platform === 'google') {
                    foreach ($account['profiles'] as $profile) {
                        $gaProfile = GaProfile::where('id', $profile['id'])
                            ->first();

                        if ($gaProfile) {
                            $gaProfile->actual = $profile['actual'] ?? false;
                            $gaProfile->save();
                        }
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
