<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class Warnings
{
    public $loadIntegrationShopify = false;
    public $lastIntegrationShopify = null;

    public function getLastIntegrationShopify(User $user)
    {
        if (!$this->loadIntegrationShopify) {
            $this->lastIntegrationShopify = Integration::where('user_id', $user->id)
                ->where('platform', 'shopify')
                ->orderBy('created_at')
                ->first();

            $this->loadIntegrationShopify = true;
        }

        return $this->lastIntegrationShopify;
    }

    public function getIntegrationShopifyCreatedAt(User $user)
    {
        $integration = $this->getLastIntegrationShopify($user);
        $createdAt = null;
        if ($integration) {
            $createdAt = $integration->created_at;
        }

        return $createdAt;
    }

    public function getCountShopifyRecords(User $user)
    {
        $integration = $this->getLastIntegrationShopify($user);
        if ($integration) {
            $orderCount = Order::where('integration_id', $integration->id)
                ->count();
        }

        return $orderCount ?? 0;
    }

    public function getStatusShopifyIntegratedLess24Hours(User $user): bool
    {
        return Carbon::parse($this->getIntegrationShopifyCreatedAt($user)) < Carbon::now()->subHours(24);
    }

    public function getStatusShopifyHasLittleInformation(User $user): bool
    {
        return $this->getCountShopifyRecords($user) > 12;
    }
}
