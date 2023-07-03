<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageFaceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnalyticController;
use App\Http\Controllers\Api\SlackEventModeController;
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
//    Route::post('loginTest', 'AuthController@loginTest')->name('user.loginTest');
    Route::post('logout', 'AuthController@logout');
  });
  Route::post('checkIpAddress', [ImageFaceController::class, 'checkIpAddress']);
  Route::group(['middleware' => ['auth:user','managerRole']], function () {
    Route::apiResource('arriving_report', 'ArrivingReportController');
    Route::apiResource('analytic', 'AnalyticController');
//    Route::apiResource('user', UserController::class); Không được dùng cách viết này với apiResource vì sẽ bị lỗi không tìm thấy
    Route::apiResource('user', 'UserController');
    Route::get('/role',[RoleController::class, 'index']);
    Route::group(['prefix' => 'image_face'],function (){
      Route::get('', [ImageFaceController::class, 'index'])->withoutMiddleware(['auth:user']);
      Route::post('compareFace', [ImageFaceController::class, 'compareFace'])->withoutMiddleware(['auth:user','managerRole']);
      Route::post('checkImage', [ImageFaceController::class, 'checkImage'])->withoutMiddleware(['auth:user']);
      Route::post('', [ImageFaceController::class, 'create']);
      Route::delete('{id}', [ImageFaceController::class, 'destroy']);
    });
  });
});

Route::post('/slack/events', [SlackEventModeController::class, 'handleVerification']);
