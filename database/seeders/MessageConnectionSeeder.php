<?php

namespace Database\Seeders;

use App\Helpers\MessagesHelper;
use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        function createRoom($type, $title, $userIdArray, $isVisible)
        {
            $message_connection = new MessageConnection();
            $message_connection->room_type = $type;
            $message_connection->room_title = $title;
            $message_connection->is_visible = $isVisible;
            $message_connection->save();

            foreach ($userIdArray as $user_id) {
                $message_connection_user = new MessageConnectionUser();
                $message_connection_user->connection_id = $message_connection->id;
                $message_connection_user->user_id = $user_id;
                $message_connection_user->save();
            }
        }

        createRoom('one-to-one', '', [1, 2], 0);
        createRoom('one-to-one', '', [1, 3], 0);
        createRoom('one-to-one', '', [1, 4], 0);
        createRoom('one-to-one', '', [1, 5], 0);
        createRoom('one-to-one', '', [2, 3], 0);
        createRoom('one-to-one', '', [2, 4], 0);
        createRoom('one-to-one', '', [2, 5], 0);
        createRoom('one-to-one', '', [3, 4], 0);
        createRoom('one-to-one', '', [3, 5], 0);
        createRoom('group', 'common', [1, 2, 3, 4, 5], 0);
        createRoom('group', 'general', [1, 2, 3, 4, 5], 1);
    }
}
