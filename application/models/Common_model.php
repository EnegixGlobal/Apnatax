<?php
class Common_model extends CI_Model{
	
	function __construct(){
		parent::__construct(); 
		$this->db->db_debug = false;
	}
    
    public function getsources(){
        $array=$this->db->get('sources')->result_array();
        return $array;
    }
    
    public function getstates(){
        $this->db->where(array("type"=>"State"));
        $array=$this->db->get('area')->result_array();
        return $array;
    }
    
    public function getdistricts($parent_id){
        $this->db->where(array("type"=>"District","parent_id"=>$parent_id));
        $array=$this->db->get('area')->result_array();
        return $array;
    }
    
    public function savenotification($data){
        $data['added_on']=$data['updated_on']=date('Y-m-d H:i:s');
        if($this->db->insert("notify",$data)){
            return array("status"=>true,"message"=>"Notification Added Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getnotifications($where=array(),$type="all"){
        $this->db->select('t1.*');
        $this->db->where($where);
        $this->db->from('notify t1');
        //$this->db->join('notes t2','t1.task_id=t2.id','left');
        $this->db->order_by('t1.added_on desc');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatenotification($data){
        $id=$data['id'];
        unset($data['id']);
        $data['updated_on']=date('Y-m-d H:i:s');
        $where=array("id"=>$id);
        if($this->db->update("notify",$data,$where)){
            return array("status"=>true,"message"=>"Notification Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    
    public function savecontactmessage($data){
        $data['added_on']=date('Y-m-d H:i:s');
        //if($this->db->get_where('contactus',array('email'=>$data['email']))->num_rows()==0){
            if($this->db->insert("contactus",$data)){
                return array("status"=>true,"message"=>"Contact Us Added Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        /*}
        else{
            return array("status"=>false,"message"=>"Already Subscribed!");
        }*/
    }
    
    public function getcontactmessages($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("contactus");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatecontactmessage($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        //if($this->db->get_where('contactus',array("id!="=>$id))->num_rows()==0){
            if($this->db->update("contactus",$data,$where)){
                return array("status"=>true,"message"=>"Contact Us Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        /*}
        else{
            return array("status"=>false,"message"=>"Contact Us Already Added!");
        }*/
    }
    
    public function deletemessage($where){
        if($this->db->delete("contactus",$where)){
            return array("status"=>true,"message"=>"Message Delete Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function savegalleryimage($data){
        if($this->db->insert("gallery",$data)){
            return array("status"=>true,"message"=>"Gallery Image Added Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getgalleryimages($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("gallery");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updategalleryimage($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->update("gallery",$data,$where)){
            return array("status"=>true,"message"=>"Gallery Image Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function savebooking($data){
        $data['added_on']=date('Y-m-d H:i:s');
        //if($this->db->get_where('consult',array('email'=>$data['email']))->num_rows()==0){
            if($this->db->insert("consult",$data)){
                return array("status"=>true,"message"=>"Consult Booking Successful!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        /*}
        else{
            return array("status"=>false,"message"=>"Already Subscribed!");
        }*/
    }
    
    public function getbookings($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("consult");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatebooking($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        //if($this->db->get_where('consult',array('email'=>$data['email'],"id!="=>$id))->num_rows()==0){
            if($this->db->update("consult",$data,$where)){
                return array("status"=>true,"message"=>"Consult Booking Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        /*}
        else{
            return array("status"=>false,"message"=>"Subscriber Already Added!");
        }*/
    }
    
    public function deletebooking($where){
        if($this->db->delete("consult",$where)){
            return array("status"=>true,"message"=>"Consult Booking Delete Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function savereferral($data){
        $data['added_on']=date('Y-m-d H:i:s');
        if($this->db->insert("referrals",$data)){
            return array("status"=>true,"message"=>"Referral Saved Successful!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getreferrals($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("referrals");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatereferral($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->update("referrals",$data,$where)){
            return array("status"=>true,"message"=>"Referral Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function deletereferral($where){
        if($this->db->delete("referrals",$where)){
            return array("status"=>true,"message"=>"Referral Delete Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function savetestimonial($data){
        $data['added_on']=date('Y-m-d H:i:s');
        if($this->db->insert("testimonials",$data)){
            return array("status"=>true,"message"=>"Testimonial Saved Successful!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function gettestimonials($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("testimonials");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatetestimonial($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->update("testimonials",$data,$where)){
            return array("status"=>true,"message"=>"Testimonial Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function savenewsletter($data){
        $data['added_on']=date('Y-m-d H:i:s');
        if($this->db->get_where('newsletters',array('email'=>$data['email']))->num_rows()==0){
            if($this->db->insert("newsletters",$data)){
                return array("status"=>true,"message"=>"Subscriber Added Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Already Subscribed!");
        }
    }
    
    public function getnewsletters($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("newsletters");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updatenewsletter($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->get_where('newsletters',array('email'=>$data['email'],"id!="=>$id))->num_rows()==0){
            if($this->db->update("newsletters",$data,$where)){
                return array("status"=>true,"message"=>"Subscriber Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Subscriber Already Added!");
        }
    }
    
    public function deletenewsletter($where){
        if($this->db->delete("newsletters",$where)){
            return array("status"=>true,"message"=>"Subscriber Delete Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
}
