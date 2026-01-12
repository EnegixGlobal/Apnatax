<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    
	function __construct(){
		parent::__construct();
        checklogin();
	}
	
	public function index(){
        $data=['title'=>'Add User'];
        $data['breadcrumb']=array("/"=>"Dashboard");
        $roles=$this->account->getroles();
        $options=array(""=>"Select Role");
        if(is_array($roles)){
            foreach($roles as $role){
                $options[$role['slug']]=$role['name'];
            }
        }
        $data['roles']=$options;
        $where="t1.role!='admin' && t1.role!='customer'";
        $data['users']=$this->account->getusers($where);
        
        $data['datatable']=true;
        
        $this->template->load('users','users',$data);
    }
    
	public function roles(){
        $data=['title'=>"User Roles"];
        $data['breadcrumb']=array("/"=>"Dashboard");//,"users/"=>"Master Key");
        $data['roles']=$this->account->getroles();
        $this->template->load('users','roles',$data);
    }
    
    public function adduser(){
        if($this->input->post('adduser')!==NULL){
            $data=$this->input->post();
            unset($data['adduser']);
            //echo PRE;print_r($data);die;
			$result=$this->account->adduser($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        if($this->input->post('updateuser')!==NULL){
            $data=$this->input->post();
            //echo PRE;print_r($data);die;
            unset($data['updateuser']);
            if(empty($data['password'])){
                unset($data['password']);
            }
            
            //echo PRE;print_r($data);die;
            
			$result=$this->account->updatecrmuser($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        redirect('users/');
    }
    
    public function getuser(){
        $id=$this->input->post('id');
        $user=$this->account->getusers(array("t1.id"=>$id),"single");
        echo json_encode($user);
    }
    
    public function addrole(){
        if($this->input->post('addrole')!==NULL){
            $data=$this->input->post();
            if(strtolower($data['name'])=='admin'){
                $this->session->set_flashdata('err_msg','Cannot Create Admin Role!');
                redirect('users/roles/');
                exit;
            }
            $data['slug']=generate_slug($data['name']);
            $data['slug']=verify_slug('roles',$data['slug']);
            
            unset($data['addrole']);
            if(isset($data['sections'])){
                $data['sections']=implode(',',$data['sections']);
            }
			$result=$this->account->addrole($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        if($this->input->post('updaterole')!==NULL){
            $data=$this->input->post();
            if(strtolower($data['name'])=='admin'){
                $this->session->set_flashdata('err_msg','Cannot Create Admin Role!');
                redirect('users/roles/');
                exit;
            }
            $data['slug']=generate_slug($data['name']);
            $data['slug']=verify_slug('roles',$data['slug'],$data['id']);
            unset($data['updaterole']);
            if(isset($data['sections'])){
                $data['sections']=implode(',',$data['sections']);
            }
            else{
                $data['sections']='';
            } 
			$result=$this->account->updaterole($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        redirect('users/roles/');
    }
    
    public function getrole(){
        $id=$this->input->post('id');
        $role=$this->account->getroles(array("id"=>$id),"single");
        $role['sections']=explode(',',$role['sections']);
        echo json_encode($role);
    }
    
	public function blockuser($username=NULL){
        if($username===NULL){
            $username=$this->uri->segment(3);
        }
        if($username===NULL){
            redirect('home/');
        }
		if($this->session->role=='admin'){
            $action= $this->uri->segment(2);
            if($action=='blockuser'){
                $data['status']=2;
                $message="User Blocked Successfully";
            }
            else{
                $data['status']=1;
                $message="User Un-Blocked Successfully";
            }
            $result=$this->db->update('users',$data,['md5(username)'=>$username]);
			if($result){
				$this->session->set_flashdata("msg",$message);
			}
            else{
                $err=$this->db->error();
                $this->session->set_flashdata("err_msg",$err['message']);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
	}
	
}
