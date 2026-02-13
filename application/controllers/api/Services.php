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
        $service_option=$this->post('service_option'); // Service option for dynamic pricing
        $period_value=$this->post('period_value'); // Period value for Monthly/Quarterly/Yearly
        
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
                        
                        // Check if this service has dynamic options
                        $has_service_options=false;
                        $service_options_pricing=array();
                        $service_options_display_names=array();
                        $selected_option_display='';
                        
                        // Check if this service has dynamic options
                        $service_options=$this->master->getserviceoptionspricing($service_id);
                        if(!empty($service_options['pricing']) && !empty($service_option)){
                            $service_options_pricing=$service_options['pricing'];
                            $service_options_display_names=$service_options['display_names'];
                            
                            // Validate selected option exists
                            if(in_array($service_option, array_keys($service_options_pricing))){
                                $has_service_options=true;
                                // Get pricing for selected option
                                $amount=$service_options_pricing[$service_option];
                                // Get display name for selected option
                                $selected_option_display=isset($service_options_display_names[$service_option])?
                                    $service_options_display_names[$service_option]:
                                    ucfirst(str_replace('-',' ',$service_option));
                                // Update service name to include the option
                                $service['name']=trim($service['name']).' - '.$selected_option_display;
                                // For services with options, default to Yearly type if not specified
                                if(empty($type) || !in_array($type, $types)){
                                    $type=in_array('Yearly', $types)?'Yearly':(count($types)>0?$types[0]:'Yearly');
                                }
                            }
                            else{
                                $status=false;
                                $message="Invalid option selected for ".$service['name'];
                            }
                        }
                        
                        if($service_id==1){
                            $status=false;
                            $message="Select Package to Activate ".$service['name'];
                            if($type=='Monthly'){
                                $message="Select Package and enter Monthly Debit Amount to Activate ".$service['name'];
                            }
                        }
                        elseif(!$has_service_options && !in_array($type,$types)){
                            $status=false;
                            $message=$type." option not available for ".$service['name'];
                        }
                        elseif($has_service_options && !empty($service_option)){
                            // Handle services with dynamic options - check for duplicate purchase of same option
                            $where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                            if(!empty($firm_id)){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            // If period_value is provided, also check for it
                            if(!empty($period_value)){
                                $where2.=" and t1.period_value='$period_value'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            if(!empty($purchases)){
                                // Check if this specific option was already purchased
                                foreach($purchases as $purchase){
                                    // First check service_option column (most reliable)
                                    if(!empty($purchase['service_option']) && $purchase['service_option']==$service_option){
                                        $status=false;
                                        $years=getyearmonthvalues($year);
                                        $period_msg='';
                                        if(!empty($period_value)){
                                            $period_info=getyearmonthvalues($period_value);
                                            $period_msg=' for '.$period_info['value'];
                                        }
                                        else{
                                            $period_msg=' for '.$years['value'];
                                        }
                                        $message="You have already Purchased ".$service['name']." (".$selected_option_display.")".$period_msg."!";
                                        break;
                                    }
                                }
                            }
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
                            // If period_value is provided, also check for it
                            if(!empty($period_value)){
                                $where2.=" and t1.period_value='$period_value'";
                            }
                            // For services with options, duplicate check already handled above
                            if(!$has_service_options){
                                $purchases=$this->service->getpurchases($where2);
                                if(!empty($purchases)){
                                    $status=false;
                                    if(!empty($period_value)){
                                        $period_info=getyearmonthvalues($period_value);
                                        $message="You have already Purchased ".$service['name']." for ".$period_info['value']."!";
                                    }
                                    else{
                                        $years=getyearmonthvalues($year);
                                        $message="You have already Purchased ".$service['name']." for ".$years['value']."!";
                                    }
                                }
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
                        elseif($type=='Monthly'){
                            // Check annual limit: Maximum 12 monthly purchases per year
                            $where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year' and t1.type='Monthly'";
                            if(!empty($firm_id)){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            
                            // Check if annual limit (12 months) is reached
                            if(!empty($purchases) && count($purchases)>=12){
                                $status=false;
                                $years=getyearmonthvalues($year);
                                $message="You have reached the annual limit! You can purchase monthly services maximum 12 times per year for ".$years['value']."!";
                            }
                            elseif(!empty($period_value)){
                                // Check for duplicate purchase of specific month
                                $where3=$where2." and t1.period_value='$period_value'";
                                $existing=$this->service->getpurchases($where3);
                                if(!empty($existing)){
                                    $status=false;
                                    $period_info=getyearmonthvalues($period_value);
                                    $message="You have already Purchased ".$service['name']." for ".$period_info['value']."!";
                                }
                            }
                        }
                        elseif($type=='Quarterly'){
                            // Check annual limit: Maximum 4 quarterly purchases per year
                            $where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year' and t1.type='Quarterly'";
                            if(!empty($firm_id)){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            
                            // Check if annual limit (4 quarters) is reached
                            if(!empty($purchases) && count($purchases)>=4){
                                $status=false;
                                $years=getyearmonthvalues($year);
                                $message="You have reached the annual limit! You can purchase quarterly services maximum 4 times per year for ".$years['value']."!";
                            }
                            elseif(!empty($period_value)){
                                // Check for duplicate purchase of specific quarter
                                $where3=$where2." and t1.period_value='$period_value'";
                                $existing=$this->service->getpurchases($where3);
                                if(!empty($existing)){
                                    $status=false;
                                    $period_info=getyearmonthvalues($period_value);
                                    $message="You have already Purchased ".$service['name']." for ".$period_info['value']."!";
                                }
                            }
                        }
                        elseif($type=='Yearly'){
                            // Check annual limit: Maximum 1 yearly purchase per year
                            $where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year' and t1.type='Yearly'";
                            if(!empty($firm_id)){
                                $where2.=" and t1.firm_id='$firm_id'";
                            }
                            $purchases=$this->service->getpurchases($where2);
                            
                            // Check if annual limit (1 yearly) is reached
                            if(!empty($purchases) && count($purchases)>=1){
                                $status=false;
                                $years=getyearmonthvalues($year);
                                $message="You have reached the annual limit! You can purchase yearly services maximum 1 time per year for ".$years['value']."!";
                            }
                            elseif(!empty($period_value)){
                                // Check for duplicate purchase of specific year period
                                $where3=$where2." and t1.period_value='$period_value'";
                                $existing=$this->service->getpurchases($where3);
                                if(!empty($existing)){
                                    $status=false;
                                    $period_info=getyearmonthvalues($period_value);
                                    $message="You have already Purchased ".$service['name']." for ".$period_info['value']."!";
                                }
                            }
                        }
                        
                        if($status){
                            // Use custom amount for services with options, otherwise use service rate
                            if($has_service_options && !empty($amount)){
                                $subtotal=floatval($amount);
                                $service['rate']=$subtotal; // Update rate for display
                            }
                            else{
                                $service['rate']=$service_id==1?$amount:$service['rate'];
                                $subtotal=$service['rate'];
                            }
                            
                            if(!$has_service_options && !empty($types) && count($types)>1){
                                if($type=='Monthly'){
                                    $subtotal=$service['rate'];
                                }
                                elseif($type=='Quarterly'){
                                    if(in_array("Monthly",$types)){
                                        $subtotal=$service['rate']*3;
                                    }
                                    else{
                                        $subtotal=$service['rate'];
                                    }
                                }
                                elseif($type=='Yearly'){
                                    if(in_array("Monthly",$types) && !in_array("Quarterly",$types)){
                                        $subtotal=$service['rate']*12;
                                    }
                                    elseif(!in_array("Monthly",$types) && in_array("Quarterly",$types)){
                                        $subtotal=$service['rate']*4;
                                    }
                                    else{
                                        $subtotal=$service['rate'];
                                    }
                                }
                            }
                            
                            // Check if GST is enabled for this customer
                            $customer=$this->customer->getcustomers(['t1.user_id'=>$user['id']],'single');
                            $gst_enabled=!empty($customer) && !empty($customer['gst_enabled']) && $customer['gst_enabled']==1;
                            $gst_amount=0;
                            $total=$subtotal;
                            
                            if($gst_enabled){
                                // Calculate 18% GST
                                $gst_amount=round(($subtotal*18)/100, 2);
                                $total=$subtotal+$gst_amount;
                            }
                            
                            $where=array('user_id'=>$user['id'],'status'=>1);
                            $single=array('date'=>date('Y-m-d'),'year'=>$year,'type'=>$type,'user_id'=>$user['id'],
                                          'service_id'=>$service['id'],'firm_id'=>$firm['id'],'service'=>$service['name'],
                                          'rate'=>$service['rate'],'subtotal'=>$subtotal,'gst_amount'=>$gst_amount,
                                          'gst_enabled'=>$gst_enabled?1:0,'amount'=>$total);
                            
                            // Store period value for Monthly/Quarterly/Yearly purchases
                            if(!empty($period_value) && ($type=='Monthly' || $type=='Quarterly' || $type=='Yearly')){
                                $single['period_value']=$period_value;
                            }
                            
                            // Store service option if applicable
                            if($has_service_options && !empty($service_option)){
                                $single['service_option']=$service_option;
                                $single['service_option_display']=$selected_option_display;
                            }
                            
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
    
	public function getserviceoptions_post(){
        $token=$this->post('token');
        $service_id=$this->post('service_id');
        if(!empty($token) && !empty($service_id)){
            $user=$this->account->verify_token($token);
            if(!empty($user) && is_array($user) && $user['role']=='customer'){
                $options = $this->master->getserviceoptions(array('service_id' => $service_id, 'status' => 1), 'all');
                $pricing = array();
                $display_names = array();
                if(!empty($options)){
                    foreach($options as $option){
                        $pricing[$option['option_key']] = $option['rate'];
                        $display_names[$option['option_key']] = $option['display_name'];
                    }
                    $this->response([
                        'status' => true,
                        'pricing' => $pricing,
                        'display_names' => $display_names,
                        'options' => $options
                    ], RestController::HTTP_OK);
                }
                else{
                    $this->response([
                        'status' => false,
                        'message' => "No Options Available for this Service!"], RestController::HTTP_OK);
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

