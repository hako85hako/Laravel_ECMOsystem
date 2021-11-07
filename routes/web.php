<?php

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
//Route::get('ECMO-simulator/menu', 'mainController@index');
//Route::post('ECMO-simulator/start', 'mainController@start');
//Route::get('ECMO-simulator/', 'menuController@start');

     Route::resource('material','App\Http\Controllers\materialController');

     Route::resource('pressuredrop','App\Http\Controllers\pressuredropController');

     Route::resource('material-detail','App\Http\Controllers\materialDetailController');


