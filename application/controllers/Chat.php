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
}

