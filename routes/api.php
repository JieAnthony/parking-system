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
    Route::get('qas', [QaController::class, 'index'])->name('qas.index');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('send/code/{username}', [SmsController::class, 'send'])->name('send.sms');
    Route::get('orders/find/information', [OrderController::class, 'find'])->name('orders.find.information');
    Route::post('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::middleware(['refresh.token'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [UserController::class, 'me'])->name('me');
        Route::apiResource('cars', CarController::class)->only(['index', 'store', 'destroy']);
        Route::get('finances', [FinanceController::class, 'index'])->name('finances.index');
        Route::get('levels', [LevelController::class, 'index'])->name('levels.index');
        Route::post('levels/{level}/buy', [LevelController::class, 'buy'])->name('levels.buy');
        Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
    });
    Route::any('notify/ali', [NotifyController::class, 'ali'])->name('notify.ali');
    Route::any('notify/wechat', [NotifyController::class, 'wechat'])->name('notify.wechat');
});
