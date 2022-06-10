<?php

namespace App\Helpers;

class SQLQueryHelper
{
    /**
     * @param $user_id
     * @param string $filter {"archived" | "unread" | "all"}
     * @param string $search
     * @param int $offset
     * @param int $limit
     * @return string
     */
    public static function roomListQuery($user_id, $filter = "all", $search = "", $offset = 0, $limit = 100)
    {
        if(!$filter || trim($filter) === "") {
            $filter = "all";
        }

        if(!$search || trim($search) === "") {
            $search = "";
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
                                 (SELECT CASE
                                    WHEN MC.room_type = 'one-to-one' THEN (SELECT display_name FROM users U WHERE U.id = (SELECT MCU.user_id FROM message_connection_users MCU WHERE MCU.connection_id = MC.id AND MCU.user_id <> 1 LIMIT 1))
                                    END)                                              AS opponent_title,
                                MC.created_at                                         AS created_at,
                                MC.updated_at                                         AS updated_at
                         FROM message_connections MC
                                  INNER JOIN message_connection_users MCU ON MCU.connection_id = MC.id
                         WHERE MCU.user_id = " . $user_id . ") M
                WHERE M.id <> 0 ";

        if($search !== "") {
            $sql .= " AND (M.room_title LIKE '%" . $search . "%' OR M.opponent_title LIKE '%" . $search . "%')";
        }

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
