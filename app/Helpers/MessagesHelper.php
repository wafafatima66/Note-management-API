<?php

namespace App\Helpers;

use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;
use App\Models\User;

class MessagesHelper
{
    public static function getRoomDetails($room_id)
    {
        $auth_user = auth()->user();
        $room = MessageConnection::where('id', '=', $room_id);
        $roomDetails = (object)[
            'title' => '',
            'user' => null,
        ];

        if ($room->exists()) {
            $room = @$room->first();

            if ($room->room_type === "one-to-one") {
                // One to one chat

                $connection_user = MessageConnectionUser::where('connection_id', '=', @$room->id)
                    ->where('user_id', '<>', $auth_user->id);

                if ($connection_user->exists()) {
                    $opponentUser = User::where('id', '=', @$connection_user->first()->id);

                    if ($opponentUser->exists()) {
                        $opponentUser = @$opponentUser->first();
                        $roomDetails->title = @$opponentUser->first_name . " " . @$opponentUser->last_name;
                        $roomDetails->user = $opponentUser;
                    }
                }

            } elseif ($room->room_type === "group") {
                // Group chat
                $roomDetails->title = @$room->room_title;
                $roomDetails->user = null;
            }
        }

        return $roomDetails;
    }
}
