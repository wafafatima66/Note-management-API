<?php

namespace App\Helpers;

class SQLQueryHelper
{
    public static function roomListQuery($user_id, $offset = 0, $limit = 100)
    {
        return
            "SELECT *,
       (SELECT COUNT(*) FROM message_seen_status MSS WHERE MSS.message_connection_id = MC.id AND MSS.receiver_id = " . $user_id . " AND MSS.seen_by_receiver = 0) AS unread,
       MC.id AS id
FROM message_connections MC
         INNER JOIN message_connection_users MCU ON MCU.connection_id = MC.id
WHERE MCU.user_id = " . $user_id . "
ORDER BY MC.updated_at DESC LIMIT " . $offset . ", " . $limit . ";";
    }
}
