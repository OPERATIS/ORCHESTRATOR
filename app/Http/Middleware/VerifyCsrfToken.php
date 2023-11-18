<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/whatsapp',
        'webhooks/messenger',
        'webhooks/telegram',
        'webhooks/stripe',
        'integrations/shopify/customers-data-request',
        'integrations/shopify/customers-redact',
        'integrations/shopify/shop-redact'
    ];
}
