<?php

namespace App\Jobs\Shopify;

use App\Models\Checkout;
use App\Models\CheckoutLineItem;
use App\Models\Integration;
use App\Services\Log as LogService;
use App\Services\Shopify;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Shopify\Clients\Rest;

class GetCheckouts implements ShouldQueue
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
        do {
            try {
                $client = new Rest($shopify->app_user_slug, $shopify->access_token);
                // First query
                if (empty($pageInfo)) {
                    $response = $client->get('checkouts', [], [
                        'limit' => 100,
                        'updated_at_max' => Carbon::parse($this->endPeriod)->toIso8601String(),
                        'updated_at_min' => Carbon::parse($this->startPeriod)->toIso8601String(),
                        'fields' => implode(',', [
                            'order_id',
                            'created_at',
                            'completed_at',
                            'customer_id',
                            'total_price',
                            'line_items'
                        ])
                    ]);
                } else {
                    $response = $client->get('checkouts', $pageInfo->getNextPageQuery(), $pageInfo->getNextPageQuery());
                }

                $pageInfo = $response->getPageInfo();
                $serializedPageInfo = serialize($pageInfo);
                $pageInfo = unserialize($serializedPageInfo);

                $responseCheckouts = json_decode($response->getBody());
                foreach ($responseCheckouts->checkouts as $checkout) {
                    $localCheckout = Checkout::updateOrCreate([
                        'integration_id' => $shopify->id,
                        'token' => $checkout->token,
                    ], [
                        'order_id' => $checkout->order_id ?? null,
                        'checkout_created_at' => $checkout->created_at,
                        'checkout_completed_at' => $checkout->completed_at,
                        'customer_id' => $checkout->customer->id,
                        'total_price' => $checkout->total_price,
                        'gift_cards' => $checkout->gift_cards
                    ]);

                    // Delete old records
                    $checkoutLineItemIds = [];
                    foreach ($checkout->line_items as $lineItem) {
                        $checkoutLineItemIds[] = $lineItem->key;
                    }

                    if (count($checkoutLineItemIds)) {
                        CheckoutLineItem::query()
                            ->where('checkout_id', $localCheckout->id)
                            ->whereNotIn('id', $checkoutLineItemIds)
                            ->delete();
                    }

                    foreach ($checkout->line_items as $lineItem) {
                        CheckoutLineItem::updateOrCreate([
                            'integration_id' => $shopify->id,
                            'checkout_id' => $localCheckout->id,
                            'checkout_line_item_id' => $lineItem->key
                        ], [
                            'product_id' => $lineItem->product_id,
                            'variant_id' => $lineItem->variant_id,
                            'price' => $lineItem->price,
                            'line_price' => $lineItem->line_price,
                            'quantity' => $lineItem->quantity,
                            'gift_card' => $lineItem->gift_card
                        ]);
                    }
                }

                $attempt = 0;
                $logService->addSuccess('GetCheckouts');
            } catch (\Exception $exception) {
                $attempt++;
                $logService->addError('GetCheckouts', $exception->getMessage());
            }
        } while ($pageInfo && $pageInfo->hasNextPage() && $attempt < 3);
    }
}
