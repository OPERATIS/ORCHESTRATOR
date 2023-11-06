<?php

namespace App\Http\Controllers;

use App\Mail\RecoverPassword;
use App\Mail\Registration;
use App\Models\UserSocial;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            } else {
                if (Auth::attempt($request->only(['email', 'password']))) {
                    return response()->json([
                        'status' => true,
                        'redirect' => url('dashboard')
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'errors' => [
                            'email' => [' '],
                            'password' => ['Invalid credentials']
                        ]
                    ]);
                }
            }
        } else {
            if (Auth::user()) {
                return redirect('dashboard');
            }
            return view('auth.login', [
                'view' => 'login'
            ]);
        }
    }

    public function registration(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            }

            $data = $request->all();

            $emailParts = explode('@', $data['email']);
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'brand_name' => $emailParts[0]
            ]);

            try {
                Mail::to($data['email'])
                    ->send(new Registration($emailParts[0], $data['password']));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            Auth::login($user);

            return response()->json([
                'status' => true,
                'redirect' => url('dashboard')
            ]);
        } else {
            return view('auth.login', [
                'view' => 'registration'
            ]);
        }
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect('login');
    }

    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            } else {
                $status = Password::sendResetLink($request->only('email'),
                    function ($user, $token) {
                        try {
                            Mail::to($user->email)
                                ->send(new RecoverPassword($token));

                            return Password::RESET_LINK_SENT;
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());

                            return false;
                        }
                    }
                );

                if ($status === Password::RESET_LINK_SENT) {
                    return response()->json([
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        // TODO incorrect
                        'errors' => [
                            'email' => ['Invalid credentials']
                        ]
                    ]);
                }
            }
        } else {
            return view('auth.login', [
                'view' => 'forgot_password'
            ]);
        }
    }

    public function resetPassword($token, Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            } else {
                $passwordResets = \App\Models\PasswordReset::get();
                foreach ($passwordResets as $passwordReset) {
                    if (Hash::check(request()->get('token'), $passwordReset->token)) {
                        $email = $passwordReset->email;
                        break;
                    }
                }
                $request->merge([
                    'email' => $email ?? null
                ]);

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

                if ($status === Password::PASSWORD_RESET) {
                    return response()->json([
                        'status' => true,
                        'redirect' => url('login')
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'errors' => ['Invalid credentials']
                    ]);
                }
            }
        } elseif (!empty($token)) {
            return view('auth.reset-password')
                ->with('token', $token);
        } else {
            return abort(404);
        }
    }

    public function googleLogin()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    public function googleCallback()
    {
        // Get user from social
        $socialiteUser = Socialite::driver('google')
            ->user();

        // Search user
        $user = User::where('email', $socialiteUser->getEmail())
            ->first();

        if (!$user) {
            $emailParts = explode('@', $socialiteUser->getEmail());
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'password' => Hash::make(uniqid(rand())),
                'brand_name' => $emailParts[0]
            ]);
        }

        UserSocial::updateOrCreate([
            'user_id' => $user->id,
            'social' => 'google',
            'social_id' => $socialiteUser->getId(),
        ], [
            // OAuth 2.0 providers...
            'token' => $socialiteUser->token,
            'refresh_token' => $socialiteUser->refreshToken,
            'expires_in' => $socialiteUser->expiresIn,
            // All providers...
            'nickname' => $socialiteUser->getNickname(),
            'avatar' => $socialiteUser->getAvatar(),
        ]);

        Auth::login($user);

        return redirect('dashboard');
    }
}
