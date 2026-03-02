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
	
	public function myprofile(){
        // Only allow admin/superadmin access
        if ($this->session->role != 'admin' && $this->session->role != 'superadmin') {
            redirect('home/');
        }
        
        $user = getuser();
        $admin_user = $this->account->getusers(array("t1.id" => $user['id']), "single");
        
        if (empty($admin_user)) {
            $this->session->set_flashdata("err_msg", "Admin profile not found!");
            redirect('home/');
        }
        
        // Get address for admin user
        $address = $this->customer->getaddresses(['t1.user_id' => $user['id']], 'single');
        if (empty($address)) {
            $address = array();
        }
        
        $data['title'] = "My Profile";
        $data['breadcrumb'] = array("active" => "My Profile");
        $data['admin'] = $admin_user;
        $data['address'] = $address;
        $data['states'] = state_dropdown();
        
        // Get districts if address exists
        if (!empty($address) && !empty($address['parent_id'])) {
            $data['districts'] = district_dropdown($address['parent_id']);
        } else {
            $data['districts'] = district_dropdown();
        }
        
        $this->template->load('users', 'myprofile', $data);
    }
    
    public function updateprofile(){
        // Only allow admin/superadmin access
        if ($this->session->role != 'admin' && $this->session->role != 'superadmin') {
            redirect('home/');
        }
        
        if ($this->input->post('updateprofile') !== NULL) {
            $user = getuser();
            $data = array();
            $has_updates = false;
            
            // Handle profile fields update (name, email, mobile, gstin)
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $mobile = $this->input->post('mobile');
            $gstin = $this->input->post('gstin');
            
            if (!empty($name)) {
                $data['name'] = $name;
                $has_updates = true;
            }
            if (!empty($email)) {
                $data['email'] = $email;
                $has_updates = true;
            }
            if (!empty($mobile)) {
                $data['mobile'] = $mobile;
                $has_updates = true;
            }
            // GSTIN can be empty, so always update it
            $data['gstin'] = !empty($gstin) ? strtoupper(trim($gstin)) : '';
            $has_updates = true;
            
            // Handle photo upload
            if (!empty($_FILES['photo']) && isset($_FILES['photo']['tmp_name']) && !empty($_FILES['photo']['tmp_name'])) {
                $upload_path = './assets/images/profile/';
                $allowed_types = 'gif|jpg|jpeg|png|svg';
                $upload = upload_file('photo', $upload_path, $allowed_types, generate_slug($user['name'] . '-profile'));
                if ($upload['status'] === true) {
                    $path = $upload['path'];
                    
                    // Try to process image if GD extension is available
                    if (extension_loaded('gd')) {
                        try {
                            $this->load->library('imager');
                            $path = $this->imager->processimage('.' . $upload['path'], 'cropscale', 80, ['width' => 300, 'height' => 300]);
                        } catch (Exception $e) {
                            // If image processing fails, use original uploaded file
                            $path = $upload['path'];
                        }
                    }
                    
                    $data['photo'] = $path;
                    $has_updates = true;
                } else {
                    $this->session->set_flashdata("err_msg", "Photo upload failed: " . $upload['msg']);
                }
            }
            
            // Update user profile if there are changes
            if ($has_updates) {
                $where = array('id' => $user['id']);
                $result = $this->account->updateuser($data, $where);
                if ($result['status'] === true) {
                    $this->session->set_flashdata("msg", "Profile Updated Successfully!");
                } else {
                    $this->session->set_flashdata("err_msg", $result['message']);
                }
            }
            
            // Handle address update
            $address = $this->input->post('address');
            $state_id = $this->input->post('parent_id');
            $district_id = $this->input->post('area_id');
            $pincode = $this->input->post('pincode');
            
            if (!empty($address) && !empty($state_id) && !empty($district_id) && !empty($pincode)) {
                $address_data = array(
                    "user_id" => $user['id'],
                    "address" => $address,
                    "parent_id" => $state_id,
                    "area_id" => $district_id,
                    "pincode" => $pincode
                );
                
                // Validate state and district
                $getstate = $this->db->get_where("area", array("id" => $address_data['parent_id'], 'type' => 'State'));
                $getdistrict = $this->db->get_where("area", array("id" => $address_data['area_id'], "parent_id" => $address_data['parent_id'], 'type' => 'District'));
                
                if ($getdistrict->num_rows() == 1 && $getstate->num_rows() == 1) {
                    $address_data['state'] = $getstate->unbuffered_row()->name;
                    $address_data['district'] = $getdistrict->unbuffered_row()->name;
                    
                    // Check if address already exists
                    $existing_address = $this->customer->getaddresses(['t1.user_id' => $user['id']], 'single');
                    
                    if (!empty($existing_address)) {
                        // Update existing address
                        $address_data['updated_on'] = date('Y-m-d H:i:s');
                        $where_address = array('user_id' => $user['id']);
                        if ($this->db->update("addresses", $address_data, $where_address)) {
                            $this->session->set_flashdata("msg", "Address Updated Successfully!");
                        } else {
                            $error = $this->db->error();
                            $this->session->set_flashdata("err_msg", "Address update failed: " . $error['message']);
                        }
                    } else {
                        // Insert new address
                        $result = $this->customer->saveaddress($address_data);
                        if ($result['status'] === true) {
                            $this->session->set_flashdata("msg", "Address Saved Successfully!");
                        } else {
                            $this->session->set_flashdata("err_msg", $result['message']);
                        }
                    }
                } else {
                    $this->session->set_flashdata("err_msg", "Invalid State or District selected!");
                }
            }
        }
        redirect('users/myprofile/');
    }
	
}
