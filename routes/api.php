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
        //Route::post('loginTest', 'AuthController@loginTest')->name('user.loginTest');
        Route::post('logout', 'AuthController@logout');
    });
    Route::get('schedule/export', 'ScheduleController@export');
    Route::get('checkIpAddress', [ImageFaceController::class, 'checkIpAddress']);
    Route::get('arriving_report/download', 'ArrivingReportController@download');
    Route::get('analytic/download', 'AnalyticController@download');
    Route::get('analytic/export-emotions', 'AnalyticController@exportEmotions');

    Route::group(['middleware' => ['auth:user','managerRole']], function () {

        Route::apiResource('arriving_report', 'ArrivingReportController');
        Route::get('analytic/emotions', 'AnalyticController@getEmotions');
        Route::apiResource('analytic', 'AnalyticController');
        //Route::apiResource('user', UserController::class); Không được dùng cách viết này với apiResource vì sẽ bị lỗi không tìm thấy
        Route::get('/user/list_all', 'UserController@getAllEmployee');
        Route::apiResource('/user', 'UserController');
        Route::get('/role',[RoleController::class, 'index']);
        Route::get('schedule/one-day', 'ScheduleController@scheduleOneDay');
        Route::get('schedule/result-chat-gpt', 'ScheduleController@getChatGPT');
        Route::get('schedule', 'ScheduleController@index')->withoutMiddleware(['managerRole']);
        Route::apiResource('policy', 'PolicyController');
        Route::apiResource('viam_user', 'VIAMUserController');

        Route::group(['prefix' => 'image_face'],function (){
            Route::get('', [ImageFaceController::class, 'index'])->withoutMiddleware(['auth:user']);
            Route::post('compareFace', [ImageFaceController::class, 'compareFace'])->withoutMiddleware(['auth:user','managerRole']);
            Route::post('checkImage', [ImageFaceController::class, 'checkImage'])->withoutMiddleware(['auth:user']);
            //Route::post('sendMailNegative', [ImageFaceController::class, 'sendMailNegative'])->withoutMiddleware(['auth:user','managerRole']);
            Route::post('', [ImageFaceController::class, 'create']);
            Route::delete('{id}', [ImageFaceController::class, 'destroy']);
        });
    });
});

Route::post('/slack/events', [SlackEventModeController::class, 'handleVerification']);
