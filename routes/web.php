<?php

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

Route::get('/', 'ElevatorQueueController@index');
Route::get('/elevators', 'ElevatorQueueController@index');
Route::get('/simplyrets', 'SimplyRetsController@index');
