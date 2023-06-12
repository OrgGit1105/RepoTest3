<?php

use Illuminate\Http\Request;
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
Route::group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['cors']], function () {
  Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login')->name('user.login');
    Route::post('logout', 'AuthController@logout');
  });
  Route::group(['middleware' => 'auth:user'], function () {
    Route::apiResource('arriving_report', 'ArrivingReportController');
  });
});

