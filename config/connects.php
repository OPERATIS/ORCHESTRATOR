<?php

return [
    'shopify' => [
        'apiKey' => env('SHOPIFY_API_KEY'),
        'apiSecret' => env('SHOPIFY_API_SECRET'),
        'appScopes' => explode(',', env('SHOPIFY_APP_SCOPES') ?? '')
    ]
];
