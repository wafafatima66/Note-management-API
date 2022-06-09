/*SELECT *,
       (SELECT COUNT(*) FROM message_seen_status MSS WHERE MSS.message_connection_id = MC.id AND MSS.receiver_id = 1 AND MSS.seen_by_receiver = 0) AS unread,
       MC.id AS id
FROM message_connections MC
         INNER JOIN message_connection_users MCU ON MCU.connection_id = MC.id
WHERE MCU.user_id = 1
ORDER BY MC.updated_at DESC LIMIT 0, 100
*/


SELECT * FROM message_connection_users MCU INNER JOIN message_connections MC ON MCU.connection_id = MC.id WHERE user_id = 1;


# TRUNCATE TABLE message_connections;
# TRUNCATE TABLE message_seen_status;
# TRUNCATE TABLE message_connection_users;
# TRUNCATE TABLE messages;
# TRUNCATE TABLE message_attachments;
