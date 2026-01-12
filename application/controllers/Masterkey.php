<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masterkey extends CI_Controller {

	function __construct(){
		parent::__construct();
        checklogin();
	}
	
	public function index(){
        $data['title']="Services";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        $data['services']=$this->master->getservices($where);
		$this->template->load('masterkey','services',$data);
	}
    
	public function documents(){
        $data['title']="Documents Required";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        $services=$this->master->getservices($where);
        $options=array(''=>'Select Service');
        if(!empty($services)){
            foreach($services as $service){
                $options[$service['id']]=$service['name'];
            }
        }
        $data['services']=$options;
        
        $where=array();
        $documents=$this->master->getdocuments($where);
        $options=array(''=>'Select Document');
        if(!empty($documents)){
            foreach($documents as $document){
                $options[$document['id']]=$document['name'];
            }
        }
        $data['documents']=$options;
        $data['form']='add';
		$this->template->load('masterkey','documents',$data);
	}
    
	public function documentlist(){
        $data['title']="Documents Required List";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where="id in (SELECT service_id from ".TP."docs_required)";
        $services=$this->master->getservices($where);
        $data['services']=$services;
		$this->template->load('masterkey','documentlist',$data);
	}
    
	public function editdocuments($id=NULL){
        $service=$this->master->getservices(array("md5(concat('service-id-',id))"=>$id),"single");
        $data['title']="Documents Required";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array();
        $services=$this->master->getservices($where);
        $options=array(''=>'Select Service');
        if(!empty($services)){
            foreach($services as $single){
                $options[$single['id']]=$single['name'];
            }
        }
        $data['services']=$options;
        $data['service']=$service;
        
        $where=array();
        $documents=$this->master->getdocuments($where);
        $options=array(''=>'Select Document');
        if(!empty($documents)){
            foreach($documents as $document){
                $options[$document['id']]=$document['name'];
            }
        }
        $data['documents']=$options;
        $data['servicedocuments']=$this->master->getservicedocuments(['t1.service_id'=>$service['id']]);
        $data['form']='update';
		$this->template->load('masterkey','documents',$data);
	}
    
	public function salarypercent(){
        $data['title']="Employee Salary Percent";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['datatable']=true;
        $where=array('status'=>1);
        $data['salarypercents']=$this->master->getsalarypercents($where);
		$this->template->load('masterkey','salarypercent',$data);
	}
    
    public function getservicedocuments(){
        $service_id=$this->input->post('service_id');
        $documents=$this->master->getservicedocuments(['t1.service_id'=>$service_id]);
        echo json_encode($documents);
	}
    
    public function addservice(){
        /*$this->benchmark->mark('code_start');
        $this->debugger->printdata($_POST);
        $count=$this->session->count;
        $count=empty($count)?0:$count;
        echo $count++;
        $this->session->set_userdata('count',$count);
        sleep(1);
        $this->benchmark->mark('code_end');
        echo '<br>Memory Usage: ' . $this->debugger->getmemoryusage() . "<br>";
        echo 'Execution Time: ' . $this->debugger->getelapsedtime('code_start', 'code_end') . ' seconds';
        //sleep(3);
        echo '<p>'.$this->benchmark->memory_usage().'</p>';
        if($count<2){
            echo '<script>window.location.reload()</script>';
        }else{
            $this->session->unset_userdata('count');
        }
        echo 'Execution Time: ' . $this->debugger->getelapsedtime() . ' seconds';
        die;*/
        if($this->input->post('addservice')!==NULL){
            $data=$this->input->post();
            unset($data['addservice']);
            $slug=generate_slug($data['name']);
            $data['slug']=verify_slug('services',$slug);
            $data['type']=!empty($data['type'])?implode(',',$data['type']):'';
            //$data['service_for']=!empty($data['service_for'])?implode(',',$data['service_for']):'';
            $data['debit_date']=!empty($data['debit_date'])?$data['debit_date']:NULL;
			$result=$this->master->addservice($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg",$result['message']);
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        if($this->input->post('updateservice')!==NULL){
            $data=$this->input->post();
            unset($data['updateservice']);
            $slug=generate_slug($data['name']);
            $data['slug']=verify_slug('services',$slug,$data['id']);
            $data['type']=!empty($data['type'])?implode(',',$data['type']):'';
            //$data['service_for']=!empty($data['service_for'])?implode(',',$data['service_for']):'';
            $data['debit_date']=!empty($data['debit_date'])?$data['debit_date']:NULL;
			$result=$this->master->updateservice($data);
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
    
    public function getservice(){
        $id=$this->input->post('id');
        $service=$this->master->getservices(array("id"=>$id),"single");
        if(!empty($service)){
            $service['types']=explode(',',$service['type']);
            $service['services_for']=explode(',',$service['service_for']);
        }
        echo json_encode($service);
    }
    
    public function addreqdocuments(){
        if($this->input->post('addreqdocuments')!==NULL){
            $data=$this->input->post();
            unset($data['addreqdocuments']);
            $service_id=$data['service_id'];
            $document_ids=$data['document_id'];
            $display_names=$data['display_name'];
            $data=array();
            if(!empty($document_ids)){
                $service=$this->master->getservices(['id'=>$service_id],'single');
                $docs=array();
                foreach($document_ids as $key=>$document_id){
                    //if(in_array($document_id,$docs)){ continue; }
                    $slug=generate_slug($display_names[$key]);
                    $slug=$service['slug'].'-'.$slug;
                    $data[]=array('service_id'=>$service_id,'document_id'=>$document_id,'display_name'=>$display_names[$key],
                                  'slug'=>$slug);
                    $docs[]=$document_id;
                }
                //print_pre($data,true);
                $result=$this->master->addreqdocuments($data);
                if($result['status']===true){
                    $this->session->set_flashdata("msg",$result['message']);
                }
                else{
                    $this->session->set_flashdata("err_msg",$result['message']);
                }
			}
            else{
                $this->session->set_flashdata("err_msg","Please Try Again!");
            }
        }
        if($this->input->post('updatereqdocuments')!==NULL){
            $data=$this->input->post();
            
            $service_id=$data['service_id'];
            $document_ids=$data['document_id'];
            $display_names=$data['display_name'];
            $ids=$data['id'];
            $statuses=$data['status'];
            unset($data['updatereqdocuments']);
            $data=array();
            if(!empty($document_ids)){
                $service=$this->master->getservices(['id'=>$service_id],'single');
                $docs=array();
                foreach($document_ids as $key=>$document_id){
                    //if(in_array($document_id,$docs)){ continue; }
                    if(empty($ids[$key])){
                        if(empty($document_ids[$key])){ continue; }
                        $slug=generate_slug($display_names[$key]);
                        $slug=$service['slug'].'-'.$slug;
                        $data[]=array('service_id'=>$service_id,'document_id'=>$document_id,
                                      'display_name'=>$display_names[$key],'slug'=>$slug,'status'=>1,'id'=>0);
                    }
                    else{
                        if(empty($document_ids[$key])){ continue; }
                        $slug=generate_slug($display_names[$key]);
                        $slug=$service['slug'].'-'.$slug;
                        $data[]=array('service_id'=>$service_id,'document_id'=>$document_id,
                                      'display_name'=>$display_names[$key],'slug'=>$slug,'status'=>$statuses[$key],
                                      'id'=>$ids[$key]);
                    }
                    $docs[]=$document_id;
                }
                $result=$this->master->updatereqdocuments($data);
                if($result['status']===true){
                    $this->session->set_flashdata("msg","Updated Successfully!");
                    redirect('masterkey/documentlist/');
                }
                else{
                    $this->session->set_flashdata("err_msg",$result['message']);
                }
			}
            else{
                $this->session->set_flashdata("err_msg","Please Try Again!");
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function getreq(){
        $id=$this->input->post('id');
        $service=$this->master->getservices(array("id"=>$id),"single");
        echo json_encode($service);
    }
    
    public function getdistricts(){
        $parent_id=$this->input->post('parent_id');
        $area_id=$this->input->post('area_id');
        $options=district_dropdown($parent_id);
        echo form_dropdown('area_id',$options,$area_id,array('class'=>'form-control','id'=>'area_id','required'=>'true'));
    }
    
    public function addsalarypercent(){
        if($this->input->post('addsalarypercent')!==NULL){
            $data=$this->input->post();
            unset($data['addsalarypercent']);
			$result=$this->master->addsalarypercent($data);
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
    
    public function getsalarypercent(){
        $id=$this->input->post('id');
        $salarypercent=$this->master->getsalarypercents(array("id"=>$id),"single");
        echo json_encode($salarypercent);
    }
    
}
//url_title
