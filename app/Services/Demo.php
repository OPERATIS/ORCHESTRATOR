<?php

namespace App\Services;

use App\Models\Checkout;
use App\Models\CheckoutLineItem;
use App\Models\Integration;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class Demo
{
    public static function createUser()
    {
        // Create or update demo user
        User::updateOrCreate([
            'id' => User::DEMO_ID,
            'email' => 'demo@demo.com',
        ], [
            'name' => 'Demo',
            'brand_name' => 'Demo',
            'password' => Hash::make('demo-password')
        ]);
    }

    public static function createConnections()
    {
        // Create facebook connect
        Integration::updateOrCreate([
            'id' => FbStat::DEMO_INTEGRATION_ID,
            'user_id' => User::DEMO_ID,
            'platform' => 'facebook',
        ], [
            'access_token' => uniqid()
        ]);

        // Create google connect
        Integration::updateOrCreate([
            'id' => GaStat::DEMO_INTEGRATION_ID,
            'user_id' => User::DEMO_ID,
            'platform' => 'google',
        ], [
            'access_token' => uniqid()
        ]);

        // Create shopify connect
        Integration::updateOrCreate([
            'id' => Order::DEMO_INTEGRATION_ID,
            'user_id' => User::DEMO_ID,
            'platform' => 'shopify',
        ], [
            'access_token' => uniqid()
        ]);
    }

    public static function createFbStats($startPeriod, $endPeriod)
    {
        FbStat::where('integration_id', FbStat::DEMO_INTEGRATION_ID)
            ->where('ad_id', FbStat::DEMO_AD_ID)
            ->where('end_period', $endPeriod)
            ->delete();

        $fbStat = FbStat::where('integration_id', FbStat::DEMO_INTEGRATION_ID)
            ->where('ad_id', FbStat::DEMO_AD_ID)
            ->orderByDesc('end_period')
            ->first();

        // If next day
        if (Carbon::parse($endPeriod)->hour === 0 && Carbon::parse($endPeriod)->minute == 5) {
            $fbStat = null;
        }

        $uniqueClicks = ($fbStat->unique_clicks ?? 0) + rand(0, 1000);
        FbStat::create([
            'integration_id' => FbStat::DEMO_INTEGRATION_ID,
            'clicks' => ($fbStat->clicks ?? 0) + rand(0, 3000) + $uniqueClicks,
            'impressions' => ($fbStat->impressions ?? 0) + rand(0, 5000),
            'spend' => ($fbStat->spend ?? 0) + rand(0, 500),
            'unique_clicks' => $uniqueClicks,
            'ad_id' => FbStat::DEMO_AD_ID,
            'start_period' => $startPeriod,
            'end_period' => $endPeriod,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public static function createGaStats($startPeriod, $endPeriod)
    {
        GaStat::where('integration_id', GaStat::DEMO_INTEGRATION_ID)
            ->where('unique_table_id', GaStat::DEMO_UNIQUE_TABLE_ID)
            ->where('end_period', $endPeriod)
            ->delete();

        $gaStat = GaStat::where('integration_id', GaStat::DEMO_INTEGRATION_ID)
            ->where('unique_table_id', GaStat::DEMO_UNIQUE_TABLE_ID)
            ->orderByDesc('end_period')
            ->first();

        // If next day
        if (Carbon::parse($endPeriod)->hour === 0 && Carbon::parse($endPeriod)->minute == 5) {
            $gaStat = null;
        }

        $uniquePageviews = ($gaStat->unique_pageviews ?? 0) + rand(0, 1000);
        GaStat::create([
            'integration_id' => GaStat::DEMO_INTEGRATION_ID,
            'impressions' => ($gaStat->impressions ?? 0) + rand(0, 5000),
            'pageviews' => ($gaStat->pageviews ?? 0) + rand(0, 3000) + $uniquePageviews,
            'unique_pageviews' => $uniquePageviews,
            'ad_clicks' => ($gaStat->ad_clicks ?? 0) + rand(0, 3000),
            'ad_cost' => ($gaStat->ad_cost ?? 0) + rand(0, 100),
            'unique_table_id' => GaStat::DEMO_UNIQUE_TABLE_ID,
            'start_period' => $startPeriod,
            'end_period' => $endPeriod,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public static function createOrders($startPeriod, $endPeriod)
    {
        // Delete order line items
        OrderLineItem::where('integration_id', Order::DEMO_INTEGRATION_ID)
            ->whereIn('order_id', Order::select('id')
                ->where('integration_id', Order::DEMO_INTEGRATION_ID)
                ->where('order_created_at', '>=', $startPeriod)
                ->where('order_created_at', '<=', $endPeriod)
                ->get()
                ->toArray()
            )
            ->delete();

        // Delete orders
        Order::where('integration_id', Order::DEMO_INTEGRATION_ID)
            ->where('order_created_at', '>=', $startPeriod)
            ->where('order_created_at', '<=', $endPeriod)
            ->delete();

        $count = rand(0, 100);
        for ($i = 0; $i < $count; $i++) {
            $orderId = time() + $i;
            $price = rand(100, 500);

            $discountCodes = [];
            if (rand(0, 10000) < 1000) {
                $discountCodes[] = json_encode(['code' => 'code']);
            }

            // Payment
            $paymentGatewayNames = [];
            if (rand(0, 100) < 10) {
                $paymentGatewayNames[] = 'cash';
            } elseif (rand(0, 100) < 10) {
                $paymentGatewayNames[] = 'card';
            } else {
                $paymentGatewayNames[] = 'paypal';
            }

            $localOrder = Order::create([
                'integration_id' => Order::DEMO_INTEGRATION_ID,
                'order_id' => $orderId,
                'order_created_at' => Carbon::parse($startPeriod)->addSeconds(rand(0, 300)),
                'financial_status' => rand(0, 100000) < 100 ? 'refunded' : 'paid',
                'order_number' => $orderId,
                'total_price' => $price,
                'customer_id' => rand(0, 100000),
                'created_at' => $endPeriod,
                'updated_at' => $endPeriod,
                'total_line_items_price' => $price - rand(5, 100),
                'count_line_items' => rand(1, 100),
                'count_refund_line_items' => rand(0, 10),
                'total_refund_line_items_price' => $price * ((rand(0, 100) / 100) / 100),
                'reference' => null,
                'referring_site' => null,
                'ads' => rand(0, 1),
                'canceled_at' => rand(0, 1) ? Carbon::parse($startPeriod)->addSeconds(rand(0, 300)) : null,
                'total_discounts' => rand(0, 100) < 10 ? 1 : 0,
                'discount_codes' => $discountCodes,
                'payment_gateway_names' => $paymentGatewayNames,
            ]);

            $countLineItem = rand(0, $localOrder->count_line_items);
            for ($j = 0; $j < $countLineItem; $j++) {
                $productId = rand(0, 100);
                $price = rand(1, 100);
                OrderLineItem::create([
                    'integration_id' => Order::DEMO_INTEGRATION_ID,
                    'order_id' => $localOrder->id,
                    'order_line_item_id' => uniqid(),
                    'product_id' => $productId,
                    'variant_id' => $productId,
                    'price' => $price,
                    'quantity' => rand(1, 10),
                    'gift_card' => rand(0, 1)
                ]);
            }
        }

        self::createCheckouts($startPeriod, $endPeriod);
    }

    public static function createCheckouts($startPeriod, $endPeriod)
    {
        // Delete checkout line items
        CheckoutLineItem::where('integration_id', Order::DEMO_INTEGRATION_ID)
            ->whereIn('checkout_id', Checkout::select('id')
                ->where('integration_id', Order::DEMO_INTEGRATION_ID)
                ->where('checkout_created_at', '>=', $startPeriod)
                ->where('checkout_created_at', '<=', $endPeriod)
                ->get()
                ->toArray()
            )->delete();

        // Delete checkouts
        Checkout::where('integration_id', Order::DEMO_INTEGRATION_ID)
            ->where('checkout_created_at', '>=', $startPeriod)
            ->where('checkout_created_at', '<=', $endPeriod)
            ->delete();

        $count = rand(0, 300);
        for ($i = 0; $i < $count; $i++) {
            $price = rand(100, 500);

            $giftCards = [];
            if (rand(0, 1000) < 100) {
                $giftCards[] = json_encode(['code' => 'code']);
            }

            $localCheckout = Checkout::create([
                'integration_id' => Order::DEMO_INTEGRATION_ID,
                'token' => uniqid(),
                'order_id' => $checkout->order_id ?? null,
                'checkout_created_at' => Carbon::parse($startPeriod)->addSeconds(rand(0, 300)),
                'checkout_completed_at' =>  rand(0, 10) > 3 ? null : Carbon::parse($startPeriod)->addSeconds(rand(300, 900)),
                'customer_id' => rand(0, 10) > 3 ? rand(0, 100000) : null,
                'total_price' => $price,
                'gift_cards' => $giftCards
            ]);

            $countLineItem = rand(0, 5);
            for ($j = 0; $j < $countLineItem; $j++) {
                $productId = rand(0, 100);
                $price = rand(1, 100);
                CheckoutLineItem::create([
                    'integration_id' => Order::DEMO_INTEGRATION_ID,
                    'checkout_id' => $localCheckout->id,
                    'checkout_line_item_id' => uniqid(),
                    'product_id' => $productId,
                    'variant_id' => $productId,
                    'price' => $price,
                    'line_price' => $price,
                    'quantity' => rand(1, 10),
                    'gift_card' => rand(0, 1)
                ]);
            }
        }
    }
}
