<?php

namespace App\Console\Commands;

use App\Models\Analysis;
use App\Models\Checkout;
use App\Models\CheckoutLineItem;
use App\Models\Integration;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Metric;
use App\Models\Order;
use App\Models\OrderLineItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SaveMetrics extends Command
{
    protected $signature = 'save-metrics {period} {endPeriod?} {type?}';

    public function handle(): bool
    {
        $period = $this->argument('period');

        if (!in_array($period, [Metric::PERIOD_HOUR, Metric::PERIOD_DAY])) {
            return true;
        }

        // Logic for old data
        $endPeriod = $this->argument('endPeriod');

        // Search only demo
        $type = $this->argument('type');

        if (!$endPeriod) {
            $startPeriod = Carbon::now();
        } else {
            $startPeriod = Carbon::parse($endPeriod);
        }

        if ($period === Metric::PERIOD_HOUR) {
            $startPeriod = $startPeriod->subHour()->setMinutes(0)->setSeconds(0)->toDateTimeString();
        } elseif ($period === Metric::PERIOD_DAY) {
            $startPeriod = $startPeriod->subDay()->startOfDay()->toDateTimeString();
        }

        if ($period === Metric::PERIOD_HOUR) {
            $endPeriod = Carbon::parse($startPeriod)->addHours()->toDateTimeString();
        } elseif ($period === Metric::PERIOD_DAY) {
            $endPeriod = Carbon::parse($startPeriod)->addDays()->toDateTimeString();
        }

        if (!$type) {
            $integrations = Integration::get();
        } else {
            $integrations = Integration::whereIn('id', [
                FbStat::DEMO_INTEGRATION_ID,
                GaStat::DEMO_INTEGRATION_ID,
                Order::DEMO_INTEGRATION_ID
            ])->get();
        }

        $integrationIds = [];
        $preparedMetrics = [];
        foreach ($integrations as $integration) {
            $integrationIds[$integration->id] = $integration->user_id;
            if (!isset($preparedMetrics[$integration->user_id])) {
                $preparedMetrics[$integration->user_id] = [
                    'user_id' => $integration->user_id,
                    'period' => $period,
                    'start_period' => $startPeriod,
                    'end_period' => $endPeriod,
                    'reach' => 0,
                    'l' => 0,
                    'p' => 0,
                    'pu' => 0,
                    'd' => 0,
                    'q1' => 0,
                    'cls' => 0,
                    'returns' => 0,
                    'q' => 0,
                    'count_customers' => 0,
                    'ads_cls' => 0,
                    'ltv' => 0,
                    'r' => 0,
                    'c1' => 0,
                    'c' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'l_map' => [],
                    'l_cpd' => 0,
                    'l_ccr' => 0,
                    'car' => 0,
                    'c_car' => 0,
                    'c_grccr' => 0,
                    'p_aov' => 0,
                    'p_dur' => 0,
                    'p_mppr' => 0,
                    'q_rr' => 0,
                    'car_ct' => 0,
                    'p_ccur' => 0,
                    'c_pmd' => [],
                    'p_cv' => 0,
                    'q_gcur' => 0,
                    'car_ttv' => 0,
                    'car_pmu' => [],
                    'car_fpr' => 0
                ];
            }
        }

        // Facebook
        $fbMetrics = FbStat::selectRaw("
            sum(impressions) as REACH,
            integration_id
        ")
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', FbStat::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($fbMetrics as $fbMetric) {
            $userId = $integrationIds[$fbMetric->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['reach'] = $fbMetric->reach;
            }
        }

        // Google
        $gaMetrics = GaStat::selectRaw("
            sum(pageviews) as REACH,
            integration_id
        ")
            ->where('end_period', '>', $startPeriod)
            ->where('end_period', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', GAStat::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($gaMetrics as $gaMetric) {
            $userId = $integrationIds[$gaMetric->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['reach'] += $gaMetric->reach;
            }
        }

        // Order
        $orderMetrics = Order::selectRaw("
            sum(total_line_items_price) as totalLineItemsPrice,
            sum(total_price) as totalPrice,
            sum(total_discounts) as totalDiscounts,
            count(CASE WHEN total_discounts > 0 THEN 1 END) as countOrdersWithDiscounts,
            count(CASE WHEN count_line_items > 1 THEN 1 END) as countOrdersWithMoreThanOneProduct,
            count(CASE WHEN JSONB_ARRAY_LENGTH(discount_codes) > 0 THEN 1 END) as countOrdersWithDiscountCodes,
            sum(count_line_items) as sumD,
            count(*) as countOrders,
            count(DISTINCT(customer_id)) + count(DISTINCT CASE WHEN customer_id IS NULL THEN 1 END) as countCustomers,
            jsonb_agg(payment_gateway_names) as paymentGatewayNames,
            count(DISTINCT CASE WHEN ads THEN customer_id END) as AdsCLs,
            integration_id
        ")
            ->where('order_created_at', '>', $startPeriod)
            ->where('order_created_at', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        $ordersPaid = Order::selectRaw("
            count(*) as countOrders,
            sum(total_price) as totalPrice,
            integration_id
        ")
            ->where('order_created_at', '>', $startPeriod)
            ->where('order_created_at', '<=', $endPeriod)
            ->where('financial_status', 'paid')
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        $checkouts = Checkout::selectRaw("
            count(*) as countCheckouts,
            count(CASE WHEN customer_id IS NULL THEN 1 END) as guest,
            count(CASE WHEN customer_id IS NOT NULL THEN 1 END) as registered,
            avg(CASE WHEN checkout_created_at IS NOT NULL AND checkout_completed_at IS NOT NULL THEN EXTRACT(SECONDS FROM checkout_completed_at - checkout_created_at) END) as checkoutTime,
            count(CASE WHEN JSONB_ARRAY_LENGTH(gift_cards) > 0 THEN 1 END) as countCheckoutsWithGiftCards,
            integration_id
        ")
            ->where('checkout_created_at', '>', $startPeriod)
            ->where('checkout_created_at', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($orderMetrics as $orderMetric) {
            $userId = $integrationIds[$orderMetric->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['p'] = $orderMetric->totallineitemsprice / $orderMetric->countorders;
                $preparedMetrics[$userId]['pu'] = $orderMetric->sumd ? ($orderMetric->totallineitemsprice / $orderMetric->sumd) : 0;
                $preparedMetrics[$userId]['d'] = $orderMetric->countorders ? ($orderMetric->sumd / $orderMetric->countorders) : 0;
                $preparedMetrics[$userId]['q1'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
                $preparedMetrics[$userId]['count_customers'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['cls'] = $orderMetric->countcustomers;
                $preparedMetrics[$userId]['ads_cls'] = $orderMetric->adscls;
                $preparedMetrics[$userId]['returns'] = 0;

                // New
                $checkout = $checkouts->where('integration_id', $orderMetric->integration_id)->first();
                $orderPaid = $ordersPaid->where('integration_id', $orderMetric->integration_id)->first();
                if ($checkout) {
                    $preparedMetrics[$userId]['l'] = $checkout->countcheckouts;
                }

                $preparedMetrics[$userId]['c1'] = $preparedMetrics[$userId]['reach'] ? $preparedMetrics[$userId]['l'] / $preparedMetrics[$userId]['reach'] : 0;
                $preparedMetrics[$userId]['c'] = $preparedMetrics[$userId]['l'] ? ($orderPaid->countorders ?? 0) / $preparedMetrics[$userId]['l'] : 0;
                // Recalculate
                $preparedMetrics[$userId]['q'] = $orderMetric->countcustomers ? ($orderMetric->countorders / $orderMetric->countcustomers) : 0;
                $preparedMetrics[$userId]['ltv'] = $preparedMetrics[$userId]['p'] * $preparedMetrics[$userId]['q'];
                $preparedMetrics[$userId]['r'] = $preparedMetrics[$userId]['cls'] * $preparedMetrics[$userId]['ltv'];
                // New
                $preparedMetrics[$userId]['l_cpd'] = $orderMetric->countorders ? ($orderMetric->sumd / $orderMetric->countorders) : 0;
                if ($checkout && $orderPaid) {
                    $preparedMetrics[$userId]['l_ccr'] = $checkout->countcheckouts ? ($orderPaid->countorders / $checkout->countcheckouts) : 0;
                    $preparedMetrics[$userId]['car'] = 1 - ($checkout->countcheckouts ? ($orderPaid->countorders / $checkout->countcheckouts) : 0);
                    $preparedMetrics[$userId]['c_car'] = 1 - ($checkout->countcheckouts ? ($orderPaid->countorders / $checkout->countcheckouts) : 0);
                }

                if ($checkout) {
                    $preparedMetrics[$userId]['c_grccr'] = $checkout->registered ? $checkout->guest / $checkout->registered : 0;
                    $preparedMetrics[$userId]['car_ct'] = $checkout->checkouttime;
                    $preparedMetrics[$userId]['q_gcur'] = $checkout->countcheckouts ? $checkout->countcheckoutswithgiftcards / $checkout->countcheckouts : 0;
                }

                $preparedMetrics[$userId]['p_aov'] = $orderMetric->countorders ? $orderMetric->totalprice / $orderMetric->countorders : 0;
                $preparedMetrics[$userId]['p_dur'] = $orderMetric->countorders ? $orderMetric->countorderswithdiscounts / $orderMetric->countorders : 0;
                $preparedMetrics[$userId]['p_mppr'] = $orderMetric->countorders ? $orderMetric->countorderswithmorethanoneproduct / $orderMetric->countorders : 0;
                $preparedMetrics[$userId]['p_ccur'] = $orderMetric->countorders ? $orderMetric->countorderswithdiscountcodes / $orderMetric->countorders : 0;

                $paymentGatewayNames = json_decode($orderMetric->paymentgatewaynames);
                $prepared = [];
                if (is_array($paymentGatewayNames)) {
                    foreach ($paymentGatewayNames as $paymentGatewayName) {
                        if (is_array($paymentGatewayName)) {
                            foreach ($paymentGatewayName as $item) {
                                $prepared[$item] = ($prepared[$item] ?? 0) + 1;
                            }
                        }
                    }
                }
                $preparedMetrics[$userId]['c_pmd'] = $prepared;
                $preparedMetrics[$userId]['car_pmu'] = $prepared;

                if ($orderPaid) {
                    $preparedMetrics[$userId]['car_ttv'] = $orderPaid->totalprice;
                }
            }
        }

        $orderMetricsReturns = Order::selectRaw("
            count(*) as countOrders,
            integration_id
        ")
            ->where('financial_status', 'refunded')
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->groupBy(['integration_id'])
            ->get();

        foreach ($orderMetricsReturns as $orderMetricReturn) {
            $userId = $integrationIds[$orderMetricReturn->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['returns'] = $preparedMetrics[$userId]['count_customers'] ? ($orderMetricReturn->countorders / $preparedMetrics[$userId]['count_customers']) : 0;
                // Recalculate if found returns
                $preparedMetrics[$userId]['q'] = $preparedMetrics[$userId]['q1'] - $preparedMetrics[$userId]['returns'];
                $preparedMetrics[$userId]['ltv'] = $preparedMetrics[$userId]['p'] * $preparedMetrics[$userId]['q'];
                $preparedMetrics[$userId]['r'] = $preparedMetrics[$userId]['cls'] * $preparedMetrics[$userId]['ltv'];
                // New
                $orderPaid = $ordersPaid->where('integration_id', $orderMetricReturn->integration_id)->first();
                if ($orderPaid) {
                    $preparedMetrics[$userId]['q_rr'] = $orderPaid->countorders > 0 ? $orderMetricReturn->countorders / $orderPaid->countorders : 0;
                }
            }
        }

        // Order and line items
        $orderIds = Order::query()
            ->where('order_created_at', '>', $startPeriod)
            ->where('order_created_at', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->pluck('id')
            ->toArray();

        $orderLineItems = OrderLineItem::selectRaw("
            product_id,
            title,
            count(*) as countProducts,
            integration_id
        ")
            ->whereIn('order_id', $orderIds)
            ->groupBy(['product_id', 'integration_id', 'title'])
            ->get();

        // Checkout and line items
        $checkoutIds = Checkout::query()
            ->where('checkout_created_at', '>', $startPeriod)
            ->where('checkout_created_at', '<=', $endPeriod)
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->pluck('id')
            ->toArray();

        $checkoutLineItems = CheckoutLineItem::selectRaw("
            product_id,
            title,
            count(*) as countProducts,
            integration_id
        ")
            ->whereIn('checkout_id', $checkoutIds)
            ->groupBy(['product_id', 'integration_id', 'title'])
            ->get();

        $collectOrderLineItems = collect($orderLineItems);

        $products = [];
        $productsName = [];
        foreach ($checkoutLineItems as $checkoutLineItem) {
            $userId = $integrationIds[$checkoutLineItem->integration_id] ?? null;
            if ($userId) {
                $trySearch = $collectOrderLineItems->where('product_id', $checkoutLineItem->product_id)
                    ->where('integration_id', $checkoutLineItem->integration_id)
                    ->first();

                if (!$trySearch || ($trySearch->countproducts !== $checkoutLineItem->countproducts)) {
                    $products[$userId][$checkoutLineItem->product_id] = $checkoutLineItem->countproducts - ($trySearch->countproducts ?? 0);
                    $products[$userId][$checkoutLineItem->product_id] = $products[$userId][$checkoutLineItem->product_id] > 0 ? $products[$userId][$checkoutLineItem->product_id] : 0;
                    $productsName[$userId][$checkoutLineItem->product_id] = $checkoutLineItem->title ?? $trySearch->title ?? 'Empty';
                }
            }
        }

        foreach ($products as $userId => $productsUser) {
            // Problem products
            if (count($productsUser)) {
                arsort($productsUser);
                $prepared = [];
                $count = 0;
                foreach ($productsUser as $productId => $value) {
                    if ($count < 5) {
                        $prepared[] = $productsName[$userId][$productId] . '#' . $productId;
                    }
                    $count++;
                }
                $preparedMetrics[$userId]['l_map'] = $prepared;
            }
        }

        // Checkout and line items
        $checkoutIds = Checkout::query()
            ->where('checkout_created_at', '>', $startPeriod)
            ->where('checkout_created_at', '<=', $endPeriod)
            ->whereNull('checkout_completed_at')
            ->when($type === 'demo', function ($query) {
                return $query->where('integration_id', Order::DEMO_INTEGRATION_ID);
            })
            ->pluck('id')
            ->toArray();

        $checkoutLineItems = CheckoutLineItem::selectRaw("
            sum(line_price) as linePrice,
            count(DISTINCT(checkout_id)) as countCheckouts,
            integration_id
        ")
            ->whereIn('checkout_id', $checkoutIds)
            ->groupBy(['integration_id'])
            ->get();

        foreach ($checkoutLineItems as $checkoutLineItem) {
            $userId = $integrationIds[$checkoutLineItem->integration_id] ?? null;
            if ($userId) {
                $preparedMetrics[$userId]['p_cv'] = $checkoutLineItem->countcheckouts > 0 ? $checkoutLineItem->lineprice / $checkoutLineItem->countcheckouts : 0;
            }
        }

        foreach ($preparedMetrics as $preparedMetric) {
            Metric::create($preparedMetric);
        }

        // Search alerts
        if ($period === Metric::PERIOD_HOUR) {
            Artisan::call("search-alerts '{$endPeriod}' {$type}");
            $analysisPeriod = Analysis::PERIOD_60_HOURS;
        } elseif ($period === Metric::PERIOD_DAY) {
            Artisan::call("search-recommendations '{$endPeriod}' {$type}");
            $analysisPeriod = Analysis::PERIOD_30_DAYS;
        }

        Artisan::call("save-analyzes {$analysisPeriod} '{$endPeriod}' {$type}");

        return true;
    }
}
