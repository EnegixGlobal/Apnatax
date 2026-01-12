<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Services extends RestController{
	function __construct(){
		parent::__construct();
        logrequest();
	}
    
	public function buyservice_post(){
        $token=$this->post('token');
        $service_id=$this->post('service_ids');
        $firm_id=$this->post('firm_id');
        $amount=$this->post('amount');
        $year=$this->post('year');
        $type=empty($this->post('type'))?'':$this->post('type');
        
        if(!empty($token) && !empty($service_id) && !empty($year) && !empty($firm_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $service_ids=explode(',',$service_id);
                $where=array('t1.id'=>$firm_id,"t1.user_id"=>$user['id']);
                $firm=$this->customer->getfirms($where,'single');
                if(count($service_ids)>1){
                    $this->response([
                        'status' => false,
                        'message' => "Select Only One Service!"], RestController::HTTP_OK);
                }
                elseif(!empty($firm)){
                    $where="status='1' and id ='$service_id'";
                    $service=$this->master->getservices($where,'single');
                    if(!empty($service)){
                        $service_for=$service['service_for'];
                        $types=explode(',',$service['type']);
                        $status=true;
                        $message="";
                        if($service_id==1){
                            $status=false;
                            $message="Select Package to Activate ".$service['name'];
                            if($type=='Monthly'){
                                $message="Select Package and enter Monthly Debit Amount to Activate ".$service['name'];
                            }
                        }
                        elseif(!in_array($type,$types)){
                            $status=false;
                            $message=$type." option not available for ".$service['name'];
                        }
                        elseif($service['type']=='Once'){
                            $where2="t1.user_id='$user[id]' and t1.service_id='$service_id'";
                            if($service_for=='Firm'){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            if(!empty($purchases)){
                                $status=false;
                                $message="You have already Purchased ".$service['name']."!";
                            }
                        }
                        elseif($types[0]=='Yearly' && count($types)==1){
                            $where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                            if($service_for=='Firm'){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            if(!empty($purchases)){
                                $status=false;
                                $years=getyearmonthvalues($year);
                                $message="You have already Purchased ".$service['name']." for ".$years['value']."!";
                            }
                        }
                        elseif($types[0]=='Yearly' && count($types)>1){
                            /*$where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                            if($service_for=='Firm'){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            if(!empty($purchases)){
                                $status=false;
                                $message="You have already Purchased this Service!";
                            }*/
                        }
                        if($status){
                            $service['rate']=$service_id==1?$amount:$service['rate'];
                            $total=$service['rate'];
                            if(!empty($types) && count($types)>1){
                                if($type=='Monthly'){
                                    $total=$service['rate'];
                                }
                                elseif($type=='Quarterly'){
                                    if(in_array("Monthly",$types)){
                                        $total=$service['rate']*3;
                                    }
                                    else{
                                        $total=$service['rate'];
                                    }
                                }
                                elseif($type=='Yearly'){
                                    if(in_array("Monthly",$types) && !in_array("Quarterly",$types)){
                                        $total=$service['rate']*12;
                                    }
                                    elseif(!in_array("Monthly",$types) && in_array("Quarterly",$types)){
                                        $total=$service['rate']*4;
                                    }
                                    else{
                                        $total=$service['rate'];
                                    }
                                }
                            }
                            $where=array('user_id'=>$user['id'],'status'=>1);
                            $single=array('date'=>date('Y-m-d'),'year'=>$year,'type'=>$type,'user_id'=>$user['id'],
                                          'service_id'=>$service['id'],'firm_id'=>$firm['id'],'service'=>$service['name'],
                                          'rate'=>$service['rate'],'amount'=>$total);
                            $balance=$this->wallet->getwalletbalance($user['id']);

                            if($balance>=$total){
                                $data=array($single);
                                $data['name']=$user['name'];
                                //print_pre($data,true);
                                $result=$this->service->purchaseservices($data);
                                if($result['status']==true){  
                                    $this->response([
                                        'status' => true,
                                        'message' => $result['message']], RestController::HTTP_OK);
                                }
                                else{
                                    $this->response([
                                        'status' => true,
                                        'message' => $result['message']], RestController::HTTP_OK);
                                }
                            }
                            else{
                                $remaining=$total-$balance;
                                $this->response([
                                    'status' => false,
                                    'amount'=>$remaining,
                                    'message' => "Add to Wallet"], RestController::HTTP_OK);
                            }
                        }
                        else{
                            $this->response([
                                'status' => false,
                                'message' => $message], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "No Service Selected!"], RestController::HTTP_OK);
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
                    'message' => "User Not Logged In!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	
	public function getservicetypes_post(){
        $token=$this->post('token');
        $service_id=$this->post('service_id');
        if(!empty($token) && !empty($service_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $where="status='1' and id ='$service_id'";
                $service=$this->master->getservices($where,'single');
                if(!empty($service)){
                    $types=explode(',',$service['type']);
                    $this->response([
                                    'status' => true,
                                    'types'=>$types], RestController::HTTP_OK);
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Service Not Available!"], RestController::HTTP_OK);
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
    
	public function selectpackage_post(){
        $token=$this->post('token');
        $package_id=$this->post('package_id');
        $year=$this->post('year');
        $firm_id=$this->post('firm_id');
        $type=$this->post('type');
        $amount=$this->post('amount');
        $autodebit=$this->post('autodebit');
        if(!empty($token) && !empty($year) && !empty($firm_id) && !empty($package_id) && ($package_id==1 || $package_id==2)){
            $name=$package_id==1?'Accountancy Prime':'Accountancy Premium';
            $autodebit=1;
            $package=$this->master->getpackages(['name'=>$name]);
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $where=array('user_id'=>$user['id'],'status'=>1,'firm_id'=>$firm_id,'year'=>$year);
                    $query=$this->db->get_where('customer_packages',$where);
                    $status=true;
                    $message=$name." Selected Successfully!";
                    if($query->num_rows()>0){
                        $cpackage=$query->unbuffered_row('array');
                        $p=$cpackage['package_id']==1?'Accountancy Prime':'Accountancy Premium';
                        $status=false;
                        $message="You have already selected ".$p."!";
                    }
                    if($type=="Monthly" && empty($amount)){
                        $status=false;
                        $message="Enter Monthly Debit Amount to Select Package!";
                    }
                    if($status){
                        $datetime=date('Y-m-d H:i:s');
                        $data=array('user_id'=>$user['id'],'package_id'=>$package_id,'firm_id'=>$firm_id,'year'=>$year,'status'=>1,
                                    'added_on'=>$datetime,'updated_on'=>$datetime);
                                    
                        if($type=="Monthly"){
                            $data['amount']=$amount;
                        }
                        if(!empty($autodebit) && $autodebit!=0){
                            $data['autodebit']=1;
                        }
                        $result=$this->db->insert("customer_packages",$data);
                        if($result){  
                            $this->response([
                                'status' => true,
                                'message' => $message], RestController::HTTP_OK);
                        }
                        else{
                            $this->response([
                                'status' => true,
                                'message' => $message], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                            'status' => true,
                            'message' => $message], RestController::HTTP_OK);
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
                    'message' => "User Not Logged In!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	
    
	public function switchpackage_post(){
        $token=$this->post('token');
        $package_id=$this->post('package_id');
        $firm_id=$this->post('firm_id');
        if(!empty($token) && !empty($firm_id) && !empty($package_id)){
            $name=$package_id==1?'Accountancy Prime':'Accountancy Premium';
            $package=$this->master->getpackages(['name'=>$name]);
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $where=array('user_id'=>$user['id'],'firm_id'=>$firm_id,'status'=>1);
                $query=$this->db->get_where('customer_packages',$where);
                $status=false;
                $message="No Package selected previously!";
                if($query->num_rows()>0){
                    $cpackage=$query->unbuffered_row('array');
                    if($cpackage['package_id']==$package_id){
                        $status=false;
                        $message="Package Already Selected!";
                    }
                    else{
                        $check=$this->db->get_where('customer_packages',['user_id'=>$user['id'],'package_id'=>$package_id,
                                                                         'firm_id'=>$firm_id,'status'=>2])->num_rows();
                        if($check==0){
                            $status=true;
                            $message="Package change Request Saved Successfully!";
                        }
                        else{
                            $status=false;
                            $message="Package change Request Already Saved!";
                        }
                    }
                }
                if($status){
                    $datetime=date('Y-m-d H:i:s');
                    $data=array('user_id'=>$user['id'],'package_id'=>$package_id,'firm_id'=>$firm_id,'status'=>2,
                                'added_on'=>$datetime,'updated_on'=>$datetime);
                    if(!empty($autodebit) && $autodebit!=0){
                        $data['autodebit']=1;
                    }
                    $result=$this->db->insert("customer_packages",$data);
                    if($result){  
                        $this->response([
                            'status' => true,
                            'message' => $message], RestController::HTTP_OK);
                    }
                    else{
                        $this->response([
                            'status' => true,
                            'message' => $message], RestController::HTTP_OK);
                    }
                }
                else{
                    $this->response([
                        'status' => true,
                        'message' => $message], RestController::HTTP_OK);
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
    
	public function myservices_post(){
        $token=$this->post('token');
        $firm_id=$this->post('firm_id');
        if(!empty($token) && !empty($firm_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $data=array("t1.user_id"=>$user['id'],'t1.id'=>$firm_id);
                $firm=$this->customer->getfirms($data,'single');
                if(!empty($firm)){
                    $where="t1.user_id='$user[id]'";
                    $services=$this->service->getpurchasedservices($where);
                    if(!empty($services)){
                        $this->response([
                            'status' => true,
                            'services' => $services], RestController::HTTP_OK);
                    }
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "No Service Purchased!"], RestController::HTTP_OK);
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
                    'message' => "User Not Logged In!"], RestController::HTTP_OK);
            }
        }
        else{
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"], RestController::HTTP_OK);
        }
	}	
    
	public function mypackage_post(){
        $token=$this->post('token');
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $where=array('user_id'=>$user['id'],'status'=>1);
                $query=$this->db->get_where('customer_packages',$where);
                if($query->num_rows()>0){
                    $cpackage=$query->unbuffered_row('array');
                    $name=$cpackage['package_id']==1?'Accountancy Prime':'Accountancy Premium';
                    $package=$this->master->getpackages(['name'=>$name]);
                    $status=true;
                    $this->response([
                        'status' => true,
                        'package_id' => $cpackage['package_id'],
                        'package_name' => $name,
                        'autodebit' => $cpackage['autodebit'],
                        'package' => $package], RestController::HTTP_OK);
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "No Package selected!"], RestController::HTTP_OK);
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
    
	public function getservicefields_post(){
        $token=$this->post('token');
        $service_id=$this->post('service_id');
        if(!empty($token)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $kyc=$this->account->getkyc(['t1.user_id'=>$user['id']],'single');
                if(!empty($kyc)){
                    $where="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.status=0";
                    $service=$this->service->getpurchasedservices($where,'single');
                    if(!empty($service)){
                        $documents=$this->master->getservicedocuments(['t1.service_id'=>$service_id]);
                        $finaldocuments=array();
                        if(!empty($documents)){
                            foreach($documents as $key=>$field){
                                $value='';
                                $editable=true;
                                if($field['document_id']==1){
                                    $value=$user['mobile'];
                                    $editable=false;
                                }
                                elseif($field['document_id']==2){
                                    $value=$user['email'];
                                    $editable=false;
                                }
                                elseif($field['document_id']==3){
                                    $value=$kyc['pan'];
                                    $documents[$key]['file']=0;
                                    $editable=false;
                                }
                                elseif($field['document_id']==4){
                                    $value=$kyc['aadhar'];
                                    $documents[$key]['file']=0;
                                    $editable=false;
                                }
                                elseif($field['document_id']==3 || $field['document_id']==4){
                                    unset($documents[$key]);
                                    continue;
                                }
                                $documents[$key]['field_value']=$value;
                                $documents[$key]['editable']=$editable;
                                $finaldocuments[]=$documents[$key];
                            }
                            $type=!empty($service['purchased_type'])?$service['purchased_type']:'';
                            $this->response([
                                'status' => true,
                                'documents' => $finaldocuments,
                                'type'=> $type], RestController::HTTP_OK);
                        }
                        else{
                            $this->response([
                                'status' => false,
                                'message' => "Required Documents Not Added! Please Try Again Later!"], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "Service Not Purchased!"], RestController::HTTP_OK);
                    }
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "KYC Not Uploaded! Please Upload KYC before Submitting this Form!"], RestController::HTTP_OK);
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

	public function saveformdata_post(){
        $token=$this->post('token');
        $order_id=$this->post('order_id');
        $year=$this->post('year');
        $month=$this->post('month');
        $firm_id=$this->post('firm_id');
        $formdata=$this->post('formdata');
        $formdata=json_decode($formdata,true);
        if(!empty($token) && !empty($order_id) && !empty($firm_id) && ((!empty($formdata) && is_array($formdata)) || !empty($_FILES))){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $kyc=$this->account->getkyc(['t1.user_id'=>$user['id']],'single');
                if(!empty($kyc)){
                    $where="t1.user_id='$user[id]' and t1.id='$order_id' and t1.firm_id='$firm_id'";
                    $order=$this->service->getpurchasedservices($where,'single');
                    if(!empty($order)){
                        $documents=$this->master->getservicedocuments(['t1.service_id'=>$order['service_id']]);
                        if(!empty($documents)){
                            $message=array();
                            $data=array();
                            $date=date('Y-m-d');
                            foreach($documents as $document){
                                $slug=$document['slug'];
                                $single=array();
                                if($document['value']==1 && isset($formdata[$slug]) && $document['document_id']!=3 && 
                                   $document['document_id']!=4){
                                    $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                    'service_id'=>$order['service_id'],'field'=>$slug,
                                                    'field_id'=>$document['id'],'value'=>$formdata[$slug]);
                                }
                                if($document['document_id']==3){
                                    $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                    'service_id'=>$order['service_id'],'field'=>$slug,
                                                    'field_id'=>$document['id'],'value'=>$kyc['pan']);
                                }
                                elseif($document['document_id']==4){
                                    $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                    'service_id'=>$order['service_id'],'field'=>$slug,
                                                    'field_id'=>$document['id'],'value'=>$kyc['aadhar']);
                                }
                                $newslug='';
                                if($document['file']>0){
                                    $upload_path='./assets/service/documents/';
                                    $allowed_types=$document['file_type'];
                                    if($document['file']==1){
                                         $newslug=$slug.'-file';
                                        if(isset($_FILES[$newslug]['tmp_name'])){
                                            $upload=upload_file($newslug,$upload_path,$allowed_types,$newslug);
                                            if($upload['status']===true){
                                                $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                  
                                                                'service_id'=>$order['service_id'],'field'=>$newslug,
                                                                'field_id'=>$document['id'],'value'=>$upload['path']);
                                            }
                                            else{
                                                $message[]=$document['display_name'].' File.'.$upload['msg'];
                                            }
                                        }
                                        elseif($document['document_id']==3){ 
                                            $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                            'service_id'=>$order['service_id'],'field'=>$newslug,
                                                            'field_id'=>$document['id'],
                                                            'value'=>str_replace(file_url(),'',$kyc['pan_image']));
                                        }
                                        elseif($document['document_id']==4){ 
                                            $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                            'service_id'=>$order['service_id'],'field'=>$newslug,'field_id'=>$document['id'],
                                           'value'=>str_replace(file_url(),'',$kyc['aadhar_image']));
                                        }
                                        else{
                                            $message[]=$document['display_name'].' File';
                                        }
                                    }
                                    elseif($document['file']==2){
                                        for($i=1;$i<=2;$i++){
                                            $newslug=$slug.'-file-'.$i;
                                            if(isset($_FILES[$newslug]['tmp_name'])){
                                                $upload=upload_file($newslug,$upload_path,$allowed_types,$newslug);
                                                if($upload['status']===true){
                                                    $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                      'service_id'=>$order['service_id'],'field'=>$newslug,'field_id'=>$document['id'],
                                                     'value'=>$upload['path']);
                                                }
                                            }
                                            elseif($document['document_id']==4){ 
                                                $image=$i==1?str_replace(file_url(),'',$kyc['aadhar_image']):str_replace(file_url(),'',$kyc['aadhar_back']);
                                                $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                'service_id'=>$order['service_id'],'field'=>$slug,'field_id'=>$document['id'],
                                               'value'=>$image);
                                            }
                                            else{
                                                $message[]=$document['display_name'].' Image '.$i;
                                            }
                                        }
                                    }
                                }
                                if(empty($single) && $document['value']==1){
                                    $message[]=$document['display_name'];
                                }
                                else{
                                    $data=array_merge($data,$single);
                                }
                            }
                            $documents[]=$formdata;

                            if(!empty($data) && empty($message)){
                                if(!empty($year)){
                                    $data[]=array('date' => $date,'user_id' => $user['id'],'order_id'=>$order['id'],
                                                  'service_id'=>$order['service_id'],'field' =>$order['service_slug'].'-year',
                                                  'field_id' => 0,'value' => $year);
                                }
                                if(!empty($month)){
                                    $data[]=array('date' => $date,'user_id' => $user['id'],'order_id'=>$order['id'],
                                                  'service_id'=>$order['service_id'],'field' =>$order['service_slug'].'-month',
                                                  'field_id' => 0,'value' => $month);
                                }
                                foreach($data as $key=>$value){
                                    $data[$key]['firm_id']=$firm_id;
                                }
                                //print_pre($data,true);
                                $result=$this->service->saveformdata($data);
                                if($result['status']===true){
                                    $notifydata=array("type"=>"Documents Uploaded","user_id"=>$user['id'],'order_id'=>$order['id'],
                                                      'message'=>$user['name'].' has Successfully Uploaded the documents for '.$order['service_name'].'.',
                                                      'added_on'=>date('Y-m-d H:i:s'),'updated_on'=>date('Y-m-d H:i:s'));
                                    $this->common->savenotification($notifydata);
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
                                $message=implode(',',$message);
                                $message="You have not provided ".$message;
                                $this->response([
                                    'status' => false,
                                    'message' => $message,
                                    'documents' => $documents,'data'=>$data,'newslug'=>$newslug], RestController::HTTP_OK);
                            }
                        }
                        else{
                            $this->response([
                                'status' => false,
                                'message' => "Required Documents Not Added! Please Try Again Later!"], RestController::HTTP_OK);
                        }
                    }
                    else{
                        $this->response([
                            'status' => false,
                            'message' => "Service Not Purchased!"], RestController::HTTP_OK);
                    }
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "KYC Not Uploaded! Please Upload KYC before Submitting this Form!"], RestController::HTTP_OK);
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

	public function formpreview_post(){
        $token=$this->post('token');
        $order_id=$this->post('order_id');
        $firm_id=$this->post('firm_id');
        if(!empty($token) && !empty($order_id) && !empty($firm_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $where="t1.user_id='$user[id]' and t1.id='$order_id' and t1.firm_id='$firm_id'";
                $order=$this->service->getpurchasedservices($where,'single');
                if(!empty($order)){
                    if($order['status']==0){
                        $this->response([
                                'status' => false,
                                'message' => "Form not Submitted!"], RestController::HTTP_OK);
                    }
                    else{
                        $data=$this->service->getuploadeddocuments(['a.order_id'=>$order['id']]);
                        if(!empty($data)){
                            foreach($data as $key=>$value){
                                if(strpos($value['formvalue'],'/assets/')===0){
                                    $data[$key]['formvalue']=file_url($value['formvalue']);
                                }
                            }
                            $this->response([
                                        'status' => true,
                                        'data' => $data], RestController::HTTP_OK);
                        }
                        else{
                            $this->response([
                                    'status' => false,
                                    'message' => "Form not Submitted!"], RestController::HTTP_OK);
                        }
                        /*if(!empty($documents)){
                            $message=array();
                            $data=array();
                            $date=date('Y-m-d');
                            foreach($documents as $document){
                                $slug=$document['slug'];
                                $single=array();
                                if($document['value']==1 && isset($formdata[$slug])){
                                    $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                  'service_id'=>$order['service_id'],'field'=>$slug,'field_id'=>$document['id'],
                                                 'value'=>$formdata[$slug]);
                                }
                                if($document['file']>0){
                                    $upload_path='./assets/service/documents/';
                                    $allowed_types=$document['file_type'];
                                    if($document['file']==1){
                                        $slug.='-file';
                                        if(isset($_FILES[$slug]['tmp_name'])){
                                            $upload=upload_file($slug,$upload_path,$allowed_types,$slug);
                                            if($upload['status']===true){
                                                $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                  'service_id'=>$order['service_id'],'field'=>$slug,'field_id'=>$document['id'],
                                                 'value'=>$upload['path']);
                                            }
                                            else{
                                                $message[]=$document['display_name'].' File';
                                            }
                                        }
                                        else{
                                            $message[]=$document['display_name'].' File';
                                        }
                                    }
                                    elseif($document['file']==2){
                                        for($i=1;$i<=2;$i++){
                                            $newslug=$slug.'-file-'.$i;
                                            if(isset($_FILES[$newslug]['tmp_name'])){
                                                $upload=upload_file($newslug,$upload_path,$allowed_types,$newslug);
                                                if($upload['status']===true){
                                                    $single[]=array('date'=>$date,'user_id'=>$user['id'],'order_id'=>$order['id'],
                                                      'service_id'=>$order['service_id'],'field'=>$newslug,'field_id'=>$document['id'],
                                                     'value'=>$upload['path']);
                                                }
                                            }
                                            else{
                                                $message[]=$document['display_name'].' Image '.$i;
                                            }
                                        }
                                    }
                                }
                                if(empty($single) && $document['value']==1){
                                    $message[]=$document['display_name'];
                                }
                                else{
                                    $data=array_merge($data,$single);
                                }
                            }
                            $documents[]=$formdata;
                            if(!empty($data) && empty($message)){
                                $result=$this->service->saveformdata($data);
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
                                $message=implode(',',$message);
                                $message="You have not provided ".$message;
                                $this->response([
                                    'status' => false,
                                    'message' => $message,
                                    'documents' => $documents], RestController::HTTP_OK);
                            }
                        }
                        else{
                            $this->response([
                                'status' => false,
                                'message' => "Required Documents Not Added! Please Try Again Later!"], RestController::HTTP_OK);
                        }*/
                    }
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "Service Not Purchased!"], RestController::HTTP_OK);
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
    
    
}
