<?php

namespace App\Services;

use Shopify\Auth\FileSessionStorage;
use Shopify\Context;

class Shopify
{
    public static function setContext($shop)
    {
        Context::initialize(
            config('integrations.shopify.apiKey'),
            config('integrations.shopify.apiSecret'),
            config('integrations.shopify.appScopes'),
            $shop,
            new FileSessionStorage(),
            '2023-04',
            true,
            false,
        );
    }

    public static function verifyWebhooks(): bool
    {
        $apiSecret = config('integrations.shopify.apiSecret');
        $hmacHeader = request()->server('HTTP_X_SHOPIFY_HMAC_SHA256');
        $data = file_get_contents('php://input');

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, $apiSecret, true));
        return hash_equals($calculatedHmac, $hmacHeader);
    }

    public static function verify($request): bool
    {
        $parameters = $request->request->all();
        $hmacRequest = $parameters['hmac'];
        unset($parameters['hmac']);
        $data = http_build_query($parameters);

        $apiSecret = config('integrations.shopify.apiSecret');
        $calculatedHmac = (hash_hmac('sha256', $data, $apiSecret));
        return (hash_equals($calculatedHmac, $hmacRequest));
    }
}
