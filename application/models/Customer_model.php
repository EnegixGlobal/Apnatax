<?php
class Customer_model extends CI_Model{
	
	function __construct(){
		parent::__construct(); 
		$this->db->db_debug = false;
	}
	
    public function savecustomer($data){
        $this->db->trans_start();
        if(empty($data['user_id']) || !is_numeric($data['user_id'])){
            $userdata=array('username'=>$data['mobile'],'name'=>$data['name'],'mobile'=>$data['mobile'],
                            'email'=>$data['email']);
            $userdata['role']='customer';
            $userdata['password']='';
            $result=$this->account->register($userdata);
        }
        else{
            $result=array('status'=>true,'user_id'=>$data['user_id'],'old'=>$data['old']);
            unset($data['old']);
        }
        //print_pre($result,true);
        if($result['status']===true && (empty($result['old']) || $result['old']===false)){
            $user_id=$result['user_id'];
            $data['user_id']=$user_id;
            $datetime=date('Y-m-d H:i:s');
            $data['added_on']=$data['updated_on']=$datetime;
            if($this->db->get_where('customers',array('mobile'=>$data['mobile']))->num_rows()==0){
                if($this->db->insert("customers",$data)){
                    $customer_id=$this->db->insert_id();
                    $this->db->trans_complete();
                    return array("status"=>true,"message"=>"Customer Added Successfully!",'customer_id'=>$customer_id);
                }
                else{
                    $error=$this->db->error();
                    return array("status"=>false,"message"=>$error['message']);
                }
            }
            else{
                return array("status"=>false,"message"=>"Customer Already Added!");
            }
        }
        else{
            if(!empty($result['old'])){
                $message="Mobile no. Already Registered to a Customer!";
            }
            else{
                $message=$result['message'];
            }
            return array("status"=>false,"message"=>$message);
        }
    }
    
