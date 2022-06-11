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
        function createRoom($type, $title, $userIdArray)
        {
            $message_connection = new MessageConnection();
            $message_connection->room_type = $type;
            $message_connection->room_title = $title;
            $message_connection->save();

            foreach ($userIdArray as $user_id) {
                $message_connection_user = new MessageConnectionUser();
                $message_connection_user->connection_id = $message_connection->id;
                $message_connection_user->user_id = $user_id;
                $message_connection_user->save();
            }
        }

        createRoom('one-to-one', '', [1, 2]);
        createRoom('one-to-one', '', [1, 3]);
        createRoom('one-to-one', '', [2, 3]);
        createRoom('group', 'Software Development', [1, 2, 3]);
    }
}
