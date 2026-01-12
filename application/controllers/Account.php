<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
	
    var $cartproducts=array();
	function __construct(){
		parent::__construct();
        logrequest();
	}
    
	public function index(){
        if($this->session->user!==NULL && $this->session->role=='applicant_user'){
            redirect('/');
        }
        $data['title']="Login";
        $data['breadcrumb']=array('/'=>'Home');
        $this->load->view('website/includes/top-section',$data);
        $this->load->view('website/includes/header');
        $this->load->view('website/includes/breadcrumb');
        $this->load->view('website/pages/login');
        $this->load->view('website/includes/footer');
        $this->load->view('website/includes/bottom-section');
    }
    
	public function enterotp(){
        if($this->session->user!==NULL && $this->session->role=='applicant_user'){
            redirect('careers/checknext/');
        }
        $data['title']="Enter OTP";
        //$data['otp']=$this->uri->segment(2);
        $data['breadcrumb']=array('/'=>'Homepage','careers/'=>'Careers','active'=>$data['title']);
        $this->load->view('website/includes/top-section',$data);
        $this->load->view('website/includes/main-nav');
        $this->load->view('website/careers/enterotp');
        $this->load->view('website/includes/footer');
        $this->load->view('website/includes/bottom-section');
    }
    
	public function forgotpassword(){
        if($this->session->user!==NULL && $this->session->role=='applicant_user'){
            redirect('careers/checknext/');
        }
        $data['title']="Forgot Password";
        $data['breadcrumb']=array('/'=>'Homepage','careers/'=>'Careers','active'=>$data['title']);
        $this->load->view('website/includes/top-section',$data);
        $this->load->view('website/includes/main-nav');
        $this->load->view('website/careers/forgotpassword');
        $this->load->view('website/includes/footer');
        $this->load->view('website/includes/bottom-section');
    }
    
	public function resetpassword(){
        if($this->session->user!==NULL && $this->session->role=='applicant_user'){
            redirect('dashboard/');
        }
        $data['title']="Reset Password";
        $data['breadcrumb']=array('/'=>'Homepage','careers/'=>'Careers','active'=>$data['title']);
        $this->load->view('website/includes/top-section',$data);
        $this->load->view('website/includes/main-nav');
        $this->load->view('website/careers/resetpassword');
        $this->load->view('website/includes/footer');
        $this->load->view('website/includes/bottom-section');
    }
    
    public function register(){
        if($this->input->post('register')!==NULL){
            $data=$this->input->post();
            unset($data['register']);
            $email=$data['email'];
            $data['username']=$email;
            $data['role']='applicant_user';
            $data['status']=1;
            $result=$this->account->register($data);
            if($result['status']===true){
                $this->session->set_userdata('email',$email);
                $where=array("username"=>$email);
                $smsresult=$this->sendotp($where);
                if($smsresult['status']===false){
                    
                }
                $this->session->set_userdata('otp',$smsresult['message']);
                //redirect('enterotp/');
                redirect('account/verifyotp/');
            }
            else{
                $error=$result['message'];
                $this->session->set_flashdata('reg_err_msg',$error);
            }
        }
        redirect('careers/registration/');
    }
    
	public function validatelogin(){
        if($this->input->post('login')!==NULL){
            $data=$this->input->post();
            $data['username']=$data['email'];
            unset($data['login']);
            $data['role']='applicant_user';
            $result=$this->account->login($data);
            //echo PRE;print_r($result);die;
            if($result['status']===true){
                $this->startsession($result['user']);
                redirect('careers/checknext/');
            }
            else{ 
                $this->session->set_flashdata('logerr',"Wrong Email Id or Password!");
                redirect('careers/login/');
            }
        }
        redirect('careers/login/');
	}
    
	public function validateUser(){
		if($this->input->post('validateuser')!==NULL){
			$email=$this->input->post('email');
            $this->session->set_userdata('email',$email);
			$where=['username'=>$email,'role'=>'applicant_user'];
			$smsresult=$this->sendotp($where);
            
            if($smsresult['status']===true){
                $otp=$smsresult['message'];
                $this->sendmail($email,$otp);
                redirect('enterotp/');
            }
            else{
                $this->session->set_flashdata('logerr',$smsresult['message']);
                redirect('account/forgotpassword/');
            }
		}
		else{
			redirect('careers/login/');
		}
	}
	
	public function startsession($result){
		$data['user']=md5($result['id']);
		$data['name']=$result['name'];
		$data['role']=$result['role'];
		$data['project']=PROJECT_NAME;
		$this->session->set_userdata($data);
	}
		
	public function logout(){
		if($this->session->user!==NULL){
			$data=array("user","name","role","project");
			$this->session->unset_userdata($data);
		}	
		redirect('/');
	}
	
    public function sendotp($where){
        $array=$this->account->createotp($where);
        if($array['status']===true){
            $result=$array['result'];
            $mobile=$result['mobile'];
            $name=$result['name'];
            $otp=$result['otp'];
            $type=$result['type'];
            if($type!='activate'){
                //resetpassword($mobile,$name,$otp);
                //$sms="$otp is your OTP to activate ".PROJECT_NAME." account.";
            }
            else{
                //loginotp($mobile,$otp);
                //$sms="$otp is your OTP to login to your ".PROJECT_NAME." account.";
            }
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
    
    public function verifyotp(){
        $page="reset";
        if($this->input->post('verifyotp')===NULL){
            $_POST['verifyotp']='verify';
            $_POST['otp']=$this->session->otp;
            $page="verify";
        }
        if($this->input->post('verifyotp')!==NULL){
            $otp=$this->input->post('otp');
            $email=$this->session->email;
            $where['username']=$email;
            $result=$this->account->verifyotp($otp,$where);
            //echo PRE;print_r($result);die;
            if($result['status']===true){
                $result=$result['result'];
                if($page=='verify'){
                    $this->startsession($result);
                    $this->session->unset_userdata('email');
                    $this->session->unset_userdata('otp');
                    redirect('careers/checknext/');
                }
                else{
                    redirect('account/resetpassword/');
                }
            }
            else{
                $error=$result['message'];
                $this->session->set_flashdata('err_msg',$error);
            }
        }
        redirect('enterotp/');
    }
    
	
    public function updatepassword(){
        if($this->input->post('updatepassword')!==NULL){
            $password=$this->input->post('password');
            $username=$this->session->email;
            $result=$this->account->updatepassword(array("password"=>$password),array("username"=>$username));
            if($result['status']===true){
                $this->session->set_flashdata('msg',$result['message']);
            }
            else{
                $error=$result['message'];
                $this->session->set_flashdata('err_msg',$error);
            }
        }
        redirect('careers/login/');
    }
    
    public function sendmail($email,$otp){
        $subject="Reset Password";
        $message ="<p>Hi $email,</p>";
        $message.="<p>$otp is your OTP to reset password of your ".PROJECT_NAME." account.</p>";
        $message.="<p>Regards,</p>";
        $message.="<p>StudioNine Constructions</p>";
        sendemail($email,$subject,$message);
    }
    
}
