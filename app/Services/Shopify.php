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
}
