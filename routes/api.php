<?php

namespace App\Http\Controllers\Api;

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

Route::name('api.')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('root');
    Route::get('home', [HomeController::class, 'home'])->name('home');
    Route::apiResource('qas', QaController::class)->only(['index']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::middleware(['refresh.token'])->group(function () {
        Route::get('me', [UserController::class, 'me'])->name('me');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::apiResource('cars', CarController::class)->only(['index', 'destroy']);
        Route::apiResource('finances', FinanceController::class)->only(['index']);
        Route::apiResource('levels', LevelController::class)->only(['index']);
        Route::post('levels/{level}/buy', [LevelController::class, 'buy'])->name('levels.buy');
        Route::apiResource('orders', OrderController::class)->only(['index', 'destroy']);
        Route::get('find/order', [OrderController::class, 'find'])->name('orders.find');
        Route::post('orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    });
    Route::any('payment/notify', [NotifyController::class, 'notify'])->name('payment.notify');
    Route::any('wechat', [WeChatController::class, 'serve'])->name('wechat.serve');
});
