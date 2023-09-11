<?php

namespace App\Http\Controllers;

use App\Mail\RecoverPassword;
use App\Mail\Registration;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function customLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $jsonMessage = $validator->errors()->first();
            $jsonStatus = false;
        } else {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $jsonMessage = $validator->errors()->first();
                $jsonStatus = true;
                $jsonRedirect = route('dashboard');
            } else {
                $jsonStatus = false;
                $jsonMessage = '';
            }
        }

        return response()->json([
            'status' => $jsonStatus ?? false,
            'message' => $jsonMessage ?? null,
            'redirect' => $jsonRedirect ?? null
        ]);
    }

    public function registration()
    {
        return view('auth.registration');
    }

    public function customRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $jsonMessage = $validator->errors()->first();
            $jsonStatus = false;
        } else {
            $data = $request->all();
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            Mail::to($data['email'])
                ->send(new Registration($data['name'], $data['password']));

            $jsonStatus = true;
            $jsonRedirect = redirect('dashboard');
        }

        return response()->json([
            'status' => $jsonStatus ?? false,
            'message' => $jsonMessage ?? null,
            'redirect' => $jsonRedirect ?? null
        ]);
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function customForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $jsonMessage = $validator->errors()->first();
            $jsonStatus = false;
        } else {
            $status = Password::sendResetLink($request->only('email'),
                function ($user, $token) {
                    Mail::to($user->email)
                        ->send(new RecoverPassword($token));

                    return Password::RESET_LINK_SENT;
                }
            );

            $jsonStatus = $status === Password::RESET_LINK_SENT;
            $jsonMessage = '';
        }

        return response()->json([
            'status' => $jsonStatus,
            'message' => $jsonMessage ?? null
        ]);
    }

    public function resetPassword($token)
    {
        return view('auth.reset-password')
            ->with('token', $token);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customResetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $jsonMessage = $validator->errors()->first();
            $jsonStatus = false;
        } else {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                    event(new PasswordReset($user));
                }
            );

            $jsonStatus = ($status === Password::PASSWORD_RESET);
            if ($status === Password::PASSWORD_RESET) {
                $jsonMessage = '';
            } elseif ($status === Password::INVALID_USER) {
                $jsonMessage = '';
            } elseif ($status === Password::INVALID_TOKEN) {
                $jsonMessage = '';
            } elseif ($status === Password::RESET_THROTTLED) {
                $jsonMessage = '';
            }
        }

        return response()->json([
            'status' => $jsonStatus,
            'message' => $jsonMessage ?? null
        ]);
    }
}
