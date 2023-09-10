<?php

use App\Http\Controllers\ConnectsController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\PagesController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PagesController::class, 'index'])->name('landing');

Route::middleware([Authenticate::class])->group(function () {
    Route::post('/connect/shopify/login', [ConnectsController::class, 'shopifyLogin'])->name('shopifyLogin');
    Route::get('/connect/shopify/callback', [ConnectsController::class, 'shopifyCallback'])->name('shopifyCallback');

    Route::get('/connect/google/login', [ConnectsController::class, 'googleLogin'])->name('googleLogin');
    Route::get('/connect/google/callback', [ConnectsController::class, 'googleCallback'])->name('googleCallback');

    Route::get('/connect/facebook/login', [ConnectsController::class, 'facebookLogin'])->name('facebookLogin');
    Route::get('/connect/facebook/callback', [ConnectsController::class, 'facebookCallback'])->name('facebookCallback');

    Route::get('dashboard', [PagesController::class, 'dashboard']);
});

Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('customLogin');
Route::get('registration', [CustomAuthController::class, 'registration'])->name('registration');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('customRegistration');
Route::get('logout', [CustomAuthController::class, 'logout'])->name('logout');


//https://www.google.com/search?q=laravel+custom+reset+password&oq=laravel+custom+reser&aqs=chrome.1.69i57j0i13i512j0i22i30l3j0i8i13i30l3j0i13i15i30.5230j0j7&sourceid=chrome&ie=UTF-8
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
