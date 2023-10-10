<?php

return [
    'shopify' => [
        'apiKey' => env('SHOPIFY_API_KEY'),
        'apiSecret' => env('SHOPIFY_API_SECRET'),
        'appScopes' => explode(',', env('SHOPIFY_APP_SCOPES') ?? '')
    ],
    'telegram' => [
        'botName' => env('TELEGRAM_BOT_NAME'),
        'botToken' => env('TELEGRAM_BOT_TOKEN'),
    ],
    'whatsapp' => [
        'accessToken' => env('WHATSAPP_ACCESS_TOKEN'),
        'displayPhoneNumber' => env('WHATSAPP_DISPLAY_PHONE_NUMBER'),
        'phoneNumberId' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'businessAccountId' => env('WHATSAPP_BUSINESS_ACCOUNT_ID')
    ],
    'messenger' => [
        'pageAccessToken' => env('MESSENGER_PAGE_ACCESS_TOKEN'),
        'pageName' => env('MESSENGER_PAGE_NAME'),
        'pageId' => env('MESSENGER_PAGE_ID'),
    ],
    'openai' => [
        'apiKey' => env('OPENAI_API_KEY')
    ],
];
