<?php
class Master_model extends CI_Model{
	
	function __construct(){
		parent::__construct(); 
		$this->db->db_debug = false;
	}
	
    public function addservice($data){
        if($this->db->get_where('services',array('name'=>$data['name']))->num_rows()==0){
            if($this->db->insert("services",$data)){
                $service_id=$this->db->insert_id();
                return array("status"=>true,"message"=>"Service Added Successfully!",'service_id'=>$service_id);
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Service Already Added!");
        }
    }
    
    public function getservices($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("services");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updateservice($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->get_where('services',array('name'=>$data['name'],"id!="=>$id))->num_rows()==0){
            if($this->db->update("services",$data,$where)){
                return array("status"=>true,"message"=>"Service Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Service Already Added!");
        }
    }
    
    public function getpackages($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("packages");
        $result=array();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function getdocuments($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("documents");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function addreqdocuments($data){
        if($this->db->get_where('docs_required',array('service_id'=>$data[0]['service_id']))->num_rows()==0){
            if($this->db->insert_batch("docs_required",$data)){
                return array("status"=>true,"message"=>"Required Documents Added Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Required Documents Already Added!");
        }
    }
    
    public function getservicedocuments($where=array(),$type="all",$columns=false){
        if($columns){
            $columns="t1.id";
        }
        else{
            $columns ="t1.id,t1.document_id,t1.display_name,t1.slug,t2.value,t2.file,ifnull(t2.file_type,'--') as file_type,";
            $columns.="CASE WHEN t2.value=0 && t2.file=0 THEN '--' ";
            $columns.=" WHEN t2.value=1 && t2.file=0 THEN 'Value'";
            $columns.=" WHEN t2.value=1 && t2.file>0 THEN 'Value, File Upload'";
            $columns.=" WHEN t2.value=0 && t2.file>0 THEN 'File Upload' ELSE '--' END as type,";
            $columns.=" t2.pattern";
        }
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('docs_required t1');
        $this->db->join('documents t2','t1.document_id=t2.id');
        $this->db->join('services t3','t1.service_id=t3.id');
        $this->db->order_by('t1.id');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatereqdocuments($data){
        $bulk=array();
        foreach($data as $single){
            if($single['id']==''){
                unset($single['status'],$single['type']);
                $bulk[]=$single;
            }
            elseif($single['status']==1){
                unset($single['status'],$single['type']);
                $check=$this->db->get_where('docs_required',$single)->num_rows();
                if($check==0){
                    $this->db->update('docs_required',$single,['id'=>$single['id']]);;
                }
            }
            elseif($single['status']==0){
                $this->db->delete('docs_required',['id'=>$single['id']]);;
            }
        }
        if(!empty($bulk)){
            $this->db->insert_batch("docs_required",$bulk);
        }
        return array('status'=>true,'message'=>'');
    }
    
    public function addsalarypercent($data){
        $status=TRUE;
        $msg="Salary Percent Added Successfully!";
        $getlast=$this->db->get_where("commission_percent",['status'=>1]);
        if($getlast->num_rows()>0){
            $msg="Salary Percent Updated Successfully!";
            $last=$getlast->unbuffered_row('array');
            if($last['percent']==$data['percent']){
                $status=false;
            }
        }
        if($status){
            $datetime=date('Y-m-d H:i:s');
            $update=$this->db->update("commission_percent",['status'=>0,'updated_on'=>$datetime],['status'=>1]);
            $data['added_on']=$data['updated_on']=$datetime;
            if($this->db->insert("commission_percent",$data)){
                $service_id=$this->db->insert_id();
                return array("status"=>true,"message"=>$msg);
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"No Changes Done!");
        }
    }
    
    public function getsalarypercents($where=array(),$type="all",$order_by="id desc"){
        $this->db->where($where);
        $this->db->order_by($order_by);
        $query=$this->db->get("commission_percent");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    
}
