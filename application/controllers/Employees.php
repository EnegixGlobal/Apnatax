<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends CI_Controller {
    
	function __construct(){
		parent::__construct();
        checklogin();
	}
	
	public function index(){
        $data=['title'=>'Employee List'];
        $data['breadcrumb']=array("/"=>"Dashboard");
        $data['employees']=$this->employee->getemployees();
        $data['datatable']=true;
        $this->template->load('employees','list',$data);
    }
    
	public function add(){
        $data=['title'=>'Add Employee'];
        $data['breadcrumb']=array("/"=>"Dashboard");
        $data['states']=state_dropdown();
        
        $options=array(''=>'Select District');
        $data['districts']=$options;
        
        $data['roles']=role_dropdown();
        
        $data['form']='add';
        $where=array('status'=>1);
        $data['salarypercent']=$this->master->getsalarypercents($where,'single');
        
        $this->template->load('employees','employeeform',$data);
    }
    
	public function edit($id=NULL){
        if($id===NULL){
            redirect('employees/');
        }
        $employee=$this->employee->getemployees(array("md5(t1.id)"=>$id),"single");
        if(empty($employee)){
            redirect('employees/');
        }
        $data=['title'=>'Edit Employee'];
        $data['employee']=$employee;
        $data['states']=state_dropdown();
        
        $options=district_dropdown($employee['parent_id']);
        $data['districts']=$options;
        
        $data['breadcrumb']=array("/"=>"Dashboard");
        $data['form']='update';
        $where=array('status'=>1);
        $data['salarypercent']=$this->master->getsalarypercents($where,'single');
        $this->template->load('employees','employeeform',$data);
    }
    
	public function employeepayment(){
        $data=['title'=>'Employee Payment'];
        $data['breadcrumb']=array("/"=>"Dashboard");
        $data['employees']=employee_dropdown();
        
        $this->template->load('employees','employeepayment',$data);
    }
    
	public function employeepaymentlist(){
        if($this->session->role!='admin'){
            redirect('/');
        }
        $data['title']="Employee Payment List";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        $data['earnings']=$this->employee->getemployeepayments($where);
		$this->template->load('employees','employeepaymentlist',$data);
	}
    
    public function saveemployee(){
        if($this->input->post('saveemployee')!==NULL){
            $data=$this->input->post();
            unset($data['saveemployee']);
            
            // Handle file uploads
            $upload_path = './assets/documents/employees/';
            $allowed_types = 'gif|jpg|jpeg|png|pdf';
            $status = true;
            $message = array();
            
            // Upload PAN Card
            if (isset($_FILES['pan_file']['tmp_name']) && !empty($_FILES['pan_file']['tmp_name'])) {
                $pan_filename = generate_slug($data['name'] . '-pan-card');
                $upload = upload_file('pan_file', $upload_path, $allowed_types, $pan_filename);
                if ($upload['status'] === true) {
                    $data['pan_file'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "PAN Card - " . trim($upload['msg']);
                }
            }
            
            // Upload Aadhar Card
            if (isset($_FILES['aadhar_file']['tmp_name']) && !empty($_FILES['aadhar_file']['tmp_name'])) {
                $aadhar_filename = generate_slug($data['name'] . '-aadhar-card');
                $upload = upload_file('aadhar_file', $upload_path, $allowed_types, $aadhar_filename);
                if ($upload['status'] === true) {
                    $data['aadhar_file'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "Aadhar Card - " . trim($upload['msg']);
                }
            }
            
            // Upload Terms & Conditions
            if (isset($_FILES['terms_file']['tmp_name']) && !empty($_FILES['terms_file']['tmp_name'])) {
                $terms_filename = generate_slug($data['name'] . '-terms-conditions');
                $upload = upload_file('terms_file', $upload_path, $allowed_types, $terms_filename);
                if ($upload['status'] === true) {
                    $data['terms_file'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "Terms & Conditions - " . trim($upload['msg']);
                }
            }
            
            if (!$status) {
                $message = implode('; ', $message);
                $this->session->set_flashdata("err_msg", $message);
                redirect('employees/add/');
                return;
            }
            
            $result=$this->employee->saveemployee($data);
            //print_pre($result,true);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        if($this->input->post('updateemployee')!==NULL){
            $data=$this->input->post();
            unset($data['updateemployee']);
            
            // Get existing employee data
            $employee_id = $data['id'];
            $existing_employee = $this->employee->getemployees(['t1.id' => $employee_id], 'single');
            
            // Handle file uploads
            $upload_path = './assets/documents/employees/';
            $allowed_types = 'gif|jpg|jpeg|png|pdf';
            $status = true;
            $message = array();
            
            // Upload PAN Card
            if (isset($_FILES['pan_file']['tmp_name']) && !empty($_FILES['pan_file']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_employee['pan_file']) && file_exists(FCPATH . $existing_employee['pan_file'])) {
                    @unlink(FCPATH . $existing_employee['pan_file']);
                }
                
                $pan_filename = generate_slug($data['name'] . '-pan-card');
                $upload = upload_file('pan_file', $upload_path, $allowed_types, $pan_filename);
                if ($upload['status'] === true) {
                    $data['pan_file'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "PAN Card - " . trim($upload['msg']);
                }
            }
            
            // Upload Aadhar Card
            if (isset($_FILES['aadhar_file']['tmp_name']) && !empty($_FILES['aadhar_file']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_employee['aadhar_file']) && file_exists(FCPATH . $existing_employee['aadhar_file'])) {
                    @unlink(FCPATH . $existing_employee['aadhar_file']);
                }
                
                $aadhar_filename = generate_slug($data['name'] . '-aadhar-card');
                $upload = upload_file('aadhar_file', $upload_path, $allowed_types, $aadhar_filename);
                if ($upload['status'] === true) {
                    $data['aadhar_file'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "Aadhar Card - " . trim($upload['msg']);
                }
            }
            
            // Upload Terms & Conditions
            if (isset($_FILES['terms_file']['tmp_name']) && !empty($_FILES['terms_file']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_employee['terms_file']) && file_exists(FCPATH . $existing_employee['terms_file'])) {
                    @unlink(FCPATH . $existing_employee['terms_file']);
                }
                
                $terms_filename = generate_slug($data['name'] . '-terms-conditions');
                $upload = upload_file('terms_file', $upload_path, $allowed_types, $terms_filename);
                if ($upload['status'] === true) {
                    $data['terms_file'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "Terms & Conditions - " . trim($upload['msg']);
                }
            }
            
            if (!$status) {
                $message = implode('; ', $message);
                $this->session->set_flashdata("err_msg", $message);
                redirect('employees/edit/' . md5($employee_id));
                return;
            }
            
            $result=$this->employee->updateemployee($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        redirect('employees/');
    }
    
    public function getmanagers(){
        $company=$this->input->post("company");
        $jobtitle=$this->input->post("jobtitle");
        $where="t1.jobtitle in (SELECT parent from ".TP."jobtitles where id='$jobtitle')";
        if(!empty($company)){
            $where.=" and t1.company='$company'";
        }
        $employees=$this->employee->getemployees($where);
        $options=array(""=>"Select");
        if(!empty($employees) && is_array($employees) ){
            foreach($employees as $employee){
                $options[$employee['user_id']]=$employee['name'];
            }
        }
        echo form_dropdown('reporting_manager',$options,'',array('class'=>'form-control',"id"=>"reporting_manager")); 
    }
    
	public function getemployee(){
        $id=$this->input->post('id');
        $employee=$this->employee->getemployees(array("t1.id"=>$id),"single");
        if(!empty($employee)){
            echo json_encode($employee);
        }
    }
	public function getemployeebalance(){
        $emp_id=$this->input->post('emp_id');
        $balances=$this->employee->getemployeebalance($emp_id);
        echo json_encode($balances);
    }
    
    public function makepayment(){
        if($this->input->post('makepayment')!==NULL){
            $data=$this->input->post();
            unset($data['makepayment']);
            //print_pre($data,true);
            $balances=$this->employee->getemployeebalance($data['emp_id']);
            if($data['amount']<=$balances['balance']){
                $result=$this->employee->makepayment($data);
                if($result['status']===true){
                    $this->session->set_flashdata("msg",$result['message']);
                }
                else{
                    $this->session->set_flashdata("err_msg",$result['message']);
                }
            }
            else{
                $this->session->set_flashdata("err_msg","Pay Amount Entered is Greater Than Balance!");
            }
        }
        redirect('employees/employeepayment/');
    }
    
    public function myprofile()
    {
        // Only allow employee access
        if ($this->session->role != 'employee' && $this->session->role != 'ca') {
            redirect('home/');
        }
        
        $user = getuser();
        $employee = $this->employee->getemployees(array("t1.id" => $user['emp_id']), "single");
        
        if (empty($employee)) {
            $this->session->set_flashdata("err_msg", "Employee profile not found!");
            redirect('home/');
        }
        
        $data['title'] = "My Profile";
        $data['breadcrumb'] = array("active" => "My Profile");
        $data['employee'] = $employee;
        $this->template->load('employees', 'myprofile', $data);
    }
    
    public function updateprofile()
    {
        // Only allow employee access
        if ($this->session->role != 'employee' && $this->session->role != 'ca') {
            redirect('home/');
        }
        
        if ($this->input->post('updateprofile') !== NULL) {
            $user = getuser();
            
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
                    
                    $photo_data = array('photo' => $path);
                    $where = array('id' => $user['id']);
                    $photo_result = $this->account->updatephoto($photo_data, $where);
                    if ($photo_result['status'] === true) {
                        $this->session->set_flashdata("msg", "Profile Photo Updated Successfully!");
                    } else {
                        $this->session->set_flashdata("err_msg", "Photo upload failed: " . $photo_result['message']);
                    }
                } else {
                    $this->session->set_flashdata("err_msg", "Photo upload failed: " . $upload['msg']);
                }
            } else {
                $this->session->set_flashdata("err_msg", "Please select a photo to upload!");
            }
        }
        redirect('employees/myprofile/');
    }
    
    public function downloaddocument($type = '', $id = NULL)
    {
        if (empty($type)) {
            $this->session->set_flashdata("err_msg", "Invalid request!");
            redirect('employees/');
        }
        
        $user = getuser();
        $employee = NULL;
        
        // If ID is provided, get that employee (for admin access)
        if (!empty($id)) {
            $employee = $this->employee->getemployees(array("md5(t1.id)" => $id), "single");
            if (empty($employee)) {
                $this->session->set_flashdata("err_msg", "Employee not found!");
                redirect('employees/');
            }
            
            // Check access: Admin can access all, employees can only access their own
            if ($this->session->role != 'admin' && $this->session->role != 'superadmin') {
                // Check if user is the employee themselves
                if ($user['emp_id'] != $employee['id']) {
                    $this->session->set_flashdata("err_msg", "Unauthorized access!");
                    redirect('employees/myprofile/');
                }
            }
        } else {
            // No ID provided - employee accessing their own documents
            if ($this->session->role != 'employee' && $this->session->role != 'ca') {
                $this->session->set_flashdata("err_msg", "Unauthorized access!");
                redirect('employees/');
            }
            
            $employee = $this->employee->getemployees(array("t1.id" => $user['emp_id']), "single");
            if (empty($employee)) {
                $this->session->set_flashdata("err_msg", "Employee profile not found!");
                redirect('employees/myprofile/');
            }
        }
        
        // Define allowed types and their corresponding file fields
        $allowed_types = array(
            'pan' => 'pan_file',
            'aadhar' => 'aadhar_file',
            'terms' => 'terms_file'
        );
        
        if (!isset($allowed_types[$type])) {
            $this->session->set_flashdata("err_msg", "Invalid document type!");
            if (!empty($id)) {
                redirect('employees/edit/' . $id);
            } else {
                redirect('employees/myprofile/');
            }
        }
        
        $file_field = $allowed_types[$type];
        $file_path = $employee[$file_field];
        
        if (empty($file_path)) {
            $this->session->set_flashdata("err_msg", "Document not found!");
            if (!empty($id)) {
                redirect('employees/edit/' . $id);
            } else {
                redirect('employees/myprofile/');
            }
        }
        
        $full_path = FCPATH . $file_path;
        
        // Check if file exists
        if (!file_exists($full_path)) {
            $this->session->set_flashdata("err_msg", "File not found on server!");
            if (!empty($id)) {
                redirect('employees/edit/' . $id);
            } else {
                redirect('employees/myprofile/');
            }
        }
        
        // Generate filename
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        $document_names = array(
            'pan' => 'PAN-Card',
            'aadhar' => 'Aadhar-Card',
            'terms' => 'Terms-Conditions'
        );
        $filename = $employee['name'] . '-' . $document_names[$type] . '.' . $extension;
        
        // Load download helper and force download
        $this->load->helper('download');
        force_download($full_path, NULL);
    }
    
}