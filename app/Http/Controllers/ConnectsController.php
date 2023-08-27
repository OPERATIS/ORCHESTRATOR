<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Exception;
use Google\Service\Analytics;
use Google\Service\Localservices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\OAuth;
use Shopify\Auth\OAuthCookie;
use Shopify\Context;
use Shopify\Exception\CookieSetException;
use Shopify\Exception\HttpRequestException;
use Shopify\Exception\InvalidOAuthException;
use Shopify\Exception\MissingArgumentException;
use Shopify\Exception\OAuthCookieNotFoundException;
use Shopify\Exception\OAuthSessionNotFoundException;
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
     * @throws UninitializedContextException|MissingArgumentException
     */
    public function shopifyLogin(Request $request): RedirectResponse
    {
        $shop = $request->get('shop');
        // TODO remove after testing
        $shop = 'quickstart-2bd07cf2.myshopify.com';
        if (empty($shop)) {
            abort(404);
        }

        $this->shopifySetContext($shop);

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

    /**
     * @param Request $request
     * @throws PrivateAppException
     * @throws SessionStorageException
     * @throws UninitializedContextException
     * @throws HttpRequestException
     * @throws InvalidOAuthException
     * @throws OAuthCookieNotFoundException
     * @throws OAuthSessionNotFoundException|MissingArgumentException
     */
    public function shopifyCallback(Request $request)
    {
        $shopifySessionId = $request->cookies->get('shopify_session_id');
        if (!empty($shopifySessionId)) {
            $shop = Session::get($shopifySessionId);
        }

        if (empty($shop)) {
            abort(404);
        }

        $this->shopifySetContext($shop);
        $session = OAuth::callback($request->cookies->all(), $request->request->all());
        // TODO save in database
        // $session->getScope()
        // $session->getExpires()
        // $session->getAccessToken()
    }

    /**
     * @param $shop
     * @throws MissingArgumentException
     */
    protected function shopifySetContext($shop)
    {
        Context::initialize(
            config('connects.shopify.apiKey'),
            config('connects.shopify.apiSecret'),
            config('connects.shopify.appScopes'),
            $shop,
            new FileSessionStorage(),
            '2023-04',
            true,
            false,
        );
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
     * @param Request $request
     * @throws Exception
     */
    public function googleCallback(Request $request)
    {
        $code = $request->get('code');
        if (empty($code)) {
            abort(404);
        }

        $client = new Client();
        $client->setAuthConfig(config_path() . '/googleCredentials.json');

        $token = $client->fetchAccessTokenWithAuthCode($code);
        // TODO save in database
        //  "access_token"
        //  "expires_in" => 3599
        //  "refresh_token"
        //  "scope" => "https://www.googleapis.com/auth/analytics https://www.googleapis.com/auth/adwords"
        //  "token_type" => "Bearer"
        //  "created" => 1693130377
    }
}
