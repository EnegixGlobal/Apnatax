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
    
}