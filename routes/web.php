<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::post('subscriptions', 'SubscriptionsController@store');
Route::delete('subscriptions', 'SubscriptionsController@destroy');

Route::get('home', 'HomeController@index');

Route::post('stripe/webhook', 'WebhooksController@handle');

Auth::routes();
