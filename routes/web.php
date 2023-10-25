<?php

use App\Http\Controllers\AlertsController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\ConnectsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Integrations\FacebookController;
use App\Http\Controllers\Integrations\GoogleController;
use App\Http\Controllers\Integrations\ShopifyController;
use App\Http\Controllers\Integrations\SlackController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Notifications\WebhooksController as NotificationsWebhooksController;
use App\Http\Controllers\Stripe\PaymentController;
use App\Http\Controllers\Stripe\WebhooksController as StripeWebhooksController;
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
Route::get('thank-you', [PagesController::class, 'thankYou'])->name('thankYou');
// temp
Route::get('404', [PagesController::class, 'error404'])->name('error404');
Route::get('500', [PagesController::class, 'error500'])->name('error500');

Route::middleware([Authenticate::class])->group(function () {
    Route::post('integrations/shopify/login', [ShopifyController::class, 'login'])->name('integrationsShopifyLogin');
    Route::get('integrations/shopify/callback', [ShopifyController::class, 'callback'])->name('integrationsShopifyCallback');

    Route::get('integrations/google/login', [GoogleController::class, 'login'])->name('integrationsGoogleLogin');
    Route::get('integrations/google/callback', [GoogleController::class, 'callback'])->name('integrationsGoogleCallback');

    Route::get('integrations/facebook/login', [FacebookController::class, 'login'])->name('integrationsFacebookLogin');
    Route::get('integrations/facebook/callback', [FacebookController::class, 'callback'])->name('integrationsFacebookCallback');

    Route::get('integrations/slack/login', [SlackController::class, 'login'])->name('integrationsSlackLogin');
    Route::get('integrations/slack/callback', [SlackController::class, 'callback'])->name('integrationsSlackCallback');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/metrics-chart', [DashboardController::class, 'metricsChart'])->name('metricsChart');

    Route::get('chats', [ChatsController::class, 'index'])->name('chats');
    Route::get('chats/list', [ChatsController::class, 'list'])->name('chatsList');
    Route::any('chats/create', [ChatsController::class, 'create'])->name('chatsCreate');
    Route::get('chats/{chatId}', [ChatsController::class, 'show'])->name('chatShow');
    Route::post('chats/{chatId}/delete', [ChatsController::class, 'delete'])->name('chatDelete');
    Route::post('chats/{chatId}/edit', [ChatsController::class, 'edit'])->name('chatEdit');
    Route::post('chats/{chatId}/send-message', [ChatsController::class, 'sendMessage'])->name('chatSendMessage');
    Route::post('chats/{chatId}/edit-message/{messageId}', [ChatsController::class, 'editMessage'])->name('chatEditMessage');
    Route::get('chats/{chatId}/messages', [ChatsController::class, 'messages'])->name('chatMessages');

    Route::get('alerts', [AlertsController::class, 'index'])->name('alerts');

    Route::get('integrations', [IntegrationsController::class, 'index'])->name('integrations');

    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profileUpdate');
    Route::any('profile/check', [ProfileController::class, 'checkPassword'])->name('checkPassword');

    Route::get('payment/create', [PaymentController::class, 'create'])->name('paymentCreate');
    Route::get('payment/cancel-subscription/{subscriptionId}', [PaymentController::class, 'cancelSubscription'])->name('paymentCancelSubscription');
    Route::get('payment/cancel-subscription-now/{subscriptionId}', [PaymentController::class, 'cancelSubscriptionNow'])->name('paymentCancelSubscriptionNow');
    Route::get('payment/resume-subscription/{subscriptionId}', [PaymentController::class, 'resumeSubscription'])->name('paymentResumeSubscription');
});

Route::any('login', [AuthController::class, 'login'])->name('login');
Route::any('registration', [AuthController::class, 'registration'])->name('registration');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::any('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::any('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('resetPassword');
Route::any('google/login', [AuthController::class, 'googleLogin'])->name('googleLogin');
Route::any('google/callback', [AuthController::class, 'googleCallback'])->name('googleCallback');

Route::any('webhooks/whatsapp', [NotificationsWebhooksController::class, 'whatsapp']);
Route::any('webhooks/messenger', [NotificationsWebhooksController::class, 'messenger']);
Route::post('webhooks/telegram', [NotificationsWebhooksController::class, 'telegram']);

Route::post('webhooks/stripe', [StripeWebhooksController::class, 'callback']);
