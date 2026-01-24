<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    var $multiplier=100000;

	function __construct(){
		parent::__construct();
        logrequest();
		//checkcookie();
	}
	
	public function index(){
        $this->triggerautodebit();
        checklogin();
        $this->employee->generatecommission();
        $data=['title'=>'Dashboard'];
        $data['breadcrumb']=array("active"=>"Dashboard");
        $data['nocard']=true;
        $data['alertify']=true;
        if($this->session->role=='customer'){
            $user=getuser();
            $data['user']=$user;
        }
        elseif($this->session->role!='admin'){
            $user=getuser();
            $data['balances']=$this->employee->getemployeebalance($user['emp_id']);
        }
        $this->template->load('pages','home',$data);
    }
    
    public function workreports(){
        checklogin();
        if($this->session->role!='customer'){
            redirect('home/');
        }
        $user=getuser();
        $year=$this->session->year;
        $firm_id=$this->session->firm;
        
        $data=['title'=>'Work Reports'];
        $data['breadcrumb']=array("active"=>"Work Reports");
        $data['datatable']=true;
        $data['user']=$user;
        
        // Get completed assessments (purchases with status=4 - Assessment Report Uploaded and assessment status=1 - Completed)
        $where="t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.status=4";
        if(!empty($year)){
            $where.=" and t1.year='$year'";
        }
        
        // Join with assessments table to get the assessment file (only completed assessments)
        $this->db->select("t1.*, t2.name as service_name, t2.slug as service_slug, t3.file as assessment_file, t3.date as assessment_date, t3.remarks as assessment_remarks");
        $this->db->from('purchases t1');
        $this->db->join('services t2', 't1.service_id=t2.id', 'left');
        $this->db->join('assessments t3', 't1.id=t3.order_id and t3.status=1', 'left');
        $this->db->where($where);
        $this->db->order_by('t3.date', 'DESC');
        $query = $this->db->get();
        $data['workreports'] = $query->result_array();
        
        $this->template->load('pages','workreports',$data);
    }
    
    public function updatenotification(){
        $id=$this->input->post('id');
        $notification=$this->common->getnotifications(["md5(concat('notify-',t1.id))"=>$id],'single');
        if(!empty($notification)){
            $this->common->updatenotification(['status'=>1,'id'=>$notification['id']]);
        }
    }
    
	public function triggerautodebit(){
        $date=date('Y-m-d');
        $dues=$this->db->get_where('accountancy',['due_date'=>$date])->result_array();
        if(!empty($dues)){
            foreach($dues as $value){
                $user_id=$value['user_id'];
                $firm_id=$value['firm_id'];
                $dates=getfiscaldates($value['date']);
                $from=$dates['from'];
                $to=$dates['to'];
                $years=getyearly(date('Y',strtotime($from)));
                $year=$years[0]['id'];
                $data=array();
                $where=array('user_id'=>$user_id,'status'=>1);
                $query=$this->db->get_where('customer_packages',$where);
                if($query->num_rows()>0){
                    $cpackage=$query->unbuffered_row('array');
                    if($cpackage['autodebit']==0){
                        continue;
                    }
                    $where2="t1.user_id='$user_id' and t1.firm_id='$firm_id' and t1.date>='$from' and t1.date<='$to'";
                    $accountancy=$this->service->getturnoverswithpayment($where2);
                    $turnovers=!empty($accountancy)?array_column($accountancy,'turnover'):array(0);
                    $turnover=array_sum($turnovers);
                    $total_turnover=$turnover*$this->multiplier;
                    $name=$cpackage['package_id']==1?'Accountancy Prime':'Accountancy Premium';
                    $package=$this->master->getpackages(['name'=>$name,'turnover>'=>$turnover],'single');
                    $date=date('Y-m-d');
                    $percent=2/100;
                    $report=array();
                    $currentmonth='';
                    if(!empty($accountancy)){
                        $total_fees=$total_paid=$total_penalty=$total_days=0;
                        $outstanding=$total=0;
                        $fees=$total_turnover/$package['turnover'];
                        $fees*=$package['rate'];
                        $count=count($accountancy);
                        $last=end($accountancy);
                        if($last['date']==''){
                            $count--;
                        }
                        $acc_fees=$fees/$count;
                        foreach($accountancy as $single){
                            $days=$paid=$penalty=0;
                            $paid=$single['paid'];
                            $outstanding=$total;
                            if($single['date']!=''){
                                $acc_fees=$fees/$count;
                                $currentmonth=$single['date'];
                            }
                            else{
                                $acc_fees=0;
                            }
                            $balance=$outstanding+$acc_fees;
                            if($single['due_date']<$date && $paid<$balance){
                                $balance-=$paid;
                                $date1 = new DateTime($single['due_date']);
                                $date2 = new DateTime($date);

                                // Calculate the difference
                                $interval = $date1->diff($date2);

                                // Get the difference in days
                                $days = $interval->days;
                                $penalty=($percent*$balance);
                                if($days<30){
                                    $penalty/=30;
                                    $penalty*=$days;
                                }
                                $penalty=round($penalty);
                                $total_penalty+=$penalty;
                                $total_days+=$days;
                            }
                            else{
                                $balance-=$paid;
                            }
                            $total=$balance+$penalty;
                            $total_fees+=$acc_fees;
                            $total_paid+=$paid;
                            $month=$single['date']!=''?date('F-y',strtotime($single['date'])):'--';
                            $due_date=$single['due_date']!=''?date('d-m-Y',strtotime($single['due_date'])):'--';
                        }
                        $total_dues=$total_fees+$total_penalty-$total_paid;
                        if($total_dues>0){
                           $balance=getwalletbalance(['id'=>$user_id]);
                            if($balance<$total_dues){
                                $amount=$balance;
                            }
                            else{
                                $amount=$total_dues;
                            }
                            if($currentmonth!='' && $amount>0){
                                $data=array('date'=>$date,'user_id'=>$user_id,'firm_id'=>$firm_id,'year'=>$year,'acc_date'=>$currentmonth,
                                            'amount'=>$amount);
                                //print_pre($data);
                                $result=$this->wallet->makeaccountancypayment($data);
                            }
                        }
                    }
                }	
            }
        }
        $day=date('d');
        $date=date('Y-m-d');
        $services=$this->master->getservices("day(debit_date)='$day'");
        if(!empty($services)){
            foreach($services as $service){
                $currentmonth=date('Y-m-d');
                if($service['id']==1){
                    $where="amount>'0' and status='1'";
                    $getcustomers=$this->db->get_where('customer_packages',$where);
                    if($getcustomers->num_rows()>0){
                        $customers=$getcustomers->result_array();
                        if(!empty($customers)){
                            foreach($customers as $customer){
                                $user_id=$customer['user_id'];
                                $year=$customer['year'];
                                $balance=getwalletbalance(['id'=>$user_id]);
                                $firm_id=$customer['firm_id'];
                                $amount=$customer['amount'];
                                if($balance>=$amount){
                                    $data=array('date'=>$date,'user_id'=>$user_id,'year'=>$year,'firm_id'=>$firm_id,'acc_date'=>$currentmonth,
                                                'amount'=>$amount);
                                    //print_pre($data);
                                    $result=$this->wallet->makeaccountancypayment($data);
                                }
                            }
                        }
                    }
                }
            }
        }
        
    }
    
    public function savesessdata(){
        $year=$this->input->post('year');
        $firm=$this->input->post('firm');
        $user=getuser();
        $data['user']=$user;
        $where=array("t1.user_id"=>$user['id'],'t1.status'=>1,'t1.request!='=>1,'t1.id'=>$firm);
        $firm=$this->customer->getfirms($where,'single');
        if(!empty($firm)){
            $this->session->set_userdata(['year'=>$year,'firm'=>$firm['id']]);
            echo 1;
        }
        else{
            echo 0;
        }
    }
    
    public function paymentresponse(){
        $user=$this->input->get('user');
        $merchant_transaction_id=$this->input->get('merchant_transaction_id');
        $year=$this->input->get('year');
        $firm=$this->input->get('firm');
        $getuser=$this->account->getuser(["md5(concat('user-id-',id))"=>$user]);
        if($getuser['status']===true){
            $user=$getuser['user'];
            $data=array();
            $data['user']=md5($user['id']);
            $data['name']=$user['name'];
            $data['emp_id']=$user['emp_id'];
            $data['role']=$user['role'];
            $data['project']=PROJECT_NAME;
            if(!empty($year)){
                $data['year']=$year;
            }
            if(!empty($firm)){
                $data['firm']=$firm;
            }
            $this->session->set_userdata($data);
            
            $wallet=$this->wallet->getwallet(["merchant_transaction_id"=>$merchant_transaction_id],'single');
            $data=array('status'=>1,'payment_details'=>$details);
            if(!empty($wallet)){
                $result=$this->wallet->updatepayment($data,['id'=>$wallet['id']]);
                if($result['status']==true){ 
                    $this->session->set_flashdata("msg",$result['message']);
                }
                else{
                    $this->session->set_flashdata("err_msg",$result['message']);
                }
            }
            else{
                $this->session->set_userdata('err_msg',"Please Try Again!");
            }
        }
        else{
            $this->session->set_userdata('err_msg',"Please Try Again!");
        }
        redirect('mywallet/');
    }
    
	public function unsubscribe(){
        echo "<h1>You have unsubscribed successfully</h1>";
    }
    
	public function imager(){
        $path='./assets/images/contact-img.webp';
        $path=file_url('/assets/images/contact-img.webp');
        $path=file_url('/images/slider1.jpg');
        
        $this->load->library('imager');
        //$result=$this->imager->checkSupportedFormat('webp');
        //var_dump($result);
        /*
        $result=$this->imager->readImage($path);
        var_dump($result);*/
        //$result=$this->imager->createImage();
        //var_dump($result);
        //$images=array('images/about.webp','images/about-bk.webp','images/business.png');
        //$result=$this->imager->createAnimationwithimage($images);
        //var_dump($result);
        
        //$result=$this->imager->createAnimation();
        //var_dump($result);
        
        //$result=$this->imager->getImageDimensions($path);
        //var_dump($result);
        
        //$result=$this->imager->readColors($path);
        //var_dump($result);
        
        //$result=$this->imager->encodeImage($path);
        //var_dump($result);
        
        //$result=$this->imager->encodeImageByMediaType($path);
        //var_dump($result);
        
        //$result=$this->imager->encodeImageByPath($path);
        //var_dump($result);
        
        //$result=$this->imager->encodeImageByExtension($path);
        //var_dump($result);
        
        //$result=$this->imager->encodeImageShortcut($path);
        //var_dump($result);
        
        /*$path='./new-image.png';
        $image=$this->imager->readImage($path);
        $result=$this->imager->saveImage($image,'./new-image.jpg');
        var_dump($result);*/
        
        /*$path='./new-image.png';
        $result=$this->imager->resizeImage($path);
        var_dump($result);*/
        
        $path='./new-image.png';
        $result=$this->imager->scaleImage($path);
        var_dump($result);
        
        
    }
    
	public function image(){
        $letter=!empty($this->input->get('letter'))?$this->input->get('letter'):'P';
        create_letter_image($letter);
	}

	public function editpassword(){
        $getuser=$this->account->getuser(array("md5(id)"=>$this->session->user));
        if($getuser['status']===true){
            $data['user']=$getuser['user'];
        }
        else{
            redirect('home/');
        }
        $data['title']="Edit Password";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb']=array();
        $data['alertify']=true;
		$this->template->load('pages','editpassword',$data);
	}
    
    public function updatepassword(){
        if($this->input->post('updatepassword')!==NULL){
            $password=$this->input->post('password');
            $repassword=$this->input->post('repassword');
            $user=$this->session->user;
            if($password==$repassword){
                $result=$this->account->updatepassword(array("password"=>$password),array("md5(id)"=>$user));
                if($result['status']===true){
                    $this->session->set_flashdata('msg',$result['message']);
                }
                else{
                    $error=$result['message'];
                    $this->session->set_flashdata('err_msg',$error);
                }
            }
            else{
                $error=$result['message'];
                $this->session->set_flashdata('err_msg',"Password Do not Match!");
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function testphonepe(){
        $amount=100;
        $mobile=7739576693;
        $redirecturl=base_url('home/redirecturl');
        $callbackurl=base_url('home/callbackurl');
        //$callbackurl='https://webhook.site/7112c20b-8fec-4fef-9cb7-b61656414bfa';
        $transaction_id = 'PHPSDK' . date("ymdHis") . "payPageTest";
        $this->session->set_userdata('transaction_id',$transaction_id);
        $data=array('mobile'=>$mobile,'amount'=>$amount,'callbackurl'=>$callbackurl,'redirecturl'=>$redirecturl,
                    'user_id'=>random_string('alnum', 16),'transaction_id'=>$transaction_id);
        $this->load->helper('phonepe');
        $url=createTransaction($data);
        redirect($url);
    }
    
    public function redirecturl(){
        $result=$this->input->post();
        print_pre($result);
        $result=$this->input->get();
        print_pre($result);
        $result=$this->input->cookie();
        print_pre($result);
        //$result=$this->input->server();
        //print_pre($result);
        $result=$this->input->raw_input_stream;
        print_pre($result);
        $result= $this->input->request_headers();
        print_pre($result);
    }
    
    public function callbackurl(){
	    header("Content-Type: application/json");
	    header("Access-Control-Allow-Origin: *");
	    header("Access-Control-Allow-headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	    header("X-Frame-Options: DENY");
	    header("X-XSS-Protection: 1;node=block");
	    header("X-XSS-Type-Options: nosniff");
        // Retrieve the raw POST data
        $postData = file_get_contents("php://input");

        // Parse JSON data (if applicable)
        $decodedData = json_decode($postData, true);

        // Log the raw POST data
        file_put_contents("./webhook_log.txt", $postData . PHP_EOL, FILE_APPEND);

        // Log the decoded data (if applicable)
        if ($decodedData !== null) {
            file_put_contents("./webhook_log.txt", print_r($decodedData, true) . PHP_EOL, FILE_APPEND);
        }

        // Your webhook logic goes here
        // Handle the incoming data as needed

        // Respond to the webhook provider (optional)
        echo "Webhook received successfully.";
    }
    
    public function redirecturl2(){
	    header("Content-Type: application/json");
	    header("Access-Control-Allow-Origin: *");
	    header("Access-Control-Allow-headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	    header("X-Frame-Options: DENY");
	    header("X-XSS-Protection: 1;node=block");
	    header("X-XSS-Type-Options: nosniff");
	    $rawdata=file_get_contents("php://input");
	    $data=json_decode($rawdata,true);
	    print_pre($data);
        $this->load->helper('phonepe');
        $result=checkPaymentStatus($this->session->transaction_id);
	    print_pre($result);
    }
    
    public function testmail(){
        $email=$this->input->get('email');
        $agentname=$adminname="Atal";
        $agentemail=$adminemail=($email===NULL)?["atal.prateek@tripledotss.com"]:["atal.prateek@tripledotss.com",$email];
        $name="Lead Name";
        $mobile=$mobile="Lead Mobile";
        $service="Lead Service";
        $source="Lead Source";
        if($this->input->get('type')=='admin'){
            adminmail($adminname,$adminemail,$name,$mobile,$service,$source);
        }
        if($this->input->get('type')=='agent'){
            agentmail($agentname,$agentemail,$name,$mobile,$service);
        }
        
    }
    
    public function runquery(){
        $query=array(
            "ALTER TABLE `tf_acc_payment` ADD `year` VARCHAR(20) NOT NULL AFTER `firm_id`;"
        );
        foreach($query as $sql){
            if(!$this->db->query($sql)){
                print_r($this->db->error());
            }
        }
    }
    
    public function clearlogs(){
        $query=array(
                    'TRUNCATE `tf_request_log`;');
        foreach($query as $sql){
            if(!$this->db->query($sql)){
                print_r($this->db->error());
            }
        }
    }
    
    public function matchcolumns(){
        $tables=$this->db->query("show tables;")->result_array();
        foreach($tables as $table){
            $tablename=$table['Tables_in_'.DB_NAME];
            $columns=$this->db->query("DESC $tablename;")->result_array();
            echo "<h1>$tablename</h1>";
            echo "<table border='1' cellspacing='0' cellpadding='5'>";
            echo "<tr>";
            foreach($columns[0] as $key=>$value){
                echo "<td>$key</td>";
            }
            echo "</tr>";
            foreach($columns as $column){
                echo "<tr>";
                foreach($column as $key=>$value){
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    public function alldata($token=''){
		$this->load->library('alldata');
		$this->alldata->viewall($token);
	}
	
	public function gettable(){
		$this->load->library('alldata');
		$this->alldata->gettable();
	}
	
	public function updatedata(){
		$this->load->library('alldata');
		$this->alldata->updatedata();
	}
}
