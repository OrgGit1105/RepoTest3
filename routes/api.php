<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageFaceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
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

//Role
Route::get('/role',[RoleController::class, 'index']);

//Auth
//User Start
//    Route::get('user/export',[UserController::class, 'export']);
Route::apiResource('user', UserController::class);
//User End

//ImageFace Start
Route::group(['prefix' => 'image_face'],function (){
  Route::get('', [ImageFaceController::class, 'index']);
  Route::post('', [ImageFaceController::class, 'create']);
  Route::delete('{id}', [ImageFaceController::class, 'destroy']);
});
//ImageFace End
