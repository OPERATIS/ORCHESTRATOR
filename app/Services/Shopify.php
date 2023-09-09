<?php

namespace App\Services;

use Shopify\Auth\FileSessionStorage;
use Shopify\Context;

class Shopify
{
    public static function setContext($shop)
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
}
