<?php
class Chat_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function get_chats($sender_id, $receiver_id)
    {
        try {
            // Escape values to prevent SQL injection
            $sender_id = intval($sender_id);
            $receiver_id = intval($receiver_id);

            $columns = "*,md5(sender_id) as enc_sender_id,added_on,DATE_FORMAT(added_on, '%d-%m-%Y') as date,
                        DATE_FORMAT(added_on, '%h:%i %p') as time, ";
            $columns .= "CASE WHEN sender_id='$sender_id' THEN 'SENT' WHEN sender_id='$receiver_id' THEN 'RECEIVED' END as type";
            $this->db->select($columns);
            $this->db->group_start();
            $this->db->where('sender_id', $sender_id);
            $this->db->where('receiver_id', $receiver_id);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->or_where('sender_id', $receiver_id);
            $this->db->where('receiver_id', $sender_id);
            $this->db->group_end();
            $this->db->order_by('added_on', 'ASC');
            $query = $this->db->get('chats');

            if ($query === false) {
                log_message('error', 'Database query failed in get_chats: ' . $this->db->error()['message']);
                return array();
            }

            //echo $this->db->last_query();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error in get_chats: ' . $e->getMessage());
            return array();
        }
    }

    public function insert_chat($data)
    {
        $data['added_on'] = $data['updated_on'] = date('Y-m-d H:i:s');
        return $this->db->insert('chats', $data);
    }

    public function getchatusers($user_id)
    {
        try {
            // Validate and sanitize user_id
            $user_id = intval($user_id);
            if ($user_id <= 0) {
                return array();
            }

            // Escape user_id to prevent SQL injection
            $escaped_user_id = $this->db->escape($user_id);

            // Get table names with prefix
            $chats_table = $this->db->dbprefix('chats');
            $users_table = $this->db->dbprefix('users');

            $sql1 = "SELECT
                        `t2`.`id`,
                        `t2`.`name`,
                        MAX(`t1`.`added_on`) as `added_on`
                    FROM
                        `{$chats_table}` `t1`
                    JOIN `{$users_table}` `t2` ON
                        `t1`.`receiver_id` = `t2`.`id`
                    WHERE
                        `t1`.`sender_id` = {$escaped_user_id}
                    GROUP BY
                        `t2`.`id`, `t2`.`name`";

            $sql2 = "SELECT
                        `t2`.`id`,
                        `t2`.`name`,
                        MAX(`t1`.`added_on`) as `added_on`
                    FROM
                        `{$chats_table}` `t1`
                    JOIN `{$users_table}` `t2` ON
                        `t1`.`sender_id` = `t2`.`id`
                    WHERE
                        `t1`.`receiver_id` = {$escaped_user_id}
                    GROUP BY
                        `t2`.`id`, `t2`.`name`";
            $sql = $sql1 . ' UNION ' . $sql2 . ' ORDER BY `added_on` desc';
            $sql = "SELECT id, name, MAX(added_on) as added_on from ($sql) as result GROUP BY id, name";
            $query = $this->db->query($sql);

            if ($query === false) {
                $error = $this->db->error();
                log_message('error', 'Database query failed in getchatusers: ' . (isset($error['message']) ? $error['message'] : 'Unknown error'));
                return array();
            }

            $array = $query->result_array();
            if (!empty($array)) {
                foreach ($array as $key => $value) {
                    $count = $this->db->get_where('chats', [
                        'sender_id' => $value['id'],
                        'receiver_id' => $user_id,
                        'status' => 0
                    ])->num_rows();
                    $array[$key]['count'] = $count;
                }
            }
            return $array;
        } catch (Exception $e) {
            log_message('error', 'Error in getchatusers: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Delete a single chat message by ID
     * @param int $chat_id
     * @return bool
     */
    public function delete_chat($chat_id)
    {
        $this->db->where('id', $chat_id);
        return $this->db->delete('chats');
    }

    /**
     * Delete all chats between two users (conversation)
     * @param int $user1_id
     * @param int $user2_id
     * @return bool
     */
    public function delete_conversation($user1_id, $user2_id)
    {
        $this->db->group_start();
        $this->db->where('sender_id', $user1_id);
        $this->db->where('receiver_id', $user2_id);
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('sender_id', $user2_id);
        $this->db->where('receiver_id', $user1_id);
        $this->db->group_end();
        return $this->db->delete('chats');
    }

    /**
     * Delete all chats from database (Admin only)
     * @return bool
     */
    public function delete_all_chats()
    {
        return $this->db->empty_table('chats');
    }

    /**
     * Delete all chats for a specific user (both as sender and receiver)
     * @param int $user_id
     * @return bool
     */
    public function delete_user_chats($user_id)
    {
        $this->db->group_start();
        $this->db->where('sender_id', $user_id);
        $this->db->or_where('receiver_id', $user_id);
        $this->db->group_end();
        return $this->db->delete('chats');
    }

    /**
     * Get total count of all chats
     * @return int
     */
    public function get_total_chats_count()
    {
        return $this->db->count_all_results('chats');
    }

    /**
     * Get count of chats for a specific user
     * @param int $user_id
     * @return int
     */
    public function get_user_chats_count($user_id)
    {
        $this->db->group_start();
        $this->db->where('sender_id', $user_id);
        $this->db->or_where('receiver_id', $user_id);
        $this->db->group_end();
        return $this->db->count_all_results('chats');
    }
}
