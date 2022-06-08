<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'public'], function () {
        Route::post('/login', [AuthController::class, 'attempt']);
    });

    Route::group(['prefix' => 'private'], function () {
        Route::group(['middleware' => ['jwt-auth']], function () {
            Route::post('/auth/logout', [AuthController::class, 'logout']);
            Route::get('/users', [UserController::class, 'getAllUsers']);
            Route::get('/auth/user', [AuthController::class, 'authUser']);

            Route::get('/rooms', [MessageController::class, 'getRooms']);
        });
    });
});
