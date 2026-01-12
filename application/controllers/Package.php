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
        $data['service_package']=$this->customer->getservicepackage(['t1.user_id'=>$user['id'],'t1.firm_id'=>$firm_id,
                                                                     't1.year'=>$year],'single');
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
    
}
