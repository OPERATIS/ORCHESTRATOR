<?php

namespace App\Http\Controllers\Stripe;

use App\Mail\EndSubscription;
use App\Mail\StartSubscription;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Stripe\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\Util\Util;
use Stripe\WebhookSignature;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WebhooksController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function callback(Request $request): Response
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));

        try {
            WebhookSignature::verifyHeader(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret'),
                config('services.stripe.webhook_tolerance')
            );
        } catch (SignatureVerificationException $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        $payload = json_decode($request->getContent(), true);
        $method = 'handle' . Str::studly(str_replace('.', '_', $payload['type']));

        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        }

        return new Response('Webhook Handled', 404);
    }

    /**
     * @param array $payload
     * @return Response
     */
    protected function handleCheckoutSessionCompleted(array $payload): Response
    {
        $session = Util::convertToStripeObject($payload['data']['object'], null);
        if (isset($session) &&
            isset($session->metadata) &&
            isset($session->metadata->user_id)
        ) {
            $user = User::where('id', $session->metadata->user_id)->first();

            if ($user) {
                $user->stripe_id = $session->customer;
                $user->save();

                return new Response('Webhook Handled', 200);
            }
        }

        return new Response('Webhook Handled', 200);
    }

    /**
     * @param array $payload
     * @return Response
     * @throws ApiErrorException
     */
    protected function handleCustomerSubscriptionCreated(array $payload): Response
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if (!$user) {
            $searchSubscription = Subscription::where('stripe_id', $payload['data']['object']['id'])
                ->with(['user'])
                ->first();

            $user = $searchSubscription->user ?? null;
        }

        $data = $payload['data']['object'];

        if ($user) {
            $data = $this->getActualStripeSubscription($data['id'])->toArray();
            $subscription = $this->updateSubscription($user, $data);

            if ($subscription && $subscription->status === StripeSubscription::STATUS_ACTIVE) {
                // Send only one time (and if not exists current actual subscriptions)
                $this->sendLetterAfterStartSubscription($user, $subscription);
            }

            return new Response('Webhook Handled', 200);
        }

        return new Response('Webhook Handled', 404);
    }

    protected function updateSubscription(User $user, $data)
    {
        $amounts = [];
        foreach ($data['items']['data'] as $item) {
            $amounts[] = $item['plan']['amount'];
        }

        $days = Payment::PERIOD_ONE_MONTH_IN_DAYS;

        $subscribeTo = null;
        $nextBillingPeriodAt = Carbon::createFromTimestamp($data['current_period_end']);
        $endsAt = Carbon::createFromTimestamp($data['current_period_end']);
        if (isset($data['cancel_at_period_end']) && $data['cancel_at_period_end']) {
            $subscribeTo = $endsAt;
        } elseif (isset($data['cancel_at']) && $data['cancel_at']) {
            $subscribeTo = Carbon::createFromTimestamp($data['cancel_at']);
        } elseif (isset($data['canceled_at']) && $data['canceled_at']) {
            $subscribeTo = Carbon::createFromTimestamp($data['canceled_at']);
        }

        // Cancel previous subscription
        if ($data['status'] === StripeSubscription::STATUS_ACTIVE) {
            $subscription = Subscription::where('user_id', $user->id)
                ->where('stripe_id', $data['id'])
                ->where('status', StripeSubscription::STATUS_ACTIVE)
                ->where('days', '<', $days)
                ->orderByDesc('created_at')
                ->first();

            if ($subscription) {
                $subscription->ends_at = Carbon::now();
                $subscription->user_subscribe_to = Carbon::now();
                $subscription->save();
            }
        }

        // Cancel previous subscription (after wrong change)
        if ($data['status'] === StripeSubscription::STATUS_CANCELED) {
            $subscriptions = Subscription::where('user_id', $user->id)
                ->where('manual_status', 'update')
                ->where('stripe_id', $data['id'])
                ->orderByDesc('created_at')
                ->get();

            foreach ($subscriptions as $subscription) {
                $subscription->status = StripeSubscription::STATUS_CANCELED;
                $subscription->manual_status = null;
                $subscription->user_subscribe_to = $subscription->ends_at;
                $subscription->save();
            }
        }

        // Cancel previous subscription
        $subscriptions = Subscription::where('user_id', $user->id)
            ->where('stripe_id', $data['id'])
            ->whereNull('user_subscribe_to')
            ->where('ends_at', '!=', $endsAt)
            ->where('days', '=', $days)
            ->orderByDesc('created_at')
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->user_subscribe_to = $subscription->ends_at;
            $subscription->save();
        }

        return Subscription::updateOrCreate([
            'user_id' => $user->id,
            'stripe_id' => $data['id'],
            'ends_at' => $endsAt
        ], [
            'price' => array_sum($amounts) / 100,
            'days' => $days,
            'manual_status' => null,
            'status' => $data['status'],
            'next_billing_period_at' => $nextBillingPeriodAt,
            'user_subscribe_to' => $subscribeTo,
        ]);
    }

    /**
     * @param User $user
     * @param $subscription
     */
    protected function sendLetterAfterStartSubscription(User $user, $subscription)
    {
        $search = Subscription::where('user_id', $subscription->user_id)
            ->where('send_letter_after_start', true)
            ->where(function ($query) use ($subscription) {
                $query->active()
                    ->orWhere('stripe_id', $subscription->stripe_id);
            })
            ->first();

        // Send email
        if (!$search) {
            try {
                Mail::to($user->email)
                    ->send(new StartSubscription());
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        $subscription->send_letter_after_start = true;
        $subscription->save();
    }

    /**
     * @param array $payload
     * @return Response
     * @throws ApiErrorException
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if (!$user) {
            $searchSubscription = Subscription::where('stripe_id', $payload['data']['object']['id'])
                ->with(['user'])
                ->first();

            $user = $searchSubscription->user ?? null;
        }

        if ($user) {
            $data = $payload['data']['object'];
            $data = $this->getActualStripeSubscription($data['id'])->toArray();
            $subscription = $this->updateSubscription($user, $data);

            if ($subscription && $subscription->status === StripeSubscription::STATUS_ACTIVE) {
                // Send only one time (and if not exists current actual subscriptions)
                $this->sendLetterAfterStartSubscription($user, $subscription);
            } elseif ($subscription && $subscription->status === StripeSubscription::STATUS_CANCELED) {
                // Send letter if not exist other subscriptions
                $this->sendLetterAfterEndSubscription($user, $subscription);
            }

            return new Response('Webhook Handled', 200);
        }

        return new Response('Webhook Handled', 404);
    }

    /**
     * @param array $payload
     * @return Response
     * @throws ApiErrorException
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if (!$user) {
            $searchSubscription = Subscription::where('stripe_id', $payload['data']['object']['id'])
                ->with(['user'])
                ->first();

            $user = $searchSubscription->user ?? null;
        }

        if ($user) {
            $data = $payload['data']['object'];
            $data = $this->getActualStripeSubscription($data['id'])->toArray();
            $subscription = $this->updateSubscription($user, $data);

            if ($subscription && $subscription->status === StripeSubscription::STATUS_CANCELED) {
                // Send letter if not exist other subscriptions
                $this->sendLetterAfterEndSubscription($user, $subscription);
            }

            return new Response('Webhook Handled', 200);
        }

        return new Response('Webhook Handled', 404);
    }

    /**
     * @param User $user
     * @param $subscription
     */
    protected function sendLetterAfterEndSubscription(User $user, $subscription)
    {
        $searchOtherSubscription = Subscription::where('user_id', $subscription->user_id)
            ->active()
            ->first();

        if (!$searchOtherSubscription) {
            if (!$subscription->send_letter_after_end) {
                try {
                    Mail::to($user->email)
                        ->send(new EndSubscription());
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }

        // Create and send letter
        $subscription->send_letter_after_end = true;
        $subscription->save();
    }

    /**
     * @param $subscriptionId
     * @return StripeSubscription
     * @throws ApiErrorException
     */
    protected function getActualStripeSubscription($subscriptionId): StripeSubscription
    {
        return StripeSubscription::retrieve($subscriptionId);
    }

    /**
     * @param $stripeId
     * @return mixed|void
     */
    protected function getUserByStripeId($stripeId)
    {
        if ($stripeId === null) {
            return;
        }

        return User::where('stripe_id', $stripeId)->first();
    }
}
