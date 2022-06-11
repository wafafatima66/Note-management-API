<?php

namespace App\Helpers;

use App\Models\Message;
use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;
use App\Models\MessageSeenStatus;
use App\Models\User;
use Illuminate\Support\Carbon;

class MessagesHelper
{
    /**
     * Get a room's detail
     * @param $room_id
     * @return object|null
     */
    public static function getRoomDetails($room_id)
    {
        $auth_user = auth()->user();
        $room = MessageConnection::where('id', '=', $room_id);

        if ($room->exists()) {
            $room = @$room->first();

            $roomDetails = (object)[
                'id' => (int)$room_id,
                'title' => '',
                'user' => null,
                'members_count' => 0,
                'room_type' => $room->room_type,
            ];

            if ($room->room_type === "one-to-one") {
                // One to one chat
                $roomDetails->members_count = 1; // By default the auth user is the only member

                $connectionUser = MessageConnectionUser::where('connection_id', '=', @$room->id)
                    ->where('user_id', '<>', $auth_user->id);

                if ($connectionUser->exists()) {
                    $opponentUser = User::where('id', '=', @$connectionUser->first()->user_id);

                    if ($opponentUser->exists()) {
                        $opponentUser = @$opponentUser->first();
                        $roomDetails->title = @$opponentUser->first_name . " " . @$opponentUser->last_name;
                        $roomDetails->user = $opponentUser;
                        $roomDetails->members_count = 2; // As we found the opponent user as 2nd member
                    }
                }

            } elseif ($room->room_type === "group") {
                // Group chat
                $roomDetails->title = @$room->room_title;
                $roomDetails->user = null;
                $roomDetails->members_count = (int)MessageConnectionUser::where('connection_id', '=', @$room->id)->count();
            }

            return $roomDetails;
        }

        return null;
    }

    /**
     * Get the messages
     * @param $connection_id
     * @param string $filter {"today" | "yesterday" | "older"}
     * @return mixed
     */
    public static function getMessages($connection_id, $filter = "today")
    {
        $auth_user = auth()->user();
        $threads = Message::where('connection_id', $connection_id);

        if ($filter === "today") {
            $threads = $threads->whereDate('created_at', Carbon::today());
        } elseif ($filter === "yesterday") {
            $threads = $threads->whereDate('created_at', Carbon::yesterday());
        } elseif ($filter === "older") {
            $threads = $threads->whereDate('created_at', '<=', Carbon::now()->subDays(2)->toDateTimeString());
        }

        $threads = $threads->with('attachments')->with('sender')->orderBy('id', 'asc');

        $threads = $threads->get();

        // Detect the opponent user
        foreach ($threads as $thread) {
            $thread->is_opponent = ($thread->user_id === $auth_user->id) ? false : true;

            // Update message seen
            if ($thread->user_id !== $auth_user->id) {
                MessageSeenStatus::where('message_connection_id', '=', $connection_id)
                    ->where('message_id', '=', $thread->id)
                    ->where('receiver_id', '=', $auth_user->id)
                    ->update([
                        'seen_by_receiver' => 1,
                        'seen_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $thread->date_time = $thread->created_at !== null ? date('h:ia - d M, Y', strtotime($thread->created_at)) : "";
            $thread->display_time = $thread->created_at !== null ? date('h:i a', strtotime($thread->created_at)) : "";
        }

        return $threads;
    }

    /**
     * Get a room info by user id
     * @param $opponent_user_id
     * @return object|null
     */
    public static function getRoomInfoByUserId($opponent_user_id)
    {
        $auth_user = auth()->user();

        $my_connections = MessageConnectionUser::where('user_id', '=', $auth_user->id);

        if ((int)$opponent_user_id !== (int)$auth_user->id && $my_connections->exists()) {
            $my_connections = $my_connections->get();

            foreach ($my_connections as $connection) {
                $opponentConnection = MessageConnectionUser::where('user_id', '=', $opponent_user_id)
                    ->where('connection_id', '=', $connection->connection_id);

                if ($opponentConnection->exists()) {
                    return self::getRoomDetails($connection->connection_id);
                }
            }
        }

        return null;
    }

    /**
     * Create a room
     * @param $room_type
     * @param $room_title
     * @param $user_id_array
     * @return object|null
     */
    public static function createRoom($room_type, $room_title, $user_id_array)
    {
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


            return self::getRoomDetails($message_connection->id);
        }

        return null;
    }
}
