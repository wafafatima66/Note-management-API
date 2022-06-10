<?php

namespace App\Helpers;

class SQLQueryHelper
{
    /**
     * @param $user_id
     * @param string $filter {"archived" | "unread" | "all"}
     * @param int $offset
     * @param int $limit
     * @return string
     */
    public static function roomListQuery($user_id, $filter = "all", $offset = 0, $limit = 100)
    {
        if(!$filter || $filter === "") {
            $filter = "all";
        }

        $sql = "SELECT *
                FROM (
                         SELECT MC.id                                                 AS id,
                                MC.room_title                                         AS room_title,
                                MC.room_type                                          AS room_type,
                                (SELECT COUNT(*)
                                 FROM message_seen_status MSS
                                 WHERE MSS.message_connection_id = MC.id
                                   AND MSS.receiver_id = " . $user_id . "
                                   AND MSS.seen_by_receiver = 0)                      AS unread,
                                (SELECT COUNT(*)
                                 FROM message_connection_archived MCA
                                 WHERE MCA.connection_id = MC.id AND MCA.user_id = " . $user_id . ") AS archived,
                                MC.created_at                                         AS created_at,
                                MC.updated_at                                         AS updated_at
                         FROM message_connections MC
                                  INNER JOIN message_connection_users MCU ON MCU.connection_id = MC.id
                         WHERE MCU.user_id = " . $user_id . ") M
                WHERE M.id <> 0 ";

        if ($filter === "all") {
            $sql .= " AND M.archived = 0";
        } elseif ($filter === "archived") {
            $sql .= " AND M.archived <> 0";
        } elseif ($filter === "unread") {
            $sql .= " AND M.unread > 0";
        }

        $sql .= " ORDER BY M.updated_at DESC LIMIT " . $offset . ", " . $limit . ";";

        return $sql;
    }
}