    public function getcustomers($where=array(),$type="all"){
        $columns="t1.*,t2.name as state_name,t3.name as district_name,t4.name as user_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('customers t1');
        $this->db->join('area t2','t1.parent_id=t2.id','left');
        $this->db->join('area t3','t1.area_id=t3.id','left');
        $this->db->join('users t4','t1.added_by=t4.id','left');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatecustomer($data){
        $datetime=date('Y-m-d H:i:s');
        $data['updated_on']=$datetime;
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->get_where('customers',array('mobile'=>$data['mobile'],"id!="=>$id))->num_rows()==0){
            if($this->db->update("customers",$data,$where)){
                return array("status"=>true,"message"=>"Customer Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Customer Already Added!");
        }
    }
    
    public function saveaddress($data){
        $datetime=date('Y-m-d H:i:s');
        $data['added_on']=$data['updated_on']=$datetime;
        if($this->db->insert("addresses",$data)){
            $address_id=$this->db->insert_id();
            return array("status"=>true,"message"=>"Address Added Successfully!",'address_id'=>$address_id);
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getaddresses($where=array(),$type="all"){
        $columns="t1.*";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('addresses t1');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function addfirm($data){
        $this->db->trans_start();
        $datetime=date('Y-m-d H:i:s');
        $data['added_on']=$data['updated_on']=$datetime;
        $where="((name='$data[name]' and user_id='$data[user_id]')";
        if(!empty($data['gstin'])){
            $where.=" or (gstin='$data[gstin]') ";
        }
        $where.=") and request='0'";
        if($this->db->get_where('firms',$where)->num_rows()==0){
            if($this->db->insert("firms",$data)){
                $firm_id=$this->db->insert_id();
                $this->db->trans_complete();
                return array("status"=>true,"message"=>"Firm Added Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Firm Already Added!");
        }
    }
    
    public function getfirms($where=array(),$type="all"){
        $columns="t1.*,t2.name as customer_name,t2.mobile,t2.email";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('firms t1');
        $this->db->join('customers t2','t1.user_id=t2.user_id');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function getcustomerpackages($where=array(),$type="all"){
        $columns="t2.name,t2.mobile,t2.email,t1.*, CASE WHEN t3.package_id=1 THEN 'Accountancy Prime' ELSE 'Accountancy Premium' END as current_package, CASE WHEN t1.package_id=1 THEN 'Accountancy Prime' ELSE 'Accountancy Premium' END as package";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('customer_packages t1');
        $this->db->join('customers t2','t1.user_id=t2.user_id');
        $this->db->join('customer_packages t3',"t1.user_id=t3.user_id and t3.status='1'");
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function customerwithfirm($where=array(),$type="all"){
        $where2="t1.user_id in (SELECT user_id from ".TP."customer_packages where status='1' )";
        $columns="t1.id as firm_id,t1.user_id,t1.name as firm_name,t2.name as customer_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->where($where2);
        $this->db->from('firms t1');
        $this->db->join('customers t2','t1.user_id=t2.user_id');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function createpackage($data){
        $this->db->trans_start();
        $datetime=date('Y-m-d H:i:s');
        $data['added_on']=$data['updated_on']=$datetime;
        $where=array('user_id'=>$data['user_id'],'firm_id'=>$data['firm_id'],'year'=>$data['year']);
        if($this->db->get_where('service_packages',$where)->num_rows()==0){
            if($this->db->insert("service_packages",$data)){
                $firm_id=$this->db->insert_id();
                $this->db->trans_complete();
                return array("status"=>true,"message"=>"Package Created Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            unset($data['added_on']);
            if($this->db->update("service_packages",$data,$where)){
                if($this->db->affected_rows()>0){
                    $message="Package Updated Successfully!";
                }
                else{
                    $message="No changes done in Package!";
                }
                $this->db->trans_complete();
                return array("status"=>true,"message"=>$message);
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
    }
    
    public function getservicepackage($where=array(),$type="all"){
        $columns="t1.*,t2.name as customer_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('service_packages t1');
        $this->db->join('customers t2','t1.user_id=t2.user_id');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
            if(!empty($array)){
                $s_ids=explode(',',$array['service_ids']);
                $where="status='1' and id in ('".implode("','",$s_ids)."')";
                $array['services']=$this->master->getservices($where);
            }
        }
        return $array;
    }
    
    public function saveoldclientdata($data){
        $datetime=date('Y-m-d H:i:s');
        $data['added_on']=$data['updated_on']=$datetime;
        if($this->db->insert("old_client_data",$data)){
            $id=$this->db->insert_id();
            return array("status"=>true,"message"=>"Old Data Uploaded Successfully!",'id'=>$id);
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getoldclientdata($where=array(),$type="all"){
        $columns="t1.*,t2.name as service_name,t2.slug as service_slug,t3.name as customer_name,t3.mobile as customer_mobile,t4.name as uploaded_by_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('old_client_data t1');
        $this->db->join('services t2','t1.service_id=t2.id','left');
        $this->db->join('customers t3','t1.user_id=t3.user_id','left');
        $this->db->join('users t4','t1.uploaded_by=t4.id','left');
        $this->db->order_by('t1.added_on','DESC');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updateoldclientdata($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        $data['updated_on']=date('Y-m-d H:i:s');
        if($this->db->update("old_client_data",$data,$where)){
            return array("status"=>true,"message"=>"Old Data Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function deleteoldclientdata($id){
        $where=array("id"=>$id);
        $data=array('status'=>0,'updated_on'=>date('Y-m-d H:i:s'));
        if($this->db->update("old_client_data",$data,$where)){
            // Also delete the physical file
            $old_data=$this->getoldclientdata($where,'single');
            if(!empty($old_data) && !empty($old_data['file_path'])){
                $file_path=FCPATH.$old_data['file_path'];
                if(file_exists($file_path)){
                    @unlink($file_path);
                }
            }
            return array("status"=>true,"message"=>"Old Data Deleted Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function bulkupdategst($enable = 1){
        $datetime = date('Y-m-d H:i:s');
        $data = array(
            'gst_enabled' => $enable,
            'updated_on' => $datetime
        );
        
        // Update all customers
        if($this->db->update("customers", $data)){
            $affected_rows = $this->db->affected_rows();
            $message = $enable == 1 
                ? "GST (18%) enabled successfully for all customers!" 
                : "GST (18%) disabled successfully for all customers!";
            return array(
                "status" => true, 
                "message" => $message,
                "affected_rows" => $affected_rows
            );
        }
        else{
            $error = $this->db->error();
            return array(
                "status" => false, 
                "message" => $error['message'] ?: "Failed to update GST settings"
            );
        }
    }
    
}
