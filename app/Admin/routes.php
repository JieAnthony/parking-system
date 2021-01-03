<?php

use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->resource('users', 'UserController')->except(['destroy']);
    $router->resource('levels', 'LevelController');
    $router->resource('orders', 'OrderController')->only(['index', 'create', 'store', 'show']);
    $router->resource('finances', 'FinanceController')->only(['index']);
    $router->resource('cars', 'CarController');
    $router->resource('barriers', 'BarrierController')->except(['show']);
    $router->resource('qas', 'QaController');

    $router->get('system/info', 'SystemController@info');
    $router->get('system/deduction', 'SystemController@deduction');

    $router->any('upload', 'UploadController@handle');
});
