<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Stripe\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load('subscriptions');
        $subscription = $user->subscriptions->sortByDesc('id')->first();

        /** @var Payment $payment */
//        $payment = app()->make(Payment::class);
//        $transactions = $payment->getTransactions($user->stripe_id);

        return view('profile.index')
            ->with('user', $user)
            ->with('subscription', $subscription)
            ->with('transactions', $transactions ?? []);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        Session::flash('success-message', 'Update ');

        $validatorUpdate = Validator::make($request->all(), [
            'brand_name' => 'nullable|min:3',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $password = $request->get('password');
        if (!empty($password)) {
            if (!Hash::check($password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'errors' => [
                        'password' => ['Invalid credentials']
                    ]
                ]);
            }
        }

        if ($validatorUpdate->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validatorUpdate->errors()
            ]);
        }

        $updateData = $request->all();
        $updateData = array_filter($updateData);

        foreach ($updateData as $key => $value) {
            if ($key === 'brand_name') {
                $user->brand_name = $value;
            }

            if ($key === 'email' && !empty($value)) {
                $user->email = $value;
            }

            if ($key === 'new_password' && !empty($value)) {
                $user->password = Hash::make($value);
            }
        }

        $user->save();

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validatorUpdate = Validator::make($request->all(), [
            'password' => 'nullable|min:8'
        ]);

        if ($validatorUpdate->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validatorUpdate->errors()
            ]);
        }

        $password = $request->get('password');
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'password' => ['Invalid credentials']
                ]
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }
}
