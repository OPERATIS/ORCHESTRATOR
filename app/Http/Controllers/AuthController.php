<?php

namespace App\Http\Controllers;

use App\Mail\RecoverPassword;
use App\Mail\Registration;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            return view('auth.login');
        }
    }

    public function registration(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
//                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'confirm_password' => 'required|min:6|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            }

            $data = $request->all();

            $name = preg_match('/^([^@]+)@/', $data['email'], $matches) ? $matches[1] : $data['email'];
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

//            Mail::to($data['email'])
//                ->send(new Registration($data['name'], $data['password']));

            Auth::login($user);

            return response()->json([
                'status' => true,
                'redirect' => url('dashboard')
            ]);
        } else {
            return view('auth.registration');
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
                        Mail::to($user->email)
                            ->send(new RecoverPassword($token));

                        return Password::RESET_LINK_SENT;
                    }
                );

                if ($status === Password::RESET_LINK_SENT) {
                    return response()->json([
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'errors' => ['Invalid credentials']
                    ]);
                }
            }
        } else {
            return view('auth.forgot-password');
        }
    }

    public function resetPassword($token, Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
//                'email' => 'required|email',
                'password' => 'required|min:6',
                'confirm_password' => 'required|min:6|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
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

                if ($status === Password::PASSWORD_RESET) {
                    return response()->json([
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
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
}
