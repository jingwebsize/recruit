<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    //$router->get('/', 'HomeController@index');
    $router->get('/', 'OutcomeController@index');
    $router->resource('users', 'UserController');
    $router->resource('special-incomes', 'SpecialIncomeController');
    //增加IncomeController、LimitController、OutcomeController、Tag
    $router->resource('tags', 'TagController');
    $router->resource('incomes', 'IncomeController');
    $router->resource('incomesall', 'IncomeallController');
    $router->resource('incomequery', 'IncomequeryController');
    $router->resource('limits', 'LimitController');
    $router->resource('limitquery', 'LimitqueryController');
    $router->resource('outcomes', 'OutcomeController');
    $router->resource('outcomequery', 'OutcomequeryController');

});
