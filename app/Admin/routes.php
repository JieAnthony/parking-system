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

    $router->resource('users', 'UserController');
    $router->resource('levels', 'LevelController');
    $router->resource('finances', 'FinanceController')->only(['index']);
    $router->resource('qas', 'QaController');
});
