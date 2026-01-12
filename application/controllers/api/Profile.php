<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Profile extends RestController{
	function __construct(){
		parent::__construct();
        logrequest();
	}
    
    public function getprofile_post(){
        $token=$this->post('token');
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $firstLetter = !empty($user['name'])?strtoupper(substr($user['name'], 0, 1)):'T';
                $photo=!empty($user['photo'])?file_url($user['photo']):base_url('profileimage/?letter='.$firstLetter);
                $result=array('name'=>$user['name'],'mobile'=>$user['mobile'],'email'=>$user['email'],'photo'=>$photo);
                $this->response([
                        'status' => true,
                        'profile' => $result], RestController::HTTP_OK);
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
	public function updateprofile_post(){
        $token=$this->post('token');
        $name=$this->post('name');
        $mobile=$this->post('mobile');
        $email=$this->post('email');
        
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array();
                $upload_path='./assets/images/profile/';
                $allowed_types='gif|jpg|jpeg|png|svg';
                $upload=upload_file('photo',$upload_path,$allowed_types,generate_slug($user['name']));
                if($upload['status']===true){
                    $this->load->library('imager');
                    $path = $this->imager->processimage('.'.$upload['path'],'cropscale',80,['width'=>300,'height'=>300]);
                    $data['photo']=$path;
                }
                if(!empty($name)){
                    $data['name']=$name;
                }
                if(!empty($email)){
                    $data['email']=$email;
                }
                if(!empty($data)){
                    $result=$this->account->updateuser($data,array("id"=>$user['id']));
                    if($result['status']===true){
                        $data['name']=isset($data['name'])?$data['name']:$user['name'];
                        $data['email']=isset($data['email'])?$data['email']:$user['email'];
                        $data['photo']=isset($data['photo'])?file_url($data['photo']):file_url($user['photo']);
                        $this->response([
                            'status' => true,
                            'message' => $result['message'],'profile'=>$data], RestController::HTTP_OK);
                    }	
                    else{
                        $this->response([
                            'status' => false,
                            'message' => $result['message']], RestController::HTTP_OK);
                    }
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Please Try Again!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	
    
	public function saveaddress_post(){
        $token=$this->post('token');
        $address=$this->post('address');
        $state_id=$this->post('state_id');
        $district_id=$this->post('district_id');
        $pincode=$this->post('pincode');
        
        if(!empty($token) && !empty($address) && !empty($state_id) && !empty($district_id) && !empty($pincode)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("user_id"=>$user['id'],"address"=>$address,"parent_id"=>$state_id,"area_id"=>$district_id,
                            "pincode"=>$pincode);
                $getstate=$this->db->get_where("area",array("id"=>$data['parent_id'],'type'=>'State'));
                $getdistrict=$this->db->get_where("area",array("id"=>$data['area_id'],"parent_id"=>$data['parent_id'],'type'=>'District'));
                if($getdistrict->num_rows()==1 && $getstate->num_rows()==1){
                    $data['state']=$getstate->unbuffered_row()->name;
                    $data['district']=$getdistrict->unbuffered_row()->name;

                    //print_pre($data,true);
                    $result=$this->account->saveaddress($data);
                    if($result['status']===true){
                        $this->response([
                            'status' => true,
                            'message' => $result['message']], RestController::HTTP_OK);
                    }	
                    else{
                        $this->response([
                            'status' => false,
                            'message' => $result['message']], RestController::HTTP_OK);
                    }
                }	
                else{
                    $message=$getstate->num_rows()==0?'State':'District';
                    $message.=" not found!";
                    $this->response([
                        'status' => false,
                        'message' => $message], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

    public function getaddress_post(){
        $token=$this->post('token');
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $address=$this->account->getaddress(['t1.user_id'=>$user['id']],'single');
                if(!empty($address)){
                    $this->response([
                            'status' => true,
                            'address' => $address], RestController::HTTP_OK);
                }		
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Address not added!"], RestController::HTTP_OK);
                }
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
	public function savekyc_post(){
        $token=$this->post('token');
        $pan=$this->post('pan');
        $aadhar=$this->post('aadhar');
        
        if(!empty($token) && !empty($pan) && !empty($aadhar)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $checkpan=preg_match('/^[A-Z]{5}\d{4}[A-Z]$/',$pan);
                $checkaadhar=preg_match('/[0-9]{12}$/',$aadhar);
                if($checkaadhar && $checkpan){
                    $data=array("user_id"=>$user['id'],"pan"=>$pan,"aadhar"=>$aadhar);
                    $status=true;
                    $message=array();
                    $upload_path='./assets/images/profile/kyc/';
                    $allowed_types='gif|jpg|jpeg|png|svg';
                    $upload=upload_file('pan_image',$upload_path,$allowed_types,generate_slug($user['name'].'-pan'));
                    if($upload['status']===true){
                        $data['pan_image']=$upload['path'];
                    }
                    else{
                        $status=false;
                        $message[]="PAN- ".trim($upload['msg']);
                    }
                    $upload=upload_file('aadhar_image',$upload_path,$allowed_types,generate_slug($user['name'].'-aadhar'));
                    if($upload['status']===true){
                        $data['aadhar_image']=$upload['path'];
                    }
                    else{
                        $status=false;
                        $message[]="Aadhar Front- ".trim($upload['msg']);
                    }
                    
                    $upload=upload_file('aadhar_back',$upload_path,$allowed_types,generate_slug($user['name'].'-aadhar-back'));
                    if($upload['status']===true){
                        $data['aadhar_back']=$upload['path'];
                    }
                    else{
                        $status=false;
                        $message[]="Aadhar Back- ".trim($upload['msg']);
                    }
                    
                    if($status){
                        $result=$this->account->savekyc($data);
                        if($result['status']===true){
                            $this->response([
                                'status' => true,
                                'message' => $result['message']], RestController::HTTP_OK);
                        }	
                        else{
                            $this->response([
                                'status' => false,
                                'message' => $result['message']], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $message=implode('; ',$message);
                        $this->response([
                            'status' => false,
                            'message' => $message], RestController::HTTP_OK);
                    }
                }	
                else{
                    if($checkaadhar && !$checkpan){
                        $message="Enter Valid PAN!";
                    }
                    elseif(!$checkaadhar && $checkpan){
                        $message="Enter Valid Aadhar No!";
                    }
                    else{
                        $message="Enter Valid PAN and Aadhar No!";
                    }
                    $this->response([
                        'status' => false,
                        'message' => $message], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

    public function getkyc_post(){
        $token=$this->post('token');
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $kyc=$this->account->getkyc(['t1.user_id'=>$user['id']],'single');
                if(!empty($kyc)){
                    $this->response([
                            'status' => true,
                            'kyc' => $kyc], RestController::HTTP_OK);
                }		
                else{
                    $this->response([
                        'status' => false,
                        'message' => "KYC not Uploaded!"], RestController::HTTP_OK);
                }
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
	public function addfirm_post(){
        $token=$this->post('token');
        $name=$this->post('name');
        $gstin=$this->post('gstin');
        
        if(!empty($token) && !empty($name)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("user_id"=>$user['id'],"name"=>$name,"gstin"=>$gstin);
                $result=$this->customer->addfirm($data);
                if($result['status']===true){
                    $this->response([
                        'status' => true,
                        'message' => $result['message']], RestController::HTTP_OK);
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => $result['message']], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function myfirms_post(){
        $token=$this->post('token');
        
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.status'=>1,'t1.request!='=>1);
                $firms=$this->customer->getfirms($data);
                if(!empty($firms)){
                    $this->response([
                        'status' => true,
                        'firms' => $firms], RestController::HTTP_OK);
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "No Firm Added!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function deletefirm_post(){
        $token=$this->post('token');
        $firm_id=$this->post('firm_id');
        
        if(!empty($token) && !empty($firm_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    if($firm['request']==0){
                        $this->db->update('firms',['request'=>1],['id'=>$firm['id']]);
                        $this->response([
                            'status' => true,
                            'message' => "Firm delete request saved successfully!"], RestController::HTTP_OK);
                    }	
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "Delete Request already saved!"], RestController::HTTP_OK);
                    }
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Added!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

    public function createpackage_post(){
        $token=$this->post('token');
        $firm_id=$this->post('firm_id');
        $service_ids=$this->post('service_ids');
        $year=$this->post('year');
        if(!empty($token) && !empty($service_ids) && !empty($year) && !empty($firm_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $s_ids=explode(',',$service_ids);
                    $where="status='1' and id in ('".implode("','",$s_ids)."')";
                    $services=$this->master->getservices($where);
                    if(!empty($services)){
                        $data=array('user_id'=>$user['id'],'firm_id'=>$firm_id,'year'=>$year,'service_ids'=>$service_ids);
                        $result=$this->customer->createpackage($data);
                        if($result['status']===true){
                            $this->response([
                                'status' => true,
                                'message' => $result['message']], RestController::HTTP_OK);
                        }	
                        else{
                            $this->response([
                                'status' => false,
                                'message' => $result['message']], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                                'status' => false,
                                'message' => "Service not Available"], RestController::HTTP_OK);
                    }
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"], RestController::HTTP_OK);
                }
            }		
            else{
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"], RestController::HTTP_OK);
            }
		}		
		else{
			$this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
		}
    }
    
	public function myservicepackage_post(){
        $token=$this->post('token');
        $firm_id=$this->post('firm_id');
        $year=$this->post('year');
        
        if(!empty($token) && !empty($firm_id) && !empty($year)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $data=array("t1.user_id"=>$user['id'],'t1.firm_id'=>$firm_id,'t1.year'=>$year);
                    $package=$this->customer->getservicepackage($data,'single');
                    if(!empty($package)){
                        $this->response([
                            'status' => true,
                            'package' => $package], RestController::HTTP_OK);
                    }	
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "Package not Created!"], RestController::HTTP_OK);
                    }
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	
    
	public function checkaccountancy_post(){
        $token=$this->post('token');
        $year=$this->post('year');
        $firm_id=$this->post('firm_id');
        $year=$this->post('year');
        
        if(!empty($token) && !empty($firm_id) && !empty($year)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $data=array("t1.user_id"=>$user['id']);
                    $status=checkaccountancy($user,$firm_id);
                    $this->response([
                        'status' => true,'accountancy'=>$status], RestController::HTTP_OK);
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	
    
	public function savemonthlystatement_post(){
        $token=$this->post('token');
        $firm_id=$this->post('firm_id');
        $year=$this->post('year');
        $month=$this->post('month');
        
        if(!empty($token) && !empty($firm_id) && !empty($year) && !empty($month)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $data=array("t1.user_id"=>$user['id']);
                    $status=checkaccountancy($user,$firm_id);
                    if($status){
                        $months=getmonths($year);
                        if(!empty($months)){
                            $month_ids=array_column($months,'id');
                            $index=array_search($month,$month_ids);
                            if($index!==false){
                                $month_name=$months[$index]['value'];
                                $where=array('user_id'=>$user['id'],'firm_id'=>$firm_id,'year'=>$year,'month'=>$month);
                                $check=$this->db->get_where('bank_statements',$where)->num_rows();
                                if($check==0){
                                    $data=$where;
                                    $upload_path='./assets/documents/statements/';
                                    $allowed_types='pdf';
                                    $upload=upload_file('statement',$upload_path,$allowed_types,generate_slug($user['name'].'-bank-statement-'.$month));
                                    if($upload['status']===true){
                                        $data['statement']=$upload['path'];
                                        $data['uploaded_by']=$user['id'];
                                        $result=$this->service->savebankstatement($data);
                                        if($result['status']===true){
                                            $this->response([
                                                'status' => true,
                                                'message' => $result['message']], RestController::HTTP_OK);
                                        }	
                                        else{
                                            $this->response([
                                                'status' => false,
                                                'message' => $result['message']], RestController::HTTP_OK);
                                        }
                                    }
                                    else{
                                        $this->response([
                                            'status' => false,'message'=>$upload['msg']], RestController::HTTP_OK);
                                    }
                                }
                                else{
                                    $this->response([
                                        'status' => false,'message'=>"Statement Already uploaded for ".$month_name], RestController::HTTP_OK);
                                }
                            }
                            else{
                                $this->response([
                                    'status' => false,'message'=>"Select Valid Month!"], RestController::HTTP_OK);
                            }
                        }
                        else{
                            $this->response([
                                'status' => false,'message'=>"Select Valid Year!"], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                            'status' => false,'message'=>"Accountancy Service not Active"], RestController::HTTP_OK);
                    }
                    
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

	public function getbankstatements_post(){
        $token=$this->post('token');
        $firm_id=$this->post('firm_id');
        $year=$this->post('year');
        
        if(!empty($token) && !empty($firm_id) && !empty($year)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $where=array("t1.user_id"=>$user['id'],'t1.firm_id'=>$firm_id);
                    $statements=$this->service->getbankstatements($where,'all','t1.month desc');

                    if(!empty($statements)){
                        foreach($statements as $key=>$single){
                            $year=getyearmonthvalues($single['year']);
                            $month=getyearmonthvalues($single['month']);
                            $statements[$key]['year_value']=$year['value'];
                            $statements[$key]['month_value']=$month['value'];
                            $statements[$key]['statement']=file_url($single['statement']);
                        }
                        $this->response([
                            'status' => true,'statements'=>$statements], RestController::HTTP_OK);
                    }
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "Bank Statements not Uploaded!"], RestController::HTTP_OK);
                    }
                }	
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"], RestController::HTTP_OK);
                }
            }	
            else{
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
				'status' => false,
				'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	

  

}
