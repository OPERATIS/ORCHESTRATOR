<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Stripe\Payment;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    /**
     * @throws ApiErrorException
     * @throws BindingResolutionException
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Payment $payment */
        $payment = app()->make(Payment::class);
        $session = $payment->createSession($user);

        return view('payment.stripe')
            ->with('sessionId', $session->id)
            ->with('publishableKey', config('services.stripe.publishable_key'));
    }

    /**
     * @param $subscriptionId
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function cancelSubscription($subscriptionId): JsonResponse
    {
        return $this->cancelSubscriptionBase($subscriptionId, 'cancelSubscription');
    }

    /**
     * @param $subscriptionId
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function cancelSubscriptionNow($subscriptionId): JsonResponse
    {
        return $this->cancelSubscriptionBase($subscriptionId, 'cancelSubscriptionNow');
    }

    /**
     * @param $subscriptionId
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function resumeSubscription($subscriptionId): JsonResponse
    {
        return $this->cancelSubscriptionBase($subscriptionId, 'resumeSubscription');
    }

    /**
     * @param $subscriptionId
     * @param $type
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    protected function cancelSubscriptionBase($subscriptionId, $type): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $subscription = Subscription::where('user_id', $user->id)
            ->where('id', $subscriptionId)
            ->first();

        if ($subscription) {
            /** @var Payment $payment */
            $payment = app()->make(Payment::class);

            try {
                $payment->{$type}($subscription);
                return response()->json([
                    'status' => true
                ]);
            } catch (\Exception $exception) {
                return response()->json([
                    'status' => false,
                    'errors' => ['The service is experiencing some problems']
                ]);
            }
        } else {
            return response()->json([
                'status' => false
            ]);
        }
    }
}
