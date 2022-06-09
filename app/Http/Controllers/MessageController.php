<?php

namespace App\Http\Controllers;

use App\Helpers\MessagesHelper;
use App\Helpers\SQLQueryHelper;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;
use App\Models\MessageSeenStatus;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Get all the message rooms
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRooms()
    {
        try {
            $auth_user = auth()->user();
            $rooms = DB::select(SQLQueryHelper::roomListQuery($auth_user->id));

            // Bind the room information
            if (count($rooms) > 0) {
                foreach ($rooms as $room) {
                    $room->info = MessagesHelper::getRoomDetails($room->id);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Message rooms fetched successfully!',
                'data' => $rooms,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get room data by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomData($id)
    {
        try {
            $room = MessagesHelper::getRoomDetails($id);

            if ($room !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room data fetched successfully!',
                    'data' => $room,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No room found!',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get room data by user id
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomDataByUser($user_id)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();
            $room = MessagesHelper::getRoomInfoByUserId($user_id);

            if ($room === null && (int)$auth_user->id !== (int)$user_id) {
                $room = MessagesHelper::createRoom('one-to-one', null, [$auth_user->id, $user_id]);
            }

            if ($room !== null) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Room data fetched successfully!!',
                    'data' => $room,
                ], 200);
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No room found!',
            ], 404);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all the messages under a room
     * @param $connection_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($connection_id)
    {
        try {
            if ($connection_id > 0) {
                $messages = [
                    'older' => MessagesHelper::getMessages($connection_id, "older"),
                    'yesterday' => MessagesHelper::getMessages($connection_id, "yesterday"),
                    'today' => MessagesHelper::getMessages($connection_id, "today"),
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Message threads fetched successfully!',
                    'data' => $messages,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection not found!',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the files under a conversation room
     * @param $connection_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSharedFiles($connection_id)
    {
        try {
            if ($connection_id > 0) {
                $files = MessageAttachment::where('connection_id', $connection_id)->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Shared files fetched successfully!',
                    'data' => $files,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection not found!',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a conversation room
     * One to one || Group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRoom(Request $request)
    {
        try {
            DB::beginTransaction();
            $room_type = $request->input('room_type');
            $room_title = $request->input('room_title');
            $user_id_array = $request->input('users');

            $roomData = MessagesHelper::createRoom($room_type, $room_title, $user_id_array);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Room data created & fetched successfully!',
                'data' => $roomData,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message to a room
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            $auth_user = auth()->user();
            $connection_id = (int)$request->input('connection_id');
            $message_text = $request->input('message');

            $connection = MessageConnection::where('id', $connection_id);

            if ($connection->exists()) {
                DB::beginTransaction();
                $connection = $connection->first();

                $message = new Message();
                $message->connection_id = $connection_id;
                $message->user_id = $auth_user->id;
                $message->message = $message_text;
                $message->save();

                // create receiver notifications
                $connection_users = MessageConnectionUser::where('connection_id', '=', $connection_id)
                    ->where('user_id', '<>', $auth_user->id);
                if ($connection_users->exists()) {
                    $connection_users = $connection_users->get();

                    foreach ($connection_users as $connection_user) {
                        $message_seen_status = new MessageSeenStatus();
                        $message_seen_status->message_connection_id = $connection_id;
                        $message_seen_status->message_id = $message->id;
                        $message_seen_status->sender_id = $auth_user->id;
                        $message_seen_status->receiver_id = $connection_user->user_id;
                        $message_seen_status->save();
                    }
                }

                if ($request->hasfile('file')) {
                    $baseFolderName = '/messages/';

                    $files = $request->file('file');
                    foreach ($files as $file) {
                        $original_name = $file->getClientOriginalName();
                        $_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                        $_file = $baseFolderName . $_file;
                        Storage::disk('local')->put('/public/uploads/' . $_file, file_get_contents($file));

                        $attachment = new MessageAttachment();
                        $attachment->connection_id = $connection_id;
                        $attachment->message_id = $message->id;
                        $attachment->user_id = $auth_user->id;
                        $attachment->file_url = $_file;
                        $attachment->name = $original_name;
                        $attachment->save();
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully!',
                ]);
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Connection not found!',
            ], 404);

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
