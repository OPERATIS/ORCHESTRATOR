<?php

namespace App\Services;

use App\Models\Checkout;
use App\Models\CheckoutLineItem;
use App\Models\GaProfile;
use App\Models\Integration;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        DB::select("SELECT setval(pg_get_serial_sequence('users', 'id'), coalesce(max(id)+1, 1), false) FROM users;");
    }

    public static function createConnections()
    {
        // Create facebook connect
        Integration::withTrashed()
            ->updateOrCreate([
                'id' => FbStat::DEMO_INTEGRATION_ID,
                'user_id' => User::DEMO_ID,
                'platform' => 'facebook',
            ], [
                'actual' => true,
                'deleted_at' => null,
                'access_token' => uniqid()
            ]);


        // Create facebook connect
        Integration::withTrashed()
            ->updateOrCreate([
                'id' => FbStat::DEMO_INTEGRATION_ID,
                'user_id' => User::DEMO_ID,
                'platform' => 'facebook',
            ], [
                'actual' => true,
                'deleted_at' => null,
                'app_user_slug' => 'demo-facebook',
                'access_token' => uniqid()
            ]);


        // Create google connect
        Integration::withTrashed()->updateOrCreate([
            'id' => GaStat::DEMO_INTEGRATION_ID,
            'user_id' => User::DEMO_ID,
            'platform' => 'google',
        ], [
            'actual' => true,
            'deleted_at' => null,
            'app_user_slug' => 'demo-google',
            'access_token' => uniqid(),
            'scope' => 'https://www.googleapis.com/auth/analytics'
        ]);

        for ($i = 0; $i < 3; $i++) {
            GaProfile::updateOrCreate([
                'integration_id' => GaStat::DEMO_INTEGRATION_ID,
                'name' => 'demo-profile-' . $i
            ], [
                'actual' => true,
                'currency' => 'USD',
                'timezone' => 'UTC',
                'profile_id' => time() . $i
            ]);
        }

        // Create shopify connect
        Integration::withTrashed()->updateOrCreate([
            'id' => Order::DEMO_INTEGRATION_ID,
            'user_id' => User::DEMO_ID,
            'platform' => 'shopify',
        ], [
            'actual' => true,
            'deleted_at' => null,
            'app_user_slug' => 'demo-shopify.myshopify.com',
            'access_token' => uniqid()
        ]);

        DB::select("SELECT setval(pg_get_serial_sequence('integrations', 'id'), coalesce(max(id)+1, 1), false) FROM integrations;");
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

        $uniqueClicks = ($fbStat->unique_clicks ?? 0) + rand(0, 100);
        FbStat::create([
            'integration_id' => FbStat::DEMO_INTEGRATION_ID,
            'clicks' => ($fbStat->clicks ?? 0) + rand(0, 300) + $uniqueClicks,
            'impressions' => ($fbStat->impressions ?? 0) + rand(0, 500),
            'spend' => ($fbStat->spend ?? 0) + rand(0, 50),
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

        $uniquePageviews = ($gaStat->unique_pageviews ?? 0) + rand(0, 100);
        GaStat::create([
            'integration_id' => GaStat::DEMO_INTEGRATION_ID,
            'impressions' => ($gaStat->impressions ?? 0) + rand(0, 500),
            'pageviews' => ($gaStat->pageviews ?? 0) + rand(0, 300) + $uniquePageviews,
            'unique_pageviews' => $uniquePageviews,
            'ad_clicks' => ($gaStat->ad_clicks ?? 0) + rand(0, 300),
            'ad_cost' => ($gaStat->ad_cost ?? 0) + rand(0, 10),
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

        $count = rand(0, 25);
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
                'count_line_items' => rand(1, 10),
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
                    'gift_card' => rand(0, 1),
                    'title' => self::getProduct($productId),
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

        $count = rand(0, 50);
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
                'checkout_completed_at' => rand(0, 10) > 3 ? null : Carbon::parse($startPeriod)->addSeconds(rand(300, 900)),
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
                    'gift_card' => rand(0, 1),
                    'title' => self::getProduct($productId),
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return string
     */
    public static function getProduct($id): string
    {
        $products = [
            0 => 'Blackcurrant',
            1 => 'Apple',
            2 => 'Apricot',
            3 => 'Avocado',
            4 => 'Banana',
            5 => 'Blackberry',
            6 => 'Blueberry',
            7 => 'Boysenberry',
            8 => 'Breadfruit',
            9 => 'Cactus fruit',
            10 => 'Cantaloupe',
            11 => 'Cherry',
            12 => 'Clementine',
            13 => 'Coconut',
            14 => 'Cranberry',
            15 => 'Currant',
            16 => 'Date',
            17 => 'Dragonfruit',
            18 => 'Durian',
            19 => 'Elderberry',
            20 => 'Fig',
            21 => 'Grape',
            22 => 'Grapefruit',
            23 => 'Ground cherry',
            24 => 'Guava',
            25 => 'Honeydew melon',
            26 => 'Jackfruit',
            27 => 'Kiwi',
            28 => 'Kumquat',
            29 => 'Lemon',
            30 => 'Lime',
            31 => 'Lychee',
            32 => 'Mango',
            33 => 'Mandarin',
            34 => 'Maracuja',
            35 => 'Mulberry',
            36 => 'Nectarine',
            37 => 'Orange',
            38 => 'Papaya',
            39 => 'Passionfruit',
            40 => 'Peach',
            41 => 'Pear',
            42 => 'Persimmon',
            43 => 'Pineapple',
            44 => 'Plum',
            45 => 'Pomegranate',
            46 => 'Raspberry',
            47 => 'Red currant',
            48 => 'Star fruit',
            49 => 'Strawberry',
            50 => 'Tangerine',
            51 => 'Watermelon',
            52 => 'Artichoke',
            53 => 'Asparagus',
            54 => 'Aubergine',
            55 => 'Beetroot',
            56 => 'Bell pepper',
            57 => 'Broccoli',
            58 => 'Brussels sprout',
            59 => 'Cabbage',
            60 => 'Carrot',
            61 => 'Cauliflower',
            62 => 'Celery',
            63 => 'Chard',
            64 => 'Chicory',
            65 => 'Cucumber',
            66 => 'Daikon radish',
            67 => 'Eggplant',
            68 => 'Endive',
            69 => 'Fennel',
            70 => 'Garlic',
            71 => 'Ginger',
            72 => 'Green bean',
            73 => 'Kale',
            74 => 'Leek',
            75 => 'Lettuce',
            76 => 'Mushroom',
            77 => 'Okra',
            78 => 'Onion',
            79 => 'Parsnip',
            80 => 'Pea',
            81 => 'Pepper',
            82 => 'Potato',
            83 => 'Pumpkin',
            84 => 'Radish',
            85 => 'Red cabbage',
            86 => 'Rhubarb',
            87 => 'Spinach',
            88 => 'Sweet corn',
            89 => 'Sweet potato',
            90 => 'Tomato',
            91 => 'Turnip',
            92 => 'Zucchini',
            93 => 'Corn',
            94 => 'White radish',
            95 => 'Green onion',
            96 => 'Kohlrabi',
            97 => 'Arugula',
            98 => 'Jalapeno pepper',
            99 => 'Green chili',
            100 => 'Swiss chard',
        ];

        return $products[$id];
    }
}
