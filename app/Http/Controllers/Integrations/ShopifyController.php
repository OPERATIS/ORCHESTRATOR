<?php

namespace App\Http\Controllers\Integrations;

use App\Models\Integration;
use App\Models\User;
use App\Services\Shopify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
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
     * @return RedirectResponse|void
     * @throws CookieSetException
     * @throws PrivateAppException
     * @throws SessionStorageException
     * @throws UninitializedContextException
     */
    public function guest(Request $request)
    {
        if (Shopify::verify($request)) {
            $shop = $request->get('shop');

            // Search shop in records
            $integration = Integration::where('app_user_slug', $shop)
                ->whereHas('user')
                ->where('platform', 'shopify')
                ->first();

            // Check correct record
            if ($integration && !empty($integration->access_token) && $integration->scope !== config('integrations.shopify.appScopes')) {
                Auth::login($integration->user);
                return redirect()->route('dashboard');
            } else {
                return $this->baseLogin($shop);
            }
        } else {
            abort(403);
        }
    }

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

        return $this->baseLogin($shop);
    }

    /**
     * @param string $shop
     * @return RedirectResponse
     * @throws CookieSetException
     * @throws PrivateAppException
     * @throws SessionStorageException
     * @throws UninitializedContextException
     */
    protected function baseLogin(string $shop): RedirectResponse
    {
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
            return abort(500);
        }

        $data = [
            'app_user_slug' => $session->getShop(),
            'app_user_id' => $session->getId(),
            'platform' => 'shopify'
        ];

        if ($user) {
            $data['user_id'] = $user->id;
        } else {
            // Search shop in records
            $integration = Integration::where('app_user_slug', $shop)
                ->whereHas('user')
                ->where('platform', 'shopify')
                ->first();

            if ($integration) {
                $user = $integration->user;
            } else {
                $user = User::create([
                    'email' => $shop,
                    'password' => Hash::make(str_replace('.myshopify.com', '', $shop)),
                    'brand_name' => str_replace('.myshopify.com', '', $shop)
                ]);
            }
            $data['user_id'] = $user->id;
            Auth::login($user);
        }

        Integration::updateOrCreate($data, [
            'actual' => true,
            'deleted_at' => null,
            'access_token' => $session->getAccessToken(),
            'expires_in' => $session->getExpires(),
            'scope' => $session->getScope()
        ]);

        return redirect(route('integrations', ['platform' => 'shopify']));
    }

    /**
     * @return JsonResponse
     */
    public function customersDataRequest(): JsonResponse
    {
        $status = Shopify::verifyWebhooks() ? 200 : 403;
        return response()->json([
            'status' => true,
        ], $status);
    }

    /**
     * @return JsonResponse
     */
    public function customersRedact(): JsonResponse
    {
        $status = Shopify::verifyWebhooks() ? 200 : 403;
        return response()->json([
            'status' => true,
        ], $status);
    }

    /**
     * @return JsonResponse
     */
    public function shopRedact(): JsonResponse
    {
        $status = Shopify::verifyWebhooks() ? 200 : 403;
        return response()->json([
            'status' => true,
        ], $status);
    }
}
