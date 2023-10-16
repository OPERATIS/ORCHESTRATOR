<?php

namespace App\Http\Controllers\Integrations;

use App\Models\Integration;
use App\Models\User;
use App\Services\Shopify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Shopify\Auth\OAuth;
use Shopify\Auth\OAuthCookie;
use Shopify\Context;
use Shopify\Exception\CookieSetException;
use Shopify\Exception\PrivateAppException;
use Shopify\Exception\SessionStorageException;
use Shopify\Exception\UninitializedContextException;

class ShopifyController extends BaseController
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
    public function login(Request $request): RedirectResponse
    {
        $shop = $request->post('shop');
        if (empty($shop)) {
            abort(404);
        }

        Shopify::setContext($shop);
        $url = OAuth::begin($shop, route('integrationsShopifyCallback'), false,
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

    public function callback(Request $request)
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
            return redirect('integrations');
        }

        Integration::updateOrCreate([
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
        return redirect('integrations');
    }
}
