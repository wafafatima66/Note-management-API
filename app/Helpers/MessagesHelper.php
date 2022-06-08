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

        if ($room->exists()) {
            $room = @$room->first();

            $roomDetails = (object)[
                'title' => '',
                'user' => null,
                'members_count' => 0,
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
                $roomDetails->members_count = (int) MessageConnectionUser::where('connection_id', '=', @$room->id)->count();
            }

            return $roomDetails;
        }

        return null;
    }
}
