<?php

use App\Http\Controllers\ConnectsController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/connect/shopify/login', [ConnectsController::class, 'shopifyLogin'])->name('shopifyLogin');
Route::get('/connect/shopify/callback', [ConnectsController::class, 'shopifyCallback'])->name('shopifyCallback');
Route::get('/connect/google/login', [ConnectsController::class, 'googleLogin'])->name('googleLogin');
Route::get('/connect/google/callback', [ConnectsController::class, 'googleCallback'])->name('googleCallback');
