<?php
class Chat_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_chats($sender_id, $receiver_id) {
        $columns="*,md5(sender_id) as enc_sender_id,added_on,DATE_FORMAT(added_on, '%d-%m-%Y') as date,
                    DATE_FORMAT(added_on, '%h:%i %p') as time, ";
        $columns.="CASE WHEN sender_id='$sender_id' THEN 'SENT' WHEN sender_id='$receiver_id' THEN 'RECEIVED' END as type";
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
        //echo $this->db->last_query();
        return $query->result_array();
    }

    public function insert_chat($data) {
        $data['added_on']=$data['updated_on']=date('Y-m-d H:i:s');
        return $this->db->insert('chats', $data);
    }

    public function getchatusers($user_id) {
        $sql1="SELECT
                    `t2`.`id`,
                    `t2`.`name`,
                    `t1`.`added_on`
                FROM
                    `tf_chats` `t1`
                JOIN `tf_users` `t2` ON
                    `t1`.`receiver_id` = `t2`.`id`
                WHERE
                    `t1`.`sender_id` = '$user_id'
                GROUP BY
                    `t1`.`receiver_id`";
        
        $sql2="SELECT
                    `t2`.`id`,
                    `t2`.`name`,
                    `t1`.`added_on`
                FROM
                    `tf_chats` `t1`
                JOIN `tf_users` `t2` ON
                    `t1`.`sender_id` = `t2`.`id`
                WHERE
                    `t1`.`receiver_id` = '$user_id'
                GROUP BY
                    `t1`.`sender_id`";
        $sql=$sql1.' UNION '.$sql2.' ORDER BY `added_on` desc';
        $sql="SELECT id,name,added_on from ($sql) as result GROUP BY id";
        $query=$this->db->query($sql);
        $array=$query->result_array();
        if(!empty($array)){
            foreach($array as $key=>$value){
                $count=$this->db->get_where('chats',['sender_id'=>$value['id'],'receiver_id'=>$user_id,
                                                     'status'=>0])->num_rows();
                $array[$key]['count']=$count;
            }
        }
        return $array;
    }
    
}
