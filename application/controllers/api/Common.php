<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Common extends RestController{
	function __construct(){
		parent::__construct();
        logrequest();
	}
    
	public function getservices_post(){
        $token=$this->post('token');
        if(!empty($token)){
            $verify=$this->account->verify_token($token);
            if($verify!==false){
                $where=['status'=>1];
                $services=$this->master->getservices($where);
                if(!empty($services)){
                    foreach($services as $key=>$service){
                        $services[$key]['types']=explode(',',$service['type']);
                        $services[$key]['services_for']=explode(',',$service['service_for']);
                    }
                    $this->response([
                        'status' => true,
                        'services' => $services], RestController::HTTP_OK);
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "No Service Available!"], RestController::HTTP_OK);
                }
            }
            else{
                $this->response([
                    'status' => false,
                    'message' => "User Not Logged In!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function getpackages_post(){
        $packages=array(['id'=>1,'name'=>'Accountancy Prime'],['id'=>2,'name'=>'Accountancy Premium']);
		if(!empty($packages)){
			$this->response([
				'status' => true,
				'packages' => $packages], RestController::HTTP_OK);
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "No Packages Found!"], RestController::HTTP_OK);
		}
	}	

	public function getpackagedetails_post(){
        $package_id=$this->post('package_id');
        if(!empty($package_id) && ($package_id==1 || $package_id==2)){
            $name=$package_id==1?'Accountancy Prime':'Accountancy Premium';
            $package=$this->master->getpackages(['name'=>$name]);
            if(!empty($package)){
                $this->response([
                    'status' => true,
                    'details' => $package], RestController::HTTP_OK);
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "No Packages Found!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "No Packages Found!"], RestController::HTTP_OK);
		}
	}	

	public function getstates_post(){
        $states=$this->common->getstates();
		if(!empty($states)){
			$this->response([
				'status' => true,
				'states' => $states], RestController::HTTP_OK);
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "No States Found!"], RestController::HTTP_OK);
		}
	}	

	public function getdistricts_post(){
        $state_id=$this->post('state_id');
        if(!empty($state_id)){
            $districts=$this->common->getdistricts($state_id);
            if(!empty($districts)){
                $this->response([
                    'status' => true,
                    'districts' => $districts], RestController::HTTP_OK);
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "No Districts Found!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide State ID!"], RestController::HTTP_OK);
		}
	}	

	public function getyears_post(){
        $years=getyearly();
        $this->response([
				'status' => true,
				'years' => $years], RestController::HTTP_OK);
	}	

	public function getquarters_post(){
        $year=$this->post('year');
        $year=empty($year)?date('Y'):$year;
        $quarters=getquarterly($year);
        $this->response([
				'status' => true,
				'quarters' => $quarters], RestController::HTTP_OK);
	}	

	public function getmonths_post(){
        $year=$this->post('year');
        $year=empty($year)?date('Y'):$year;
        $months=getmonths($year);
        $this->response([
				'status' => true,
				'months' => $months], RestController::HTTP_OK);
	}	

    
}
