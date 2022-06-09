<?php

namespace App\Http\Controllers;

use App\Helpers\MessagesHelper;
use App\Helpers\SQLQueryHelper;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;

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
            $room_type = $request->input('room_type');
            $room_title = $request->input('room_title');
            $user_id_array = $request->input('users');

            if (count($user_id_array) > 0 && ($room_type === "one-to-one" || "group")) {
                $message_connection = new MessageConnection();
                $message_connection->room_type = $room_type;
                $message_connection->room_title = $room_title;
                $message_connection->save();

                // register the user list under this connection
                foreach ($user_id_array as $user_id) {
                    $message_connection_user = new MessageConnectionUser();
                    $message_connection_user->connection_id = $message_connection->id;
                    $message_connection_user->user_id = $user_id;
                    $message_connection_user->save();
                }
            }

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function replyToThread(Request $request)
    {
        try {
            $auth_user = auth()->user();
            $connection_id = (int)$request->input('connection_id');
            $message_text = $request->input('message');

            $connection = MessageConnection::where('id', $connection_id);

            if ($connection->exists()) {
                $connection = $connection->first();

                if ($connection->sender_id === $auth_user->id) {
                    $receiver_id = $connection->receiver_id;
                } else {
                    $receiver_id = $connection->sender_id;
                }

                $message = new Message();
                $message->connection_id = $connection_id;
                $message->sender_id = $auth_user->id;
                $message->receiver_id = $receiver_id;
                $message->message = $message_text;
                $message->seen_by_receiver = 0;
                $message->save();


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
                        $attachment->file_url = $_file;
                        $attachment->name = $original_name;
                        $attachment->save();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully!',
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
}
