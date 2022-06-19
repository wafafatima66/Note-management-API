<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageDailyReportsController;
use App\Http\Controllers\MessageMeetingMinutesController;
use App\Http\Controllers\MessageNotesController;
use App\Http\Controllers\MessageTasksController;
use App\Http\Controllers\MessageWikisController;
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
            Route::get('/room/{id}/members', [MessageController::class, 'getRoomMembers']);
            Route::get('/room/{id}/messages', [MessageController::class, 'getMessages']);
            Route::get('/room/{id}/files', [MessageController::class, 'getSharedFiles']);
            Route::get('/room/{id}/notes', [MessageNotesController::class, 'getChatNotes']);
            Route::get('/room/{id}/meeting-minutes', [MessageMeetingMinutesController::class, 'getMeetingMinutes']);
            Route::get('/room/{id}/wikis', [MessageWikisController::class, 'getWikis']);
            Route::get('/room/{id}/daily-reports', [MessageDailyReportsController::class, 'getDailyReports']);
            Route::get('/room/{id}/tasks', [MessageTasksController::class, 'getTaskList']);
            Route::get('/room/{id}/task-status-list', [MessageTasksController::class, 'getStatusList']);
            Route::post('/save-room-task', [MessageTasksController::class, 'saveTask']);
            Route::post('/save-room-task-status', [MessageTasksController::class, 'saveTaskStatus']);
            Route::post('/save-room-note', [MessageNotesController::class, 'saveChatNote']);
            Route::post('/save-room-meeting-minute', [MessageMeetingMinutesController::class, 'saveMeetingMinute']);
            Route::post('/save-room-wiki', [MessageWikisController::class, 'saveWiki']);
            Route::post('/save-room-daily-report', [MessageDailyReportsController::class, 'saveDailyReport']);
            Route::delete('/delete-room-note/{id}', [MessageNotesController::class, 'deleteChatNote']);
            Route::delete('/delete-room-meeting-minute/{id}', [MessageMeetingMinutesController::class, 'deleteMeetingMinute']);
            Route::delete('/delete-room-wiki/{id}', [MessageWikisController::class, 'deleteWiki']);
            Route::delete('/delete-room-daily-report/{id}', [MessageDailyReportsController::class, 'deleteDailyReport']);
            Route::get('/room-add-member-users', [MessageController::class, 'getAddMemberList']);
            Route::post('/add-room-member', [MessageController::class, 'addMemberToRoom']);
            Route::delete('/remove-room-member', [MessageController::class, 'removeRoomMember']);
            Route::patch('/save-room-settings', [MessageController::class, 'saveRoomSettings']);
        });
    });
});
