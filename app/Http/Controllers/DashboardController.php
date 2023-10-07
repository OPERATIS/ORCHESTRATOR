<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Models\GaProfile;
use App\Models\User;
use Google\Service\Analytics;
use Google\Service\Localservices;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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

        return view('dashboard')
            ->with('user', $user)
            ->with('shopify', $shopify ?? null)
            ->with('facebook', $facebook ?? null)
            ->with('googleAnalytics', $googleAnalytics ?? null)
            ->with('googleAdwords', $googleAdwords ?? null)
            ->with('gaProfiles', $gaProfiles ?? null);
    }
}
