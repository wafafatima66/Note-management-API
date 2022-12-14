<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\FolderManagementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageDailyReportsController;
use App\Http\Controllers\MessageMeetingMinutesController;
use App\Http\Controllers\MessageNotesController;
use App\Http\Controllers\MessageTasksController;
use App\Http\Controllers\MessageWikisController;
// use App\Http\Controllers\NoteCategoryController;
// use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\Common\ApplicationController;
use App\Http\Controllers\Docua\NoteCategoryController;
use App\Http\Controllers\Docua\NoteController;
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
            Route::get('/room/{id}/notes/{note_id}/comment',  [MessageNotesController::class, 'getChatNoteWithComment']);
            Route::get('/room/{id}/meeting-minutes', [MessageMeetingMinutesController::class, 'getMeetingMinutes']);
            Route::get('/room/{id}/wikis', [MessageWikisController::class, 'getWikis']);
            Route::get('/room/{id}/daily-reports', [MessageDailyReportsController::class, 'getDailyReports']);
            Route::get('/room/{id}/tasks', [MessageTasksController::class, 'getTaskList']);
            Route::get('/room/{id}/task-status-list', [MessageTasksController::class, 'getStatusList']);
            Route::post('/save-room-task', [MessageTasksController::class, 'saveTask']);
            Route::post('/save-room-task-status', [MessageTasksController::class, 'saveTaskStatus']);
            Route::post('/save-room-note', [MessageNotesController::class, 'saveChatNote']);
            Route::post('/save-room-note-comment', [MessageNotesController::class, 'saveChatNoteComment']);
            Route::post('/save-room-meeting-minute', [MessageMeetingMinutesController::class, 'saveMeetingMinute']);
            Route::post('/save-room-wiki', [MessageWikisController::class, 'saveWiki']);
            Route::post('/save-room-daily-report', [MessageDailyReportsController::class, 'saveDailyReport']);
            Route::delete('/delete-room-note/{id}', [MessageNotesController::class, 'deleteChatNote']);
            Route::delete('/delete-room-note-comment/{note_id}/comment/{id}', [MessageNotesController::class, 'deleteChatNoteComment']);
            Route::delete('/delete-room-meeting-minute/{id}', [MessageMeetingMinutesController::class, 'deleteMeetingMinute']);
            Route::delete('/delete-room-wiki/{id}', [MessageWikisController::class, 'deleteWiki']);
            Route::delete('/delete-room-daily-report/{id}', [MessageDailyReportsController::class, 'deleteDailyReport']);
            Route::get('/room-add-member-users', [MessageController::class, 'getAddMemberList']);
            Route::post('/add-room-member', [MessageController::class, 'addMemberToRoom']);
            Route::delete('/remove-room-member', [MessageController::class, 'removeRoomMember']);
            Route::patch('/save-room-settings', [MessageController::class, 'saveRoomSettings']);

            Route::group(['prefix' => 'docua'], function () {
                Route::get('/notes', [NoteController::class, 'getNotes']);
                Route::post('/save-notes', [NoteController::class, 'saveNotes']);
                Route::get('/note/{id}', [NoteController::class, 'getNoteData']);
                Route::delete('/remove-note/{id}', [NoteController::class, 'deleteNote']);
                Route::get('/notes-category', [NoteCategoryController::class, 'getNoteCategories']);
                Route::post('/save-category', [NoteCategoryController::class, 'saveNoteCategories']);
                Route::get('/category/{id}', [NoteCategoryController::class, 'getNoteCategoryData']);
                Route::delete('/remove-category/{id}', [NoteCategoryController::class, 'deleteNoteCategory']);
            });
            //Category
            Route::get('/categories', [CategoryController::class, 'getCategory']);

            //Folder and File Managemenet
            Route::get('/folders/{connection_id}/{folder_id?}',  [FolderManagementController::class, 'getFolder']);
            Route::post('/folders',  [FolderManagementController::class, 'saveFolder']);
            Route::post('/files',  [FileManagementController::class, 'saveFile']);
            Route::delete('/folders/{connection_id}/{folder_id}', [FolderManagementController::class, 'deleteFolder']);
            Route::delete('/files/{connection_id}/{id}', [FileManagementController::class, 'deleteFile']);

            // User installed application
            Route::get('/user-installed-applications', [ApplicationController::class, 'getUserApplications']);
            // Applications
            Route::get('/applications', [ApplicationController::class, 'getAllApplications']);
        });
    });
});
