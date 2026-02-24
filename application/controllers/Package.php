<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends CI_Controller {

	function __construct(){
		parent::__construct();
        logrequest();
        checklogin();
		//checkcookie();
        if($this->session->role!='customer'){
            redirect('/');
        }
	}
	
	public function index(){
        $data=['title'=>'My Package'];
        $data['breadcrumb']=array("active"=>"My Package");
        $data['alertify']=true;
        $user=getuser();
        $year=$this->session->year;
        $firm_id=$this->session->firm;
        $where=array('user_id'=>$user['id'],'status'=>1);
        $query=$this->db->get_where('customer_packages',$where);
        if($query->num_rows()>0){
            $data['package']=$query->unbuffered_row('array');
        }
        $where_package=array('t1.user_id'=>$user['id'],'t1.firm_id'=>$firm_id,'t1.year'=>$year);
        $data['service_package']=$this->customer->getservicepackage($where_package,'single');
		$this->template->load('package','mypackage',$data);
    }
	
    public function savepackage(){
        if($this->input->post('savepackage')!==NULL){
            $data=$this->input->post();
            $user=getuser();
            $firm_id=$this->session->firm;
            $year=$this->session->year;
            $service_id=$data['service_id'];
            $service_ids=implode(',',$service_id);
            $service_ids=trim($service_ids);
            $service_ids=trim($service_ids,',');
            //print_pre($service_ids,true);
            $where=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id,'t1.status'=>1);
            $firm=$this->customer->getfirms($where,'single');
            if(!empty($firm)){
                $s_ids=explode(',',$service_ids);
                $where="status='1' and id in ('".implode("','",$s_ids)."')";
                $services=$this->master->getservices($where);
                if(!empty($services)){
                    $data=array('user_id'=>$user['id'],'firm_id'=>$firm_id,'year'=>$year,'service_ids'=>$service_ids);
                    $result=$this->customer->createpackage($data);
                    if($result['status']===true){
                        $this->session->set_flashdata('msg',$result['message']);
                    }	
                    else{
                        $this->session->set_flashdata('err_msg',$result['message']);
                    }
                }
                else{
                    $this->session->set_flashdata('err_msg','Service Not available!');
                }
            }	
            else{
                $this->session->set_flashdata('err_msg','Firm not selected!');
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function requestdelete(){
        // Check if request column exists
        $check_column = $this->db->query("SHOW COLUMNS FROM `tf_service_packages` LIKE 'request'");
        if ($check_column->num_rows() == 0) {
            $this->session->set_flashdata("err_msg","Delete request feature is not available. Please contact administrator.");
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }
        
        $user=getuser();
        $firm_id=$this->session->firm;
        $year=$this->session->year;
        $where=array('t1.user_id'=>$user['id'],'t1.firm_id'=>$firm_id,'t1.year'=>$year);
        $service_package=$this->customer->getservicepackage($where,'single');
        if(!empty($service_package)){
            // Check if request field exists in the result
            if(!isset($service_package['request'])){
                $service_package['request'] = 0;
            }
            // Allow request if no request (0) or if rejected (2) - can re-request after rejection
            if($service_package['request']==0 || $service_package['request']==2){
                if($this->db->update('service_packages',['request'=>1],['id'=>$service_package['id']])){
                    $message = $service_package['request'] == 2 ? "Package Delete Request Resubmitted! Admin will review your request." : "Package Delete Request Saved! Admin will review your request.";
                    $this->session->set_flashdata("msg", $message);
                }
                else{
                    $this->session->set_flashdata("err_msg","Failed to save delete request!");
                }
            }
            else{
                $this->session->set_flashdata("err_msg","Delete Request already submitted!");
            }
        }
        else{
            $this->session->set_flashdata("err_msg","Package not found!");
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
}
