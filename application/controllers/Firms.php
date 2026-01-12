<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Firms extends CI_Controller {

	function __construct(){
		parent::__construct();
        logrequest();
		//checkcookie();
        if($this->session->role!='customer'){
            redirect('home/');
        }
	}
	
	public function index(){
        $data=['title'=>'Firm'];
        $data['breadcrumb']=array("active"=>"Firm");
        $user=getuser();
        $data['user']=$user;
        $data['datatable']=true;
        $where=array("t1.user_id"=>$user['id'],'t1.status'=>1,'t1.request!='=>1);
        $data['firms']=$this->customer->getfirms($where);
        //print_pre($data,true);
        $this->template->load('firms','firms',$data);
    }

    public function addfirm(){
        if($this->input->post('addfirm')!==NULL){
            $data=$this->input->post();
            $user=getuser();
            $data['user_id']=$user['id'];
            unset($data['addfirm']);
			$result=$this->customer->addfirm($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        if($this->input->post('updatefirm')!==NULL){
            $data=$this->input->post();
            unset($data['updatefirm']);
			$result=$this->customer->updatefirm($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        if(!empty($data) && !isset($data['id'])){
            unset($_SESSION["msg"],$_SESSION["err_msg"]);
            echo json_encode($result);
            exit;
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function getfirm(){
        $id=$this->input->post('id');
        $firm=$this->customer->getfirms(array("t1.id"=>$id),"single");
        echo json_encode($firm);
    }
    
    public function requestdelete(){
        $id=$this->input->post('id');
        $user=getuser();
        $where=array("t1.user_id"=>$user['id'],'t1.id'=>$id);
        $firm=$this->customer->getfirms($where,'single');
        if(!empty($firm)){
            if($firm['request']==0){
                if($this->db->update('firms',['request'=>1],['id'=>$firm['id']])){
                    if($this->session->firm==$firm['id']){
                        $this->session->unset_userdata('firm');
                    }
                    $this->session->set_flashdata("msg","Firm Delete Request Saved!");
                }
                else{
                    $this->session->set_flashdata("err_msg",$result['message']);
                }
            }
        }
    }
    
}