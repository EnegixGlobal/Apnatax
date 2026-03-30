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
                if($smsresult['status']===true && !empty($data['email'])){
                    // Email OTP so user can manually enter it in the mobile/web OTP screen.
                    $otp = $smsresult['message'] ?? '';
                    if(!empty($otp)){
                        $this->load->helper('email');
                        $subject = 'OTP for Registration - ' . PROJECT_NAME;
                        $name = !empty($data['name']) ? $data['name'] : $data['email'];
                        $message  = '<p>Hi ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>';
                        $message .= '<p>Your OTP for ' . PROJECT_NAME . ' registration is: <b>' . htmlspecialchars((string)$otp, ENT_QUOTES, 'UTF-8') . '</b>.</p>';
                        $message .= '<p>OTP is valid for 30 minutes.</p>';
                        $message .= '<p>Regards,</p>';
                        $message .= '<p>' . htmlspecialchars(PROJECT_NAME, ENT_QUOTES, 'UTF-8') . '</p>';
                        $mailOk = sendemail($data['email'], $subject, $message);
                        if (!$mailOk) {
                            log_message('error', 'OTP registration email failed for: '.$data['email'].' (see previous sendemail line in log)');
                        }
                    }
                }
                unset($data['username'],$data['role'],$data['password']);
                $data['user_id']=$result['user_id'];
                $data['old']=$result['old'];
                $result=$this->customer->savecustomer($data);
                $response=array("name"=>$data['name'],"mobile"=>$data['mobile'],"email"=>$data['email'],
                                "token"=>$token,'otp'=>'');
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

	public function updateregid_post()
	{
		$token = $this->post('token');
		$regid = $this->post('regid');
		if (empty($token)) {
			$this->response([
				'status' => false,
				'message' => 'Please provide token!',
			], RestController::HTTP_OK);
			return;
		}
		$verify = $this->account->verify_token($token);
		if (empty($verify) || !is_array($verify)) {
			$this->response([
				'status' => false,
				'message' => 'User Not Logged In!',
			], RestController::HTTP_OK);
			return;
		}
		$this->account->updateregid_for_session_token($token, (string) $regid);
		$this->response([
			'status' => true,
			'message' => 'Device registration updated',
		], RestController::HTTP_OK);
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
        $mobile=$this->post('mobile');
        $email=$this->post('email');
        $where=array();
        $device_id=$this->post('device_id');
        $device_name=$this->post('device_name');
        
        if(!empty($device_id) && !empty($device_name) && (!empty($mobile) || !empty($email))){
            if(!empty($email)){
                $where = array('email'=>$email, 'role'=>'customer');
            }
            else{
                $where = array('username'=>$mobile);
            }

            $result=$this->account->getuser($where);
            if($result['status']===true){
                $user=$result['user'];
                $token=md5($user['id'].'.'.time().'.'.$user['username']);
                // `tf_tokens.regid` is NOT NULL in your schema.
                // Forgot-password flow doesn't send FCM regid, so default to empty string.
                $regid = $this->post('regid');
                if ($regid === null) {
                    $regid = '';
                }
                $tokendata=array("user_id"=>$user['id'],"token"=>$token,"device_id"=>$device_id,
                                 "device_name"=>$device_name,"regid"=>$regid);
                $verify=$this->account->addtoken($tokendata);
                // If token insert/activation failed, do not return an unusable token to the app.
                // Otherwise app will navigate to ResetPassword but verify_token() will fail.
                if($verify !== true){
                    log_message('error', '[sendotptomobile_post] addtoken failed err=' . (string)$verify . ' tokenPrefix=' . substr((string)$token, 0, 8));
                    $this->response([
                        'status' => false,
                        'message' => (string)$verify
                    ], RestController::HTTP_OK);
                    return;
                }

                $result=$this->sendotp(array('username'=>$user['mobile']));
                if($result['status']===true){
                    // Email OTP so user can manually enter it in the mobile/web OTP screen.
                    $otp = $result['message'] ?? '';
                    $mailTo = '';
                    if (!empty($email)) {
                        $mailTo = trim($email);
                    } elseif (!empty($user['email'])) {
                        $mailTo = trim((string) $user['email']);
                    }
                    if($mailTo !== '' && !empty($otp)){
                        $this->load->helper('email');
                        $subject = 'OTP for Password Reset - ' . PROJECT_NAME;
                        $name = !empty($user['name']) ? $user['name'] : $mailTo;
                        $message  = '<p>Hi ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>';
                        $message .= '<p>Your OTP to reset password for ' . PROJECT_NAME . ' account is: <b>' . htmlspecialchars((string)$otp, ENT_QUOTES, 'UTF-8') . '</b>.</p>';
                        $message .= '<p>OTP is valid for 30 minutes.</p>';
                        $message .= '<p>Regards,</p>';
                        $message .= '<p>' . htmlspecialchars(PROJECT_NAME, ENT_QUOTES, 'UTF-8') . '</p>';
                        $mailOk = sendemail($mailTo, $subject, $message);
                        if (!$mailOk) {
                            log_message('error', 'OTP forgot-password email failed for: '.$mailTo.' (see previous sendemail line in log)');
                        }
                    }
                    $this->response([
                        'status' => true,
                        // Keep token under response key so mobile app can navigate properly.
                        'response' => ['token' => $token]
                    ], RestController::HTTP_OK);
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

    public function resetpassword_post(){
        $token=$this->post('token');
        $otp=$this->post('otp');
        $new_password=$this->post('new_password');
        $repassword=$this->post('repassword');

        // Debug logs for reset-password flow (token validity + OTP validation).
        // Avoid logging full password/OTP to keep it safer.
        $tokenPrefix = !empty($token) ? substr((string)$token, 0, 8) : '';
        $otpLen = !empty($otp) ? strlen((string)$otp) : 0;
        $newPwLen = !empty($new_password) ? strlen((string)$new_password) : 0;
        log_message('error', '[resetpassword_post] tokenPrefix=' . $tokenPrefix . ' otpLen=' . $otpLen . ' newPwLen=' . $newPwLen);

        if(empty($token) || empty($otp) || empty($new_password) || empty($repassword)){
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
            return;
        }

        if($new_password!=$repassword){
            $this->response([
                'status' => false,
                'message' => "New Password and Confirm Password do not Match!"
            ], RestController::HTTP_OK);
            return;
        }

        $user=$this->account->verify_token($token);
        if(empty($user) || !is_array($user)){
            log_message('error', '[resetpassword_post] verify_token failed tokenPrefix=' . $tokenPrefix);
            $this->response([
                'status' => false,
                'message' => "Token Invalid"
            ], RestController::HTTP_OK);
            return;
        }

        $where=array("username"=>$user['mobile']);
        $verifyOtpResult=$this->account->verifyotp($otp,$where);
        if($verifyOtpResult['status']!==true){
            $errMsg = $verifyOtpResult['message'] ?? 'Invalid OTP!';
            log_message('error', '[resetpassword_post] verifyotp failed tokenPrefix=' . $tokenPrefix . ' err=' . $errMsg);
            $this->response([
                'status' => false,
                'message' => $verifyOtpResult['message'] ?? 'Invalid OTP!'
            ], RestController::HTTP_OK);
            return;
        }

        $update=$this->account->updatepassword(array("password"=>$new_password),array("id"=>$user['id']));
        if($update['status']===true){
            log_message('error', '[resetpassword_post] password update success tokenPrefix=' . $tokenPrefix . ' userId=' . ($user['id'] ?? ''));
            $this->response([
                'status' => true,
                'message' => $update['message']
            ], RestController::HTTP_OK);
        }
        else{
            $errMsg = $update['message'] ?? 'Failed to reset password';
            log_message('error', '[resetpassword_post] password update failed tokenPrefix=' . $tokenPrefix . ' err=' . $errMsg);
            $this->response([
                'status' => false,
                'message' => $errMsg
            ], RestController::HTTP_OK);
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
