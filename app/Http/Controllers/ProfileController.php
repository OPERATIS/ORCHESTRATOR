<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile.index')
            ->with('user', $user);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validatorUpdate = Validator::make($request->all(), [
            'brand_name' => 'nullable|min:3',
            'email' => 'nullable|email',
            'new_email' => 'nullable|required_with:email|email',
            'password' => 'nullable|min:8',
            'new_password' => 'nullable|required_with:password|min:8|confirmed',
        ]);

        $password = $request->get('password');
        if (empty($password)) {
            if ($user->password !== Hash::make($password)) {
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

            if ($key === 'new_email') {
                $user->email = $value;
            }

            if ($key === 'new_password') {
                $user->password = Hash::make($value);
            }
        }

        $user->save();

        return response()->json([
            'status' => true
        ]);
    }

    public function checkPassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validatorUpdate = Validator::make($request->all(), [
            'password' => 'nullable|min:8'
        ]);

        $password = $request->get('password');
        if (empty($password)) {
            if ($user->password !== Hash::make($password)) {
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

        return response()->json([
            'status' => true
        ]);
    }
}
