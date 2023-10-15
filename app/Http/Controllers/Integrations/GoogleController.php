<?php

namespace App\Http\Controllers\Integrations;

use App\Models\Integration;
use App\Models\User;
use App\Services\Google;
use Google\Client;
use Google\Exception;
use Google\Service\Analytics;
use Google\Service\Localservices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class GoogleController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function login(Request $request): RedirectResponse
    {
        $service = $request->get('service');
        if (empty($service)) {
            abort(404);
        }

        $client = new Client();
        $client->setIncludeGrantedScopes(true);
        $client->setAuthConfig(config_path() . '/googleCredentials.json');
        if ($service === 'analytics') {
            $client->addScope(Analytics::ANALYTICS);
        }

        if ($service === 'adwords') {
            $client->addScope(Localservices::ADWORDS);
        }

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $redirectUri = route('integrationsGoogleCallback');
        $client->setRedirectUri($redirectUri);
        $url = $client->createAuthUrl();
        return redirect()->to($url);
    }

    /**
     * @throws Exception
     */
    public function callback(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $code = $request->get('code');
        if (empty($code)) {
            abort(404);
        }

        $client = new Client();
        $client->setAuthConfig(config_path() . '/googleCredentials.json');

        try {
            $token = $client->fetchAccessTokenWithAuthCode($code);
        } catch (\Exception $exception) {
            Session::flash('success-message', 'Some error please contact us');
            return redirect('integrations');
        }

        if (isset($token['error'])) {
            // TODO add text
            Session::flash('success-message', 'Some error please contact us');
            return redirect('integrations');
        } else {
            $integration = Integration::updateOrCreate([
                'user_id' => $user->id,
                'app_user_id' => $client->getClientId(),
                'platform' => 'google'
            ], [
                'access_token' => $token['access_token'],
                'expires_in' => $token['expires_in'],
                'refresh_token' => $token['refresh_token'],
                'scope' => implode(',', explode(' ', $token['scope']))
            ]);

            $scope = explode(',', $integration['scope']);
            if (in_array(Analytics::ANALYTICS, $scope)) {
                // Save profiles
                $client->setAccessToken($integration->access_token);
                $analytics = new Analytics($client);
                Google::getProfileId($analytics, $integration->id);
            }
        }

        // TODO add text
        Session::flash('success-message', 'Connect shopify added/updated');
        return redirect('integrations');
    }
}
