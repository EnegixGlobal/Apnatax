<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Account extends RestController{
	function __construct(){
		parent::__construct();
        logrequest();
	}
    
	public function register_post(){
        $data['name']=$this->post('name');
        $data['mobile']=$data['username']=$this->post('mobile');
        $data['email']=$this->post('email');
        $data['password']=$this->post('password');
        $regid=$this->post('regid');
        $device_id=$this->post('device_id');
        $device_name=$this->post('device_name');
        $data['role']='customer';
        if(!empty($data['name']) && !empty($data['mobile']) && !empty($data['password']) && 
            !empty($device_id) && !empty($device_name)){
            $result=$this->account->register($data);
            if($result['status']===true){  
                $token=md5($result['user_id'].'.'.time().'.'.$data['username']);
                $tokendata=array("user_id"=>$result['user_id'],"token"=>$token,"device_id"=>$device_id,
                                 "device_name"=>$device_name,"regid"=>$regid);
                $verify=$this->account->addtoken($tokendata);
                $where=array("username"=>$data['mobile']);
                $smsresult=$this->sendotp($where);
                if($smsresult['status']===false){

                }
                unset($data['username'],$data['role'],$data['password']);
                $data['user_id']=$result['user_id'];
                $data['old']=$result['old'];
                $result=$this->customer->savecustomer($data);
                $response=array("name"=>$data['name'],"mobile"=>$data['mobile'],"email"=>$data['email'],
                                "token"=>$token,'otp'=>$smsresult['message']);
                $this->response(['status'=>TRUE,'response'=>$response], RestController::HTTP_OK);
            }
            else{
                $error=$result['message'];
                $this->response([
                    'status' => false,
                    'message' => $error], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function login_post(){
        $data['username']=$this->post('mobile');
        $data['password']=$this->post('password');
        $data['regid']=$this->post('regid');
        $data['device_id']=$this->post('device_id');
        $data['device_name']=$this->post('device_name');
        if(!empty($data['username']) && !empty($data['password']) && !empty($data['device_id']) && !empty($data['device_name'])){
            $result=$this->account->login($data);
            if($result['status']===true){
                $user=$result['user'];
                $token=md5($user['id'].'.'.time().'.'.$data['username']);
                $tokendata=array("user_id"=>$user['id'],"token"=>$token,"device_id"=>$data['device_id'],
                                 "device_name"=>$data['device_name'],"regid"=>$data['regid']);
                $verify=$this->account->addtoken($tokendata);
                if($verify===true){
                    $firstLetter = !empty($user['name'])?strtoupper(substr($user['name'], 0, 1)):'T';
                    $photo=!empty($user['photo'])?file_url($user['photo']):base_url('profileimage/?letter='.$firstLetter);
                    $response=array("name"=>$user['name'],"mobile"=>$user['mobile'],"email"=>$user['email'],"photo"=>$photo,
                                    "token"=>$token);
                    $this->response(['status'=>TRUE,'response'=>$response], RestController::HTTP_OK);
                }
                else{
                    $this->response([
                        'status' => FALSE,
                        'message' => $verify
                    ], RestController::HTTP_OK);
                }
            }
            else{
                $error=$result['message'];
                $this->response([
                    'status' => false,
                    'message' => $error], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' =>  "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function verifyotp_post(){
        $token=$this->post('token');
        $otp=$this->post('otp');
        if(!empty($token) && !empty($otp)){
            $verify=$this->account->verify_token($token);
            if($verify!==false){
                $where['username']=$verify['mobile'];
                $result=$this->account->verifyotp($otp,$where);
                if($result['status']===true){
                    $result=$result['result'];
                    $firstLetter = !empty($result['name'])?strtoupper(substr($result['name'], 0, 1)):'T';
                    $photo=!empty($result['photo'])?file_url($result['photo']):base_url('profileimage/?letter='.$firstLetter);
                    $result=array('name'=>$result['name'],'mobile'=>$result['mobile'],'photo'=>$photo);
                    $this->response([
                        'status' => true,
                        'result' => $result], RestController::HTTP_OK);
                }
                else{
                    $error=$result['message'];
                    $this->response([
                        'status' => false,
                        'message' => $error], RestController::HTTP_OK);
                }
            }
            else{
                $this->response([
                    'status' => false,
                    'message' => "Token Invalid"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function sendotptomobile_post(){
        $where['username']=$this->post('mobile');
        $regid=$this->post('regid');
        $device_id=$this->post('device_id');
        $device_name=$this->post('device_name');
        
        if(!empty($where['username']) && !empty($regid) && !empty($device_id) && !empty($device_name)){

            $result=$this->account->getuser($where);
            if($result['status']===true){
                $user=$result['user'];
                $token=md5($user['id'].'.'.time().'.'.$user['username']);
                $tokendata=array("user_id"=>$user['id'],"token"=>$token,"device_id"=>$device_id,
                                 "device_name"=>$device_name,"regid"=>$regid);
                $verify=$this->account->addtoken($tokendata);

                $result=$this->sendotp($where);
                if($result['status']===true){
                    $this->response([
                        'status' => true,
                        'result' => $result['message'],"token"=>$token], RestController::HTTP_OK);
                }
                else{
                    $error=$result['message'];
                    $this->response([
                        'status' => false,
                        'message' => $error], RestController::HTTP_OK);
                }
            }
            else{
                $error=$result['message'];
                $this->response([
                    'status' => false,
                    'message' => $error], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

    public function sendotp($where){
        $array=$this->account->createotp($where);
        if($array['status']===true){
            $result=$array['result'];
            $mobile=$result['mobile'];
            $name=$result['name'];
            $otp=$result['otp'];
            $type=$result['type'];
            //loginotp($mobile,$otp);
            /*if($type!='activate'){
                resetpassword($mobile,$name,$otp);
                //$sms="$otp is your OTP to activate ".PROJECT_NAME." account.";
            }
            else{
                loginotp($mobile,$otp);
                //$sms="$otp is your OTP to login to your ".PROJECT_NAME." account.";
            }*/
            //send_sms($mobile,$sms);
            return array("status"=>true,"message"=>$otp);
        }
        else{
            return $array;
        }
    }
    
    public function resendotp(){
        $mobile=$this->session->mobile;
        $where=array("username"=>$mobile);
        $result=$this->sendotp($where);
    }
    
    public function changepassword_post(){
        $token=$this->post('token');
        $old_password=$this->post('old_password');
        $new_password=$this->post('new_password');
        $repassword=$this->post('repassword');
        
        if(!empty($token) && !empty($old_password) && !empty($new_password)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user)){
                // Verify old password
                $where=array("id"=>$user['id']);
                $dbuser=$this->account->getuser($where);
                if($dbuser['status']===true){
                    $dbuser=$dbuser['user'];
                    $password=$old_password.SITE_SALT.$dbuser['salt'];
                    if(password_verify($password,$dbuser['password'])){
                        // Verify new password matches repassword if provided
                        if(!empty($repassword) && $new_password!=$repassword){
                            $this->response([
                                'status' => false,
                                'message' => "New Password and Confirm Password do not Match!"], RestController::HTTP_OK);
                            return;
                        }
                        // Update password
                        $result=$this->account->updatepassword(array("password"=>$new_password),$where);
                        if($result['status']===true){
                            $this->response([
                                'status' => true,
                                'message' => $result['message']], RestController::HTTP_OK);
                        }
                        else{
                            $this->response([
                                'status' => false,
                                'message' => $result['message']], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "Old Password is Incorrect!"], RestController::HTTP_OK);
                    }
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "User Not Found!"], RestController::HTTP_OK);
                }
            }
            else{
                $this->response([
                    'status' => false,
                    'message' => "User Not Logged In!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
    }
    
}
