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

//   Route::get('/', function () {
//       return view('/welcome');
//   });
     //Route::get('/', 'menuController@index');

    //物品
    Route::resource('material','App\Http\Controllers\materialController');
    //物品詳細
    Route::resource('material-detail','App\Http\Controllers\materialDetailController');
    //圧力損失、揚程表示
    Route::resource('pressuredrop','App\Http\Controllers\pressuredropController');
    //シミュレーション
    Route::resource('simulation','App\Http\Controllers\simulationController');
    //シミュレーション詳細
    Route::resource('simulation-detail','App\Http\Controllers\simulationDetailController');


    Auth::routes();


    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home2');
