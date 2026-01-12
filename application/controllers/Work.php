<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Work extends CI_Controller {

	function __construct(){
		parent::__construct();
        //logrequest();
		//checkcookie();
        if($this->session->role!='customer'){
            redirect('home/');
        }
	}
	
	public function index(){
        $data['title']="Work Report";
        $user=getuser();
        $year=$this->session->year;
        $firm_id=$this->session->firm;
        $years=getyearmonthvalues($year);
        $from=$years['year1'].'-04-01';
        $to=$years['year2'].'-03-31';
        $where="t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.year='$year'";
        $this->db->group_by('t1.service_id');
        $data['services']=$this->service->getpurchasedservices($where);
        $this->template->load('work','workreport',$data);
    }
    
}