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
            Route::post('/user/edit', [UserController::class, 'updateProfile']);
            Route::get('/user/{id}', [UserController::class, 'getUserProfile']);
            Route::get('/auth/user', [AuthController::class, 'authUser']);

            Route::get('/rooms', [MessageController::class, 'getRooms']);
            Route::post('/room/new', [MessageController::class, 'createRoom']);
            Route::post('/room/archive', [MessageController::class, 'archiveRoom']);
            Route::post('/room/send-message', [MessageController::class, 'sendMessage']);
            Route::get('/get-room-by-user/{user_id}', [MessageController::class, 'getRoomDataByUser']);
            Route::get('/room/{id}', [MessageController::class, 'getRoomData']);
            Route::get('/room/{id}/messages', [MessageController::class, 'getMessages']);
            Route::get('/room/{id}/files', [MessageController::class, 'getSharedFiles']);
            Route::get('/room/{id}/notes', [MessageController::class, 'getChatNotes']);
            Route::post('/update-room-note', [MessageController::class, 'updateChatNote']);
            Route::delete('/delete-room-note/{id}', [MessageController::class, 'deleteChatNote']);
        });
    });
});
