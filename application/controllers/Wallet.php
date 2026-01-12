<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends CI_Controller {

	function __construct(){
		parent::__construct();
        checklogin();
	}
	
	public function index(){
        if($this->session->role!='employee'){
            redirect('wallet/employeeearnings/');
        }
        $data['title']="My Earnings";
        $user=getuser();
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $user=getuser();
        $where=array('a.emp_id'=>$user['emp_id']);
        $data['earnings']=$this->employee->getmyearnings($where);
		$this->template->load('wallet','myearnings',$data);
	}
    
	public function mypayments(){
        if($this->session->role!='employee'){
            redirect('employees/employeepaymentlist/');
        }
        $data['title']="My Payments";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $user=getuser();
        $where=array('t1.emp_id'=>$user['emp_id']);
        $data['earnings']=$this->employee->getemployeepayments($where);
		$this->template->load('wallet','mypayments',$data);
	}
    
	public function mywallet(){
        if($this->session->role!='customer'){
            redirect('home/');
        }
        $data['title']="My Wallet";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $user=getuser();
        $where1=array('user_id'=>$user['id'],'status'=>1);
        $where2=array('t1.user_id'=>$user['id']);
        $order_by="added_on desc";
        $data['transactions']=$this->wallet->gettransactions($where1,$where2,$order_by);
		$this->template->load('wallet','wallet',$data);
	}
    
	public function employeeearnings(){
        if($this->session->role!='admin'){
            redirect('/');
        }
        $data['title']="Employee Earnings";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        $data['earnings']=$this->employee->getemployeeearnings($where);
		$this->template->load('wallet','employeeearnings',$data);
	}
    
	public function viewearnings($emp_id=NULL){
        $data['title']="View Earnings";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array('md5(a.emp_id)'=>$emp_id);
        $data['earnings']=$this->employee->getmyearnings($where);
		$this->template->load('wallet','myearnings',$data);
	}
    
    public function addtowallet(){
        if($this->input->post('addtowallet')!==NULL){
            $amount=$this->input->post('amount');
            $user=getuser();
            $date=date('Y-m-d');
            $data=array('user_id'=>$user['id'],'date'=>$date,'amount'=>$amount);
            if(date('Y-m-d')<'2025-06-30'){
                //$data['status']=1;
            }
            $result=$this->wallet->addtowallet($data);
			if($result['status']===true){
                $params="user=".md5('user-id-'.$user['id']).'&merchant_transaction_id='.$result['merchant_transaction_id'];
                $params.="&year=".$this->session->year."&firm=".$this->session->firm;
                $this->load->library('phonepe');
                $redirect_url=base_url('home/paymentresponse/?'.$params);
                $data=array('amount'=>$data['amount'],'user_id'=>$user['id'],'mobile'=>$user['mobile'],
                            'transactionId'=>$result['merchant_transaction_id'],'redirect_url'=>$redirect_url);
                $this->phonepe->initiatePayment($data);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
                redirect('wallet/mywallet/');
            }
        }
        else{
            redirect('wallet/mywallet/');
        }
    }
    
	public function myassessments(){
        $data['title']="Orders";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        if($this->session->role!='admin'){
            $where['md5(a.user_id)']=$this->session->user;
        }
        $data['orders']=$this->employee->myassessments($where);
		$this->template->load('orders','myassessments',$data);
	}
    
	public function viewdocuments($id=NULL){
        $order=$this->service->getpurchases(['md5(t1.id)'=>$id],'single');
        //print_pre($order,true);
        if(empty($order)){
            redirect('orders/');
            exit;
        }
        $service_id=$order['service_id'];
        
        
        $documents=$this->service->getuploadeddocuments(['a.order_id'=>$order['id']]);
        if(!empty($documents)){
            foreach($documents as $key=>$value){
                if(strpos($value['formvalue'],'/assets/')===0){
                    $documents[$key]['formvalue']=file_url($value['formvalue']);
                }
            }
        }
        else{
            $documents=$this->master->getservicedocuments(['t1.service_id'=>$service_id]);
        }
        
        $data['order']=$order;
        $data['documents']=$documents;
        $data['title']="View Documents";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        //print_pre($data,true);
        
        $where="t1.role!='admin' && t1.role!='customer'";
        $employees=$this->account->getusers($where);
        
        $options=array(''=>"Select Employee");
        if(!empty($employees)){
            foreach($employees as $employee){
                $options[$employee['id']]=$employee['username'].' - '.$employee['name'];
            }
        }
        
        $data['employees']=$options;
        
        $getassigned=$this->db->get_where('order_assign',['order_id'=>$order['id'],'status'=>0]);
        $assigned=array();
        if($getassigned->num_rows()>0){
            $assigned=$getassigned->unbuffered_row('array');
        }
        $data['assigned']=$assigned;
        
        $getassessment=$this->db->get_where('assessments',['order_id'=>$order['id'],'status'=>0]);
        $assessment=array();
        if($getassessment->num_rows()>0){
            $assessment=$getassessment->unbuffered_row('array');
        }
        $data['assessment']=$assessment;
        
		$this->template->load('orders','viewdocuments',$data);
	}
    
	public function acceptorder($id=NULL){
        $order=$this->service->getpurchases(['md5(t1.id)'=>$id],'single');
        //print_pre($order,true);
        if(empty($order)){
            redirect('orders/');
            exit;
        }
        $service_id=$order['service_id'];
        $user=getuser();
        $data=array('order_id'=>$order['id'],'user_id'=>$user['id'],'done_by'=>$user['id'],'status'=>0,
                    'added_on'=>date('Y-m-d H:i:s'),'updated_on'=>date('Y-m-d H:i:s'));
        if($this->db->insert('order_assign',$data)){
            $this->db->update('purchases',['status'=>3],['id'=>$order['id']]);
            $this->session->set_flashdata(['msg'=>'Accepted for Assessment']);
        }
        else{
            $error=$this->db->error();
            $this->session->set_flashdata(['err_msg'=>$error['message']]);
        }
        redirect('orders/');
	}
    
	public function assignemployee($id=NULL){
        if($this->input->post('assignemployee')!==NULL){
            $data=$this->input->post();
            $user=getuser();
            $data['done_by']=$user['id'];
            unset($data['assignemployee']);
            $data['added_on']=$data['updated_on']=date('Y-m-d H:i:s');
            if($this->db->insert('order_assign',$data)){
                $this->db->update('purchases',['status'=>3],['id'=>$data['order_id']]);
                $this->session->set_flashdata(['msg'=>'Employee Assigned Successfully!']);
            }
            else{
                $error=$this->db->error();
                $this->session->set_flashdata(['err_msg'=>$error['message']]);
            }
        }
        redirect('orders/');
	}
    
	public function uploadassessment($id=NULL){
        $redirect='orders/';
        if($this->input->post('uploadassessment')!==NULL){
            $data=$this->input->post();
            $user=getuser();
            $data['user_id']=$user['id'];
            unset($data['uploadassessment']);
            $redirect='orders/viewdocuments/'.md5($data['order_id']);
            $upload_path='./assets/service/assessments/';
            $allowed_types='pdf|xlsx|doc|docx';
            if(isset($_FILES['file']['tmp_name'])){
                $order=$this->service->getpurchases(['t1.id'=>$data['order_id']],'single');
                $filename=$order['name'].'-'.$order['service_name'].'-assessment';
                $upload=upload_file('file',$upload_path,$allowed_types,$filename);
                if($upload['status']===true){
                    $data['file']=$upload['path'];
                
                    $data['date']=date("Y-m-d");
                    $data['added_on']=$data['updated_on']=date('Y-m-d H:i:s');
                    if($this->db->insert('assessments',$data)){
                        $this->db->update('purchases',['status'=>4],['id'=>$data['order_id']]);
                        $this->session->set_flashdata(['msg'=>'Assessment Uploaded Successfully!']);
                    }
                    else{
                        $error=$this->db->error();
                        $this->session->set_flashdata(['err_msg'=>$error['message']]);
                    }
                }
                else{
                    $this->session->set_flashdata(['err_msg'=>$upload['msg']]);
                }
            }
            else{
                $this->session->set_flashdata(['err_msg'=>"File not Uploaded! Please Try Again"]);
            }
        }
        redirect($redirect);
	}
    
    
}
//url_title