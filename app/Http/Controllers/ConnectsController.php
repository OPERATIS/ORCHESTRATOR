<?php

namespace App\Http\Controllers;

use App\Models\Connect;
use App\Models\User;
use App\Services\Shopify;
use Google\Client;
use Google\Exception;
use Google\Service\Analytics;
use Google\Service\Localservices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Shopify\Auth\OAuth;
use Shopify\Auth\OAuthCookie;
use Shopify\Context;
use Shopify\Exception\CookieSetException;
use Shopify\Exception\PrivateAppException;
use Shopify\Exception\SessionStorageException;
use Shopify\Exception\UninitializedContextException;

class ConnectsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws CookieSetException
     * @throws PrivateAppException
     * @throws SessionStorageException
     * @throws UninitializedContextException
     */
    public function shopifyLogin(Request $request): RedirectResponse
    {
        $shop = $request->post('shop');
        if (empty($shop)) {
            abort(404);
        }

        Shopify::setContext($shop);
        $url = OAuth::begin($shop, route('shopifyCallback'), false,
            function (OAuthCookie $cookie) use ($shop) {
                Session::put($cookie->getValue(), $shop);
                Cookie::queue(Cookie::make($cookie->getName(), $cookie->getValue(), $cookie->getExpire()));
                return true;
            });

        // Fix redirect url (library problem)
        $url = urldecode($url);
        $url = str_replace('redirect_uri=' . Context::$HOST_SCHEME . '://' . Context::$HOST_NAME . '/', 'redirect_uri=', $url);
        return redirect()->to($url);
    }

    public function shopifyCallback(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $shopifySessionId = $request->cookies->get('shopify_session_id');
        if (!empty($shopifySessionId)) {
            $shop = Session::get($shopifySessionId);
        }

        if (empty($shop)) {
            return abort(404);
        }

        Shopify::setContext($shop);
        try {
            $session = OAuth::callback($request->cookies->all(), $request->request->all());
        } catch (\Exception $exception) {
            // TODO add text
            Session::flash('success-message', 'Some error please contact us');
            return redirect('dashboard');
        }

        Connect::updateOrCreate([
            'user_id' => $user->id,
            'app_user_slug' => $session->getShop(),
            'app_user_id' => $session->getId(),
            'platform' => 'shopify'
        ], [
            'access_token' => $session->getAccessToken(),
            'expires_in' => $session->getExpires(),
            'scope' => $session->getScope()
        ]);

        // TODO add text
        Session::flash('success-message', 'Connect shopify added/updated');
        return redirect('dashboard');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function googleLogin(Request $request): RedirectResponse
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

        $redirectUri = route('googleCallback');
        $client->setRedirectUri($redirectUri);
        $url = $client->createAuthUrl();
        return redirect()->to($url);
    }

    /**
     * @throws Exception
     */
    public function googleCallback(Request $request)
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
            return redirect('dashboard');
        }

        if (isset($token['error'])) {
            // TODO add text
            Session::flash('success-message', 'Some error please contact us');
            return redirect('dashboard');
        } else {
            Connect::updateOrCreate([
                'user_id' => $user->id,
                'app_user_id' => $client->getClientId(),
                'platform' => 'google'
            ], [
                'access_token' => $token['access_token'],
                'expires_in' => $token['expires_in'],
                'refresh_token' => $token['refresh_token'],
                'scope' => implode(',', explode(' ', $token['scope']))
            ]);
        }

        // TODO add text
        Session::flash('success-message', 'Connect shopify added/updated');
        return redirect('dashboard');
    }

    /**
     * @return mixed
     */
    public function facebookLogin()
    {
        return Socialite::driver('facebook')
            ->scopes(['ads_read'])
            ->redirect();
    }

    public function facebookCallback()
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $socialiteUser = Socialite::driver('facebook')
                ->user();
        } catch (\Exception $exception) {
            // TODO add text
            Session::flash('success-message', 'Some error please contact us');
            return redirect('dashboard');
        }

        Connect::updateOrCreate([
            'user_id' => $user->id,
            'app_user_slug' => $socialiteUser->getNickname() ?? null,
            'app_user_id' => $socialiteUser->getId(),
            'platform' => 'facebook'
        ], [
            'access_token' => $socialiteUser->token,
            'expires_in' => $socialiteUser->expiresIn,
            'scope' => implode(',', $socialiteUser->approvedScopes)
        ]);

        // TODO add text
        Session::flash('success-message', 'Connect shopify added/updated');
        return redirect('dashboard');
    }
}
