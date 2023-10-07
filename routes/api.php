<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MetricsController;
use App\Http\Controllers\Api\SlackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/metrics/actual', [MetricsController::class, 'actualData'])->name('api.metrics.actual');
    Route::get('/metrics/chart', [MetricsController::class, 'chartData'])->name('api.metrics.chart');
    Route::get('/slack/login', [SlackController::class, 'login'])->name('api.slack.login');
    Route::get('/slack/callback', [SlackController::class, 'callback'])->name('api.slack.callback');
});

Route::post('/auth/register', [AuthController::class, 'registerUser'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name('api.auth.login');
