<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	function __construct(){
		parent::__construct();
        //logrequest();
		//checkcookie();
        if($this->session->role!='customer'){
            redirect('home/');
        }
	}
	
	public function index(){
        $data['title']="Profile";
        $user=getuser();
        $customer=$this->customer->getcustomers(['t1.user_id'=>$user['id']],'single');
        if(empty($customer)){
            redirect('customers/');
        }
        $data['customer']=$customer;
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['states']=state_dropdown();
        
        $options=district_dropdown($customer['parent_id']);
        $data['districts']=$options;
        
        
        $data['form']='update';
        $this->template->load('profile','profile',$data);
    }
    
	public function kyc(){
        $data['title']="KYC";
        $user=getuser();
        $customer=$this->customer->getcustomers(['t1.user_id'=>$user['id']],'single');
        $data['kyc']=$this->account->getkyc(['t1.user_id'=>$user['id']],'single');
        $this->template->load('profile','kyc',$data);
    }
    
	public function certificates(){
        $data['title']="Certificates";
        $user=getuser();
        $customer=$this->customer->getcustomers(['t1.user_id'=>$user['id']],'single');
        $data['kyc']=$this->account->getkyc(['t1.user_id'=>$user['id']],'single');
        $this->template->load('profile','certificates',$data);
    }
    
    public function download_certificate($type = ''){
        $user = getuser();
        $allowed_types = array('tds_certificate', 'gst_certificate', 'audit_report', 'income_tax_certificate');
        
        if(empty($type) || !in_array($type, $allowed_types)){
            $this->session->set_flashdata("err_msg", "Invalid certificate type!");
            redirect('profile/certificates');
        }
        
        // Get KYC data with raw file path (without file_url conversion)
        $kyc = $this->db->select($type)->where('user_id', $user['id'])->get('kyc')->row_array();
        
        if(empty($kyc) || empty($kyc[$type])){
            $this->session->set_flashdata("err_msg", "Certificate not found!");
            redirect('profile/certificates');
        }
        
        $file_path = $kyc[$type];
        $full_path = FCPATH . $file_path;
        
        // Check if file exists
        if(!file_exists($full_path)){
            $this->session->set_flashdata("err_msg", "Certificate file not found!");
            redirect('profile/certificates');
        }
        
        // Get filename from path
        $filename = basename($file_path);
        
        // Load download helper and force download
        $this->load->helper('download');
        force_download($full_path, NULL);
    }
    
	public function bankstatement(){
        $data['title']="Monthly Bank Statement";
        $user=getuser();
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        
        $where=array("t1.user_id"=>$user['id'],'t1.firm_id'=>$this->session->firm,'t1.year'=>$this->session->year);
        $statements=$this->service->getbankstatements($where,'all','t1.month desc');

        if(!empty($statements)){
            foreach($statements as $key=>$single){
                $year=getyearmonthvalues($single['year']);
                $month=getyearmonthvalues($single['month']);
                $statements[$key]['year_value']=$year['value'];
                $statements[$key]['month_value']=$month['value'];
                $statements[$key]['statement']=file_url($single['statement']);
            }
        }
        $data['statements']=$statements;
        $this->template->load('profile','bankstatement',$data);
    }
    
    public function updateprofile(){
        if($this->input->post('updateprofile')!==NULL){
            $data=$this->input->post();
            unset($data['updateprofile']);
            $user=getuser();
            //print_pre($data,true);
			$result=$this->customer->updatecustomer($data);
			if($result['status']===true){
				$this->session->set_flashdata("msg","Profile Updated Successfully!");
			}
            else{
                $this->session->set_flashdata("err_msg",$result['message']);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function updatekyc(){
        if($this->input->post('updatekyc')!==NULL){
            $data=$this->input->post();
            $user=getuser();
            //print_pre($data,true);
            $checkpan=preg_match('/^[A-Z]{5}\d{4}[A-Z]$/',$data['pan']);
            $checkaadhar=preg_match('/[0-9]{12}$/',$data['aadhar']);
            if($checkaadhar && $checkpan){
                $data=array("user_id"=>$user['id'],"pan"=>$data['pan'],"aadhar"=>$data['aadhar']);
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
                        $this->session->set_flashdata("msg",$result['message']);
                    }
                    else{
                        $this->session->set_flashdata("err_msg",$result['message']);
                    }
                }
                else{
                    $message=implode('; ',$message);
                    $this->session->set_flashdata("err_msg",$message);
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
                $this->session->set_flashdata("err_msg",$message);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function savebankstatement(){
        if($this->input->post('savebankstatement')!==NULL){
            $data=$this->input->post();
            unset($data['savebankstatement']);
            $user=getuser();
            
            $where=array("t1.user_id"=>$user['id'],'t1.id'=>$this->session->firm);
            $firm=$this->customer->getfirms($where,'single');
            if(!empty($firm)){
                $status=checkaccountancy($user,$firm['id']);
                if($status){
                    $months=getmonths($this->session->year);
                    if(!empty($months)){
                        $month_ids=array_column($months,'id');
                        $index=array_search($data['month'],$month_ids);
                        if($index!==false){
                            $month_name=$months[$index]['value'];
                            $where=array('user_id'=>$user['id'],'firm_id'=>$firm['id'],'year'=>$this->session->year,'month'=>$data['month']);
                            $check=$this->db->get_where('bank_statements',$where)->num_rows();
                            if($check==0){
                                $data=$where;
                                $upload_path='./assets/documents/statements/';
                                $allowed_types='pdf';
                                $upload=upload_file('statement',$upload_path,$allowed_types,generate_slug($user['name'].'-bank-statement-'.$data['month']));
                                
                                if($upload['status']===true){
                                    $data['statement']=$upload['path'];
                                    $upload=upload_file('creditors_statement',$upload_path,$allowed_types,
                                                        generate_slug($user['name'].'-creditors-statement-'.$data['month']));
                                
                                    if($upload['status']===true){
                                        $data['creditors_statement']=$upload['path'];
                                        $data['uploaded_by']=$user['id'];
                                        //print_pre($data,true);
                                        $result=$this->service->savebankstatement($data);
                                        if($result['status']===true){
                                            $this->session->set_flashdata("msg",$result['message']);
                                        }
                                        else{
                                            $this->session->set_flashdata("err_msg",$result['message']);
                                        }
                                    }
                                    else{
                                        $this->session->set_flashdata("err_msg",'Creditors Statement-'.$upload['msg']);
                                    }
                                }
                                else{
                                    $this->session->set_flashdata("err_msg",$upload['msg']);
                                }
                            }
                            else{
                                $this->session->set_flashdata("err_msg","Statement Already uploaded for ".$month_name);
                            }
                        }
                        else{
                            $this->session->set_flashdata("err_msg","Select Valid Month!");
                        }
                    }
                    else{
                        $this->session->set_flashdata("err_msg","Select Valid Year!");
                    }
                }
                else{
                    $this->session->set_flashdata("err_msg","Accountancy Service not Active!");
                }
			}
            else{
                $this->session->set_flashdata("err_msg","Firm not found!");
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function olddata(){
        $data['title']="Old Data";
        $user=getuser();
        $data['breadcrumb']=array("active"=>"Old Data");
        $data['datatable']=true;
        
        // Get old data for this customer
        $where=array('t1.user_id'=>$user['id'],'t1.status'=>1);
        $data['old_data']=$this->customer->getoldclientdata($where);
        
        $this->template->load('profile','olddata',$data);
    }
    
    public function downloadolddata($id=NULL){
        if($id===NULL){
            redirect('profile/olddata');
        }
        $user=getuser();
        $old_data=$this->customer->getoldclientdata(['md5(t1.id)'=>$id,'t1.user_id'=>$user['id']],'single');
        if(empty($old_data)){
            $this->session->set_flashdata("err_msg","Data not found!");
            redirect('profile/olddata');
        }
        
        $file_path=FCPATH.$old_data['file_path'];
        if(file_exists($file_path)){
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$old_data['file_name'].'"');
            header('Content-Length: '.filesize($file_path));
            readfile($file_path);
            exit;
        }
        else{
            $this->session->set_flashdata("err_msg","File not found!");
            redirect('profile/olddata');
        }
    }
    
    
    
    
}