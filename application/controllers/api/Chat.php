<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Chat extends RestController{
	function __construct(){
		parent::__construct();
        logrequest();
	}
    
    public function getchats_post(){
        $token=$this->post('token');
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $chats=$this->chat->getchatusers($user['id']);
                if(!empty($chats)){
                    $chats = array_map(function($entry) {
                        $entry['id']=md5('user-'.$entry['id']);
                        return $entry;
                    }, $chats);
                }
                $this->response([
                        'status' => true,
                        'chats' => $chats], RestController::HTTP_OK);
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
    public function newchat_post(){
        $token=$this->post('token');
        $message=$this->post('message');
        if(!empty($token) && !empty($message)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $sender_id=$user['id'];
                $customer=$this->customer->getcustomers(['t1.user_id'=>$user['id']],'single');
                if($customer['added_by']===NULL){
                    $receiver_id=1;
                }
                else{
                    $receiver_id=$customer['added_by'];
                }
                $data = array(
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'message' => $message
                );

                $result=$this->chat->insert_chat($data);
                if($result){
                    $this->response([
                            'status' => true,
                            'message' => "Message Sent"], RestController::HTTP_OK);
                }	
                else{
                    $error=$this->db->error();
                    $this->response([
                        'status' => false,
                        'message' => $error['message']], RestController::HTTP_OK);
                }
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
    public function getchatmessages_post(){
        $token=$this->post('token');
        $receiver_id=$this->post('id');
        if(!empty($token) && !empty($receiver_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $sender_id = $user['id'];
                $getreceiver=$this->account->getuser(["md5(concat('user-',id))"=>$receiver_id]);
                $chats=array();
                if($getreceiver['status']===true){
                    $receiver=$getreceiver['user'];
                    $this->db->update('chats',['status'=>1],['sender_id'=>$receiver['id'], 'receiver_id'=>$sender_id]);
                    $user=$getreceiver['user']['name'];
                    $chats = $this->chat->get_chats($sender_id, $receiver['id']);
                }
                $this->response([
                        'status' => true,
                        'chats' => $chats], RestController::HTTP_OK);
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
    public function sendmessage_post(){
        $token=$this->post('token');
        $receiver_id=$this->post('id');
        $message=$this->post('message');
        if(!empty($token) && !empty($receiver_id) && !empty($message)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $sender_id = $user['id'];
                $getreceiver=$this->account->getuser(["md5(concat('user-',id))"=>$receiver_id]);
                if($getreceiver['status']===true){
                    $receiver=$getreceiver['user'];

                    $data = array(
                        'sender_id' => $sender_id,
                        'receiver_id' => $receiver['id'],
                        'message' => $message
                    );

                    $result=$this->chat->insert_chat($data);
                    if($result){
                        $this->response([
                                'status' => true,
                                'message' => "Message Sent"], RestController::HTTP_OK);
                    }	
                    else{
                        $error=$this->db->error();
                        $this->response([
                            'status' => false,
                            'message' => $error['message']], RestController::HTTP_OK);
                    }
                }		
                else{
                    $this->response([
                        'status' => false,
                        'message' => "User not Logged In!"], RestController::HTTP_OK);
                }
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
}