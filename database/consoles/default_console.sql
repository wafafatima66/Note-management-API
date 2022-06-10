/*SELECT *
FROM (
         SELECT MC.id                            AS id,
                MC.room_title                    AS room_title,
                MC.room_type                     AS room_type,
                (SELECT COUNT(*)
                 FROM message_seen_status MSS
                 WHERE MSS.message_connection_id = MC.id
                   AND MSS.receiver_id = 1
                   AND MSS.seen_by_receiver = 0) AS unread,
                (SELECT COUNT(*)
                 FROM message_connection_archived MCA
                 WHERE MCA.connection_id = MC.id
                   AND MCA.user_id = 1)          AS archived,
                (SELECT CASE
                    WHEN MC.room_type = 'one-to-one' THEN (SELECT display_name FROM users U WHERE U.id = (SELECT MCU.user_id FROM message_connection_users MCU WHERE MCU.connection_id = MC.id AND MCU.user_id <> 1 LIMIT 1))
                    END)                        AS opponent_title,
                MC.created_at                    AS created_at,
                MC.updated_at                    AS updated_at
         FROM message_connections MC
                  INNER JOIN message_connection_users MCU ON MCU.connection_id = MC.id
         WHERE MCU.user_id = 1) M
WHERE M.id <> 0
#   AND M.archived = 0
  AND M.unread = 0
ORDER BY M.updated_at DESC
LIMIT 0, 100*/



# TRUNCATE TABLE message_connections;
# TRUNCATE TABLE message_seen_status;
# TRUNCATE TABLE message_connection_users;
# TRUNCATE TABLE messages;
# TRUNCATE TABLE message_attachments;
