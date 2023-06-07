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

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {

  Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('remind-password', 'AuthController@remindPassword');
  });
  Route::group(['middleware' => 'auth:user'], function () {
    Route::group(['prefix' => 'auth'], function () {
      Route::post('refresh', 'AuthController@refresh');
    });
    Route::get('profile', 'AuthController@getProfile');
    Route::put('profile', 'AuthController@update');
    Route::apiResource('user', 'UserController');
    Route::get('company_branch/role', 'CompanyBranchController@getByRole');
    Route::get('company_branch/user', 'CompanyBranchController@getByUser');
    Route::apiResource('company_branch', 'CompanyBranchController');
    Route::apiResource('enrollment', 'EnrollmentController');
    Route::get('employee/all','EmployeeController@getAll');
    Route::get('employee/detail','EmployeeController@detail');
    Route::apiResource('employee','EmployeeController');
//    Route::Get('retirement-prediction-chart', 'DataManagementController@index');
    Route::get('digitaco_data/detail/{id}/{type}', 'DigitacoFileController@detail');
    Route::apiResource('digitaco_data', 'DigitacoFileController');
    Route::apiResource('risk_score', 'RiskScoreController');
    Route::apiResource('retirement_prediction_chart', 'RetirementPredictionChartController');
  });
  Route::apiResource('roles', "RoleController")->middleware(['auth:user']);

});

