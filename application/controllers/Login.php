<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    
    function __construct(){
		parent::__construct();
    }

	public function index(){
		loginredirect();
        if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']=='magazine.studionineconstructions.com') {
            $this->session->set_userdata('section','magazine');
        }
        $data['title']="Login";
        $data['page']="login";
        $this->load->view('includes/top-section',$data);
        $this->load->view('pages/login');
        $this->load->view('includes/bottom-section');
	}
    
	public function drowssaptide(){
        $data['title']="Edit Password";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
		$this->template->load('pages','editpassword',$data);
	}
    
	public function validatelogin(){
        $redirect='login/';
        if($this->input->post('login')!==NULL){
            $data=$this->input->post();
            if(isset($data['role']) && $data['role']=='customer'){
                $redirect='login.php';
            }
            unset($data['login']);
            $result=$this->account->login($data);
            if($result['status']===true){
                $user=$result['user'];
                $this->startsession($user);
                loginredirect();
            }
            else{ 
                $this->session->set_flashdata('logerr',$result['message']);
                redirect($redirect);
            }
        }
        redirect($redirect);
	}
	
	public function register(){
        $redirect='register.php';
        if($this->input->post('register')!==NULL){
            $data=$this->input->post();
            $data['username']=$data['mobile'];
            unset($data['register']);
            //$this->db->trans_start();
            $result=$this->account->register($data);
            if($result['status']===true){  
                $mobile=$data['mobile'];
                $where=array("username"=>$mobile);
                $smsresult=$this->sendotp($where);
                if($smsresult['status']===false){

                }
                unset($data['username'],$data['role'],$data['password']);
                $data['user_id']=$result['user_id'];
                $data['old']=$result['old'];
                $result=$this->customer->savecustomer($data);
                $this->session->set_userdata('mobile',$mobile);
                redirect('enterotp.php?otp='.$smsresult['message']);
            }
            else{
                $this->session->set_flashdata('logerr',$result['message']);
                redirect($redirect);
            }
        }
        redirect($redirect);
	}
    
    public function verifyotp(){
        if($this->input->post('verifyotp')!==NULL){
            $otp=$this->input->post('otp');
            $username=$this->session->mobile;
            $where['username']=$username;
            //$this->db->trans_start();
            $result=$this->account->verifyotp($otp,$where);
            if($result['status']===true){
                $result=$result['result'];
                $this->startsession($result);
                $this->session->unset_userdata('mobile');
                redirect('home/');
            }
            else{
                $error=$result['message'];
                $this->session->set_flashdata('err_msg',$error);
            }
        }
        redirect('enterotp.php');
    }
	public function logout(){
        $url='login/';
		if($this->session->user!==NULL){
            if($this->session->role=='customer'){
                $url='login.php';
            }
			$data=array("user","name","role","project","desig","year","firm");
			$this->session->unset_userdata($data);
		}	
		redirect($url);
	}
	
	public function startsession($result){
		$data['user']=md5($result['id']);
		$data['name']=$result['name'];
		$data['emp_id']=$result['emp_id'];
		$data['role']=$result['role'];
		$data['project']=PROJECT_NAME;
		$this->session->set_userdata($data);
	}
	
    public function updatepassword(){
        if($this->input->post('updatepassword')!==NULL){
            $password=$this->input->post('password');
            $username=$this->input->post('username');
            $result=$this->account->updatepassword(array("password"=>$password),array("username"=>$username,"role"=>"admin"));
            if($result['status']===true){
                $this->session->set_flashdata('msg',$result['message']);
            }
            else{
                $error=$result['message'];
                $this->session->set_flashdata('err_msg',$error);
            }
        }
        redirect('');
    }
    
    public function updatesender(){
        $sender=$this->input->post('sender');
		write_file('./sender.txt',$sender);
        redirect('profile/');
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
    
}
