<?php

namespace App\Jobs\Shopify;

use App\Models\Connect;
use App\Models\Order;
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

    protected $connect = null;
    protected $startPeriod = null;
    protected $endPeriod = null;

    /**
     * @param Connect $connect
     * @param $startPeriod
     * @param $endPeriod
     */
    public function __construct(Connect $connect, $startPeriod, $endPeriod)
    {
        $this->connect = $connect;
        $this->startPeriod = $startPeriod;
        $this->endPeriod = $endPeriod;
    }

    public function handle(): void
    {
        $shopify = $this->connect;
        Shopify::shopifySetContext($shopify->app_user_slug);

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
                        'created_at_max' => Carbon::parse($this->endPeriod)->toIso8601String(),
                        'created_at_min' => Carbon::parse($this->startPeriod)->toIso8601String(),
                        'fields' => implode(',', [
                            'financial_status',
                            'order_number',
                            'total_price',
                            'customer',
                            'id',
                            'created_at'
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
                    $orders[] = [
                        "connect_id" => $shopify->id,
                        "order_id" => $order->id,
                        "order_created_at" => $order->created_at,
                        "financial_status" => $order->financial_status,
                        "order_number" => $order->order_number,
                        "total_price" => $order->total_price,
                        "customer_id" => $order->customer->id ?? null
                    ];
                }

                $attempt = 0;
            } catch (\Exception $exception) {
                $attempt++;
            }
        } while ($pageInfo && $pageInfo->hasNextPage() && $attempt < 3);

        Order::insert($orders);
    }
}
