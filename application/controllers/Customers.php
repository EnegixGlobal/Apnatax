<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller {

	function __construct(){
		parent::__construct();
        checklogin();
	}
	
	public function index(){
        $data['title']="Customers";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        if($this->session->role!='admin'){
            $where['md5(t1.added_by)']=$this->session->user;
        }
        $data['customers']=$this->customer->getcustomers($where);
		$this->template->load('customer','customers',$data);
	}
    
	public function addcustomer(){
        $data['title']="Add Customer";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['states']=state_dropdown();
        
        $options=array(''=>'Select District');
        $data['districts']=$options;
        
        
        
        $data['form']='add';
        
		$this->template->load('customer','customerform',$data);
	}
    
	public function editcustomer($id=NULL){
        $customer=$this->customer->getcustomers(['md5(t1.id)'=>$id],'single');
        if(empty($customer)){
            redirect('customers/');
        }
        $data['customer']=$customer;
        $data['title']="Edit customer";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['states']=state_dropdown();
        
        $options=district_dropdown($customer['parent_id']);
        $data['districts']=$options;
        
        
        $data['form']='update';
        
		$this->template->load('customer','customerform',$data);
	}
    
    
    public function kycdetails($id=NULL){
        $customer=$this->customer->getcustomers(['md5(t1.id)'=>$id],'single');
        if(empty($customer)){
            redirect('customers/');
        }
        $data['customer']=$customer;
        $data['title']="Customer KYC Details";
        $kyc=$this->account->getkyc(['t1.user_id'=>$customer['user_id']],'single');
        $data['kyc']=$kyc;
        $data['form']='update';
        
        //$this->debugger->printdata($kyc,true,true);
		$this->template->load('customer','kycdetails',$data);
	}
    
	public function customerpurchases(){
        $data['title']="Customer Purchases";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        if($this->session->role!='admin'){
            $where['md5(t1.added_by)']=$this->session->user;
        }
        $data['customers']=customer_dropdown($where);
        $data['years']=year_dropdown();
		$this->template->load('customer','customerpurchases',$data);
	}
    
	public function customerwisereport(){
        $data['title']="Customer Wise Report";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        if($this->session->role!='admin'){
            $where['md5(t1.added_by)']=$this->session->user;
        }
        $data['customers']=customer_dropdown($where);
        $data['years']=year_dropdown();
		$this->template->load('customer','customerwisereport',$data);
	}
    
	public function packageswitchrequests(){
        $data['title']="Customer Package Switch Requests";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array('t1.status'=>2);
        $data['customers']=$this->customer->getcustomerpackages($where);
		$this->template->load('customer','packageswitchrequests',$data);
	}
    
	public function firmdeleterequests(){
        $data['title']="Firm Delete Requests";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array('t1.status'=>1,'t1.request'=>1);
        $data['customers']=$this->customer->getfirms($where);
		$this->template->load('customer','firmdeleterequests',$data);
	}
    
    
    public function savecustomer(){
        if($this->input->post('savecustomer')!==NULL){
            $data=$this->input->post();
            unset($data['savecustomer']);
            $user=getuser();
            $data['added_by']=$user['id'];
			$result=$this->customer->savecustomer($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
            redirect('customers/addcustomer/');
        }
        if($this->input->post('updatecustomer')!==NULL){
            $data=$this->input->post();
            unset($data['updatecustomer']);
            $user=getuser();
            //print_pre($data,true);
			$result=$this->customer->updatecustomer($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
            redirect('customers/');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function updatepackagerequest(){
        $id=$this->input->post('id');
        $status=$this->input->post('status');
        $getpackage=$this->db->get_where('customer_packages',["md5(concat('customer-package-',id))"=>$id]);
        if($getpackage->num_rows()>0){
            $package=$getpackage->unbuffered_row('array');
            if($status==1){
                $this->db->update('customer_packages',['status'=>0],['user_id'=>$package['user_id'],
                                                                 'firm_id'=>$package['firm_id']]);
            }
            
            $result=$this->db->update('customer_packages',['status'=>$status],['id'=>$package['id']]);
            if($result){
                $this->session->set_flashdata("msg","Package Switch Request Approved Successfully!");
            }
            else{
                $error=$this->db->error();
                $this->session->set_flashdata("err_msg",$error['message']);
            }
        }
    }
    
    public function updatefirmstatus(){
        $id=$this->input->post('id');
        $status=$this->input->post('status');
        $firm=$this->customer->getfirms(["md5(concat('firm-id-',t1.id))"=>$id,'request'=>1,'status'=>1],'single');
        if(!empty($firm)){
            $message=$status==1?"Firm Deleted Successfully":"Firm Delete Request Rejected!";
            $request=$status==1?1:2;
            $status=$status==1?0:1;
            logupdateoperations('firms',['status'=>$status,'request'=>$request],['id'=>$firm['id']]);
            $result=$this->db->update('firms',['status'=>$status,'request'=>$request],['id'=>$firm['id']]);
            if($result){
                $this->session->set_flashdata("msg",$message);
            }
            else{
                $error=$this->db->error();
                $this->session->set_flashdata("err_msg",$error['message']);
            }
        }
    }
    
    public function getpurchases(){
        $user_id=$this->input->post('user_id');
        $year=$this->input->post('year');
        if(!empty($year)){
            $years=getyearmonthvalues($year);
            $data['years']=$years;
            $where=array('t1.user_id'=>$user_id,'t1.date>='=>$years['year1'].'-04-01','t1.date<='=>$years['year2'].'-03-31');
            $data['purchases']=$this->service->getpurchases($where);
        }
        $data['services']=$this->master->getservices();
        $this->load->view('customer/servicetable',$data);
    }
    
    public function getcustomerreport(){
        $user_id=$this->input->post('user_id');
        $year=$this->input->post('year');
        $where=array('t1.user_id'=>$user_id);
        if(!empty($year)){
            $where['t1.year']=$year;
        }
        $data['orders']=$this->service->getpurchases($where);
        $this->load->view('customer/orderlist',$data);
    }
    
    
}
//url_title