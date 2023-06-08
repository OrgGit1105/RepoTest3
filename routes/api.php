<?php

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

//User
Route::apiResource('user', UserController::class);

//ImageFace
Route::get('/image_face', [ImageFaceController::class, 'index']);
Route::post('/image_face/{id}', [ImageFaceController::class, 'create']);
