<?php

namespace App\Http\Controllers;

use App\Models\Connect;
use App\Models\GaProfile;
use App\Models\User;
use Google\Service\Analytics;
use Google\Service\Localservices;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            $connects = Connect::where('user_id', $user->id)->get();
            $shopify = $connects->where('platform', 'shopify')->first();
            $facebook = $connects->where('platform', 'facebook')->first();
            $google = $connects->where('platform', 'google')->first();

            if ($google) {
                $scope = explode(',', $google->scope);
                if (in_array(Analytics::ANALYTICS, $scope)) {
                    $googleAnalytics = $google;
                }

                if (in_array(Localservices::ADWORDS, $scope)) {
                    $googleAdwords = $google;
                }

                $gaProfiles = GaProfile::where('connect_id', $google->id)->get();
            }

            return view('dashboard')
                ->with('user', $user)
                ->with('shopify', $shopify ?? null)
                ->with('facebook', $facebook ?? null)
                ->with('googleAnalytics', $googleAnalytics ?? null)
                ->with('googleAdwords', $googleAdwords ?? null)
                ->with('gaProfiles', $gaProfiles ?? null);
        }

        return view('dashboard')
            ->with('shopify', $shopify ?? null)
            ->with('facebook', $facebook ?? null)
            ->with('googleAnalytics', $googleAnalytics ?? null)
            ->with('googleAdwords', $googleAdwords ?? null)
            ->with('gaProfiles', $gaProfiles ?? null);
    }
}
