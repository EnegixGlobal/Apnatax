<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        checklogin();
    }

    public function index() {
        $data['title']='Chat';
        $user=getuser();
        $data['chats']=$this->chat->getchatusers($user['id']);
        $sender_id = $user['id'];
        $receiver_id = $this->input->get('receiver_id');

        $data['receiver_id'] = $receiver_id;
        if($this->session->role=='admin'){
            $where="t1.role!='admin'";
        }
        else{
            $where="t1.role='customer' or t1.id=1";
        }
        
        $data['users']=$this->account->getusers($where);

        $data['bottom_script']=array('file'=>['includes/js/chat.js']);
        $this->template->load('chat','chat',$data);
    }

    public function send_message() {
        $user=getuser();
        $sender_id = $user['id'];
        $receiver_id = $this->input->post('receiver_id');
        $getreceiver=$this->account->getuser(["md5(concat('user-',id))"=>$receiver_id]);
        if($getreceiver['status']===true){
            $receiver=$getreceiver['user'];
            $message = $this->input->post('message');

            $data = array(
                'sender_id' => $sender_id,
                'receiver_id' => $receiver['id'],
                'message' => $message
            );

            $this->chat->insert_chat($data);
            echo json_encode(['status' => 'success']);
        }
        else{
            echo json_encode(['status' => 'error']);
        }
    }

    public function get_messages() {
        $result['user']='';
        $result['count']=0;
        $result['chat']=array();
        $user=getuser();
        $sender_id = $user['id'];
        $receiver_id = $this->input->get('receiver_id');
        $getreceiver=$this->account->getuser(["md5(concat('user-',id))"=>$receiver_id]);
        if($getreceiver['status']===true){
            $receiver=$getreceiver['user'];
            $count=$this->db->get_where('chats',['sender_id'=>$receiver['id'],'receiver_id'=>$sender_id,
                                                     'status'=>0])->num_rows();
            $this->db->update('chats',['status'=>1],['sender_id'=>$receiver['id'], 'receiver_id'=>$sender_id]);
            $user=$getreceiver['user']['name'];
            $chats = $this->chat->get_chats($sender_id, $receiver['id']);
            $result['user']=$user;
            $result['count']=$count;
            $result['chat']=$chats;
        }
        echo json_encode($result);
    }

    /**
     * Delete a single chat message
     * Only admin can delete chats
     */
    public function delete_chat() {
        if($this->session->role != 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $chat_id = $this->input->post('chat_id');
        if(empty($chat_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Chat ID is required']);
            return;
        }

        $result = $this->chat->delete_chat($chat_id);
        if($result) {
            echo json_encode(['status' => 'success', 'message' => 'Chat deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete chat']);
        }
    }

    /**
     * Delete entire conversation between current user and another user
     * Only admin can delete conversations
     */
    public function delete_conversation() {
        if($this->session->role != 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $user = getuser();
        $current_user_id = $user['id'];
        $receiver_id = $this->input->post('receiver_id');
        
        if(empty($receiver_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Receiver ID is required']);
            return;
        }

        $getreceiver = $this->account->getuser(["md5(concat('user-',id))" => $receiver_id]);
        if($getreceiver['status'] === true) {
            $receiver = $getreceiver['user'];
            $result = $this->chat->delete_conversation($current_user_id, $receiver['id']);
            if($result) {
                echo json_encode(['status' => 'success', 'message' => 'Conversation deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete conversation']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid receiver']);
        }
    }

    /**
     * Delete all chats from database
     * Only admin can delete all chats
     */
    public function delete_all_chats() {
        if($this->session->role != 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $confirm = $this->input->post('confirm');
        if($confirm !== 'yes') {
            echo json_encode(['status' => 'error', 'message' => 'Confirmation required']);
            return;
        }

        $result = $this->chat->delete_all_chats();
        if($result) {
            echo json_encode(['status' => 'success', 'message' => 'All chats deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete all chats']);
        }
    }

    /**
     * Delete all chats for a specific user ID
     * Only admin can delete user chats
     */
    public function delete_user_chats() {
        if($this->session->role != 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $user_id = $this->input->post('user_id');
        if(empty($user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
            return;
        }

        // Verify user exists
        $getuser = $this->account->getuser(["md5(concat('user-',id))" => $user_id]);
        if($getuser['status'] === true) {
            $user = $getuser['user'];
            $result = $this->chat->delete_user_chats($user['id']);
            if($result) {
                echo json_encode(['status' => 'success', 'message' => 'All chats for user deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete user chats']);
            }
        } else {
            // Try direct user ID if hash doesn't work
            if(is_numeric($user_id)) {
                $result = $this->chat->delete_user_chats($user_id);
                if($result) {
                    echo json_encode(['status' => 'success', 'message' => 'All chats for user deleted successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete user chats']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
            }
        }
    }
}

