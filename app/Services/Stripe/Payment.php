<?php

namespace App\Services\Stripe;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;

final class Payment
{
    public const PERIOD_ONE_MONTH_IN_DAYS = 30;
    public const PRICE_ONE_MONTH = 99;
    public const MANUAL_STATUS_UPDATING = 'updating';

    protected $priceApiId = null;
    protected $stripeClient = null;

    public function __construct()
    {
        $this->priceApiId = config('services.stripe.price_api_id');
        Stripe::setApiKey(config('services.stripe.secret_key'));
        $this->stripeClient = new StripeClient(config('services.stripe.secret_key'));
    }

    /**
     * @param User $user
     * @return Session
     * @throws ApiErrorException
     */
    public function createSession(User $user): Session
    {
        // Search old by active subscriptions
        $problemSubscriptions = $user->subscriptions()
            ->problem()
            ->get();

        $ids = [];
        foreach ($problemSubscriptions as $problemSubscription) {
            $ids[$problemSubscription->stripe_id] = $problemSubscription->stripe_id;
        }

        foreach ($ids as $id) {
            // Receive subscription
            try {
                $stripeSubscription = StripeSubscription::retrieve($id);
                $stripeSubscription->cancel();
            } catch (\Exception $exception) {
                Log::error('[Payment.cancelSubscription] ' . $exception->getTraceAsString());
            }
        }

        $lineItems = $this->getLineItems();

        // Prepare data for sessions
        $data = [
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'success_url' => route('thankYou'),
            'cancel_url' => route('profile'),
            'line_items' => $lineItems,
            'metadata' => [
                'user_id' => $user->id
            ],
        ];

        $data['allow_promotion_codes'] = true;

        // Set customer if user have stripe_id
        if (!empty($user->stripe_id)) {
            $data['customer'] = $user->stripe_id;
        }

        return Session::create($data);
    }

    /**
     * @param Subscription $subscription
     * @throws ApiErrorException
     */
    public function cancelSubscription(Subscription $subscription)
    {
        $this->stripeClient->subscriptions
            ->update($subscription->stripe_id, [
                'cancel_at_period_end' => true
            ]);

        $subscription->manual_status = self::MANUAL_STATUS_UPDATING;
        $subscription->save();
    }

    /**
     * @param Subscription $subscription
     * @throws ApiErrorException
     */
    public function cancelSubscriptionNow(Subscription $subscription)
    {
        $this->stripeClient->subscriptions
            ->cancel($subscription->stripe_id, []);

        $subscription->manual_status = self::MANUAL_STATUS_UPDATING;
        $subscription->save();
    }


    /**
     * @param Subscription $subscription
     * @throws ApiErrorException
     */
    public function resumeSubscription(Subscription $subscription)
    {
        $this->stripeClient->subscriptions
            ->update($subscription->stripe_id, ['cancel_at_period_end' => false]);

        $subscription->manual_status = self::MANUAL_STATUS_UPDATING;
        $subscription->save();
    }

    /**
     * @return array
     */
    public function getLineItems(): array
    {
        $lineItems = [];
        $lineItems[] = [
            'price' => $this->priceApiId,
            'quantity' => 1,
        ];

        return $lineItems;
    }

    /**
     * @param null $stripeId
     * @return array
     */
    public function getTransactions($stripeId = null): array
    {
        $prepared = [];
        if ($stripeId) {
            try {
                $invoices = $this->stripeClient->invoices->all([
                    'limit' => 100,
                    'customer' => $stripeId,
                    'status' => 'paid',
                ]);

                foreach ($invoices->data as $invoice) {
                    $currentItem = [];
                    $currentItem['invoice_pdf'] = $invoice->invoice_pdf;
                    $currentItem['created'] = Carbon::parse($invoice->created)->toDateTimeString();
                    $currentItem['total'] = $invoice->total / 100;

                    foreach ($invoice->lines['data'] as $item) {
                        $currentItem['items'][] = [
                            'amount' => $item->amount / 100,
                            'description' => $item->description,
                        ];
                    }

                    $prepared[] = $currentItem;
                }
            } catch (\Exception $exception) {

            }
        }

        return $prepared;
    }
}
