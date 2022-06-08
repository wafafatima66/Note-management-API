<?php

namespace App\Http\Controllers;

use App\Helpers\MessagesHelper;
use App\Helpers\SQLQueryHelper;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;
use App\Models\User;
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

            // Detect the opponent user
            if(count($rooms) > 0) {
                foreach ($rooms as $room) {
                    /*$roomType = $room->room_type;

                    if($roomType === "one-to-one") {
                        $connection_user = MessageConnectionUser::where('connection_id', '=', $room->id)
                            ->where('user_id', '<>', $auth_user->id);

                        if($connection_user->exists()) {
                            $opponentUser = User::where('id', '=', $connection_user->first()->id);

                            if($opponentUser->exists()) {
                                $room->opponent_user = $opponentUser->first();
                            }
                        }
                    }*/

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

    public function getRoomData(Request $request)
    {
        try {
            $id = (int)$request->input('id');
            $auth_user = auth()->user();

            if ($id > 0) {
                $connection = MessageConnection::where('id', '=', $id);
            } else {
                // get the initial room
                $connection = MessageConnection::where('sender_id', '=', $auth_user->id)
                    ->orWhere('receiver_id', '=', $auth_user->id);
            }

            $connection = $connection->with('sender')->with('receiver');

            /*if ($connection->exists()) {
                $connection = $connection->first();

                if ($connection->sender->id === $auth_user->id) {
                    $connection->opponent = $connection->receiver;
                } else if ($connection->receiver->id === $auth_user->id) {
                    $connection->opponent = $connection->sender;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Initial room found!',
                    'data' => $connection,
                ]);
            }*/

            return response()->json([
                'success' => false,
                'message' => 'No rooms found!',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getThreads(Request $request)
    {
        try {
            $connection_id = (int)$request->input('connection_id');
            $auth_user = auth()->user();

            if ($connection_id > 0) {
                $threads = Message::where('connection_id', $connection_id)->with('attachments')->orderBy('id', 'asc');

                $threads = $threads->get();

                // Detect the opponent user
                foreach ($threads as $thread) {
                    $thread->is_opponent = ($thread->sender_id === $auth_user->id) ? false : true;

                    // Update message seen
                    if ($thread->sender_id !== $auth_user->id) {
                        Message::where('id', '=', $thread->id)->update([
                            'seen_by_receiver' => 1,
                        ]);
                    }

                    $thread->date_time = $thread->created_at !== null ? date('h:ia - d M, Y', strtotime($thread->created_at)) : "";
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Message threads fetched successfully!',
                    'data' => $threads,
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

    public function getSharedFiles(Request $request)
    {
        try {
            $connection_id = (int)$request->input('connection_id');

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
