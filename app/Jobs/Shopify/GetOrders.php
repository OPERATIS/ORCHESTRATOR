<?php

namespace App\Jobs\Shopify;

use App\Models\Integration;
use App\Models\Order;
use App\Services\Log as LogService;
use App\Services\Shopify;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Shopify\Clients\Rest;

class GetOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $integration = null;
    protected $startPeriod = null;
    protected $endPeriod = null;

    /**
     * @param Integration $integration
     * @param $startPeriod
     * @param $endPeriod
     */
    public function __construct(Integration $integration, $startPeriod, $endPeriod)
    {
        $this->integration = $integration;
        $this->startPeriod = $startPeriod;
        $this->endPeriod = $endPeriod;
    }

    public function handle(): void
    {
        $shopify = $this->integration;
        $logService = new LogService('shopify', $this->integration->id);
        Shopify::setContext($shopify->app_user_slug);

        $attempt = 0;
        $pageInfo = null;
        $orders = [];
        do {
            try {
                $client = new Rest($shopify->app_user_slug, $shopify->access_token);
                // First query
                if (empty($pageInfo)) {
                    $response = $client->get('orders', [], [
                        'limit' => 100,
                        'updated_at_max' => Carbon::parse($this->endPeriod)->toIso8601String(),
                        'updated_at_min' => Carbon::parse($this->startPeriod)->toIso8601String(),
                        'fields' => implode(',', [
                            'financial_status',
                            'order_number',
                            'total_price',
                            'customer',
                            'id',
                            'created_at',
                            'total_line_items_price',
                            'line_items',
                            'refunds',
                            'reference',
                            'referring_site',
                        ])
                    ]);
                } else {
                    $response = $client->get('orders', $pageInfo->getNextPageQuery(), $pageInfo->getNextPageQuery());
                }

                $pageInfo = $response->getPageInfo();
                $serializedPageInfo = serialize($pageInfo);
                $pageInfo = unserialize($serializedPageInfo);


                $responseOrders = json_decode($response->getBody()->getContents());
                foreach ($responseOrders->orders as $order) {
                    $totalRefundLineItemsPrice = 0;
                    foreach ($order->refunds->refund_line_items ?? [] as $one) {
                        $totalRefundLineItemsPrice += $one->subtotal;
                    }

                    $ads = false;
                    if (str_contains($order->reference, 'gclid') || str_contains($order->reference, 'fbclid')) {
                        $ads = true;
                    } elseif (str_contains($order->referring_site, 'gclid') || str_contains($order->referring_site, 'fbclid')) {
                        $ads = true;
                    }

                    Order::updateOrCreate([
                        "integration_id" => $shopify->id,
                        "order_id" => $order->id,
                        "order_number" => $order->order_number,
                    ], [
                        "order_created_at" => Carbon::parse($order->created_at)->setTimezone(0),
                        "financial_status" => $order->financial_status,
                        "total_price" => $order->total_price,
                        "customer_id" => $order->customer->id ?? null,
                        "total_line_items_price" => $order->total_line_items_price,
                        "count_line_items" => count($order->line_items ?? []),
                        "total_refund_line_items_price" => $totalRefundLineItemsPrice,
                        "count_refund_line_items" => count($order->refunds->refund_line_items ?? []),
                        "reference" => $order->reference,
                        "referring_site" => $order->referring_site,
                        "ads" => $ads
                    ]);
                }

                $attempt = 0;
                $logService->addSuccess('get');
            } catch (\Exception $exception) {
                $attempt++;
                $logService->addError('get', $exception->getMessage());
            }
        } while ($pageInfo && $pageInfo->hasNextPage() && $attempt < 3);
    }
}
