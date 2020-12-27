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
});
