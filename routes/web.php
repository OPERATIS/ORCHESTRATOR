<?php

use App\Http\Controllers\ConnectsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\WebhooksController;
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

    Route::get('/connect/slack/login', [ConnectsController::class, 'slackLogin'])->name('slackLogin');
    Route::get('/connect/slack/callback', [ConnectsController::class, 'slackCallback'])->name('slackCallback');

    Route::get('dashboard', [PagesController::class, 'dashboard'])->name('dashboard');
});

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('custom-login', [AuthController::class, 'customLogin'])->name('customLogin');
Route::get('registration', [AuthController::class, 'registration'])->name('registration');
Route::post('custom-registration', [AuthController::class, 'customRegistration'])->name('customRegistration');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::post('custom-forgot-password', [AuthController::class, 'customForgotPassword'])->name('customForgotPassword');
Route::get('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('resetPassword');
Route::post('custom-reset-password', [AuthController::class, 'customResetPassword'])->name('customResetPassword');

Route::get('webhooks/whatsapp', [WebhooksController::class, 'whatsapp']);
Route::post('webhooks/whatsapp', [WebhooksController::class, 'whatsapp']);

Route::get('webhooks/messenger', [WebhooksController::class, 'messenger']);
Route::post('webhooks/messenger', [WebhooksController::class, 'messenger']);
Route::post('webhooks/telegram', [WebhooksController::class, 'telegram']);
