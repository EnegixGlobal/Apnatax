<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	function __construct(){
		parent::__construct();
        logrequest();
        checklogin();
		//checkcookie();
        if($this->session->role!='customer'){
            redirect('/');
        }
	}
	
	public function index(){
        $data=['title'=>'Accountancy Reports'];
        $data['breadcrumb']=array("active"=>"Accountancy Reports");
        $data['alertify']=true;
        $user=getuser();
        $year=$this->session->year;
        $firm_id=$this->session->firm;
        $user_id=$user['id'];
        $yearval=getyearmonthvalues($year);
        $year1=$yearval['year1'];
        $year2=$yearval['year2'];
        $from="$year1-04-01";
        $to="$year2-03-31";
        $where=array('user_id'=>$user_id,'status'=>1);
        $query=$this->db->get_where('customer_packages',$where);
        if($query->num_rows()>0){
            $where2="t1.user_id='$user_id' and t1.firm_id='$firm_id' and t1.date>='$from' and t1.date<='$to'";
            $accountancy=$this->service->getturnoverswithpayment($where2);
            $turnovers=!empty($accountancy)?array_column($accountancy,'turnover'):array(0);
            $turnover=array_sum($turnovers);
            $total_turnover=$turnover;
            $cpackage=$query->unbuffered_row('array');
            $name=$cpackage['package_id']==1?'Accountancy Prime':'Accountancy Premium';
            $package=$this->master->getpackages(['name'=>$name,'turnover>'=>$turnover],'single');
            $date=date('Y-m-d');
            $percent=2/100;
            $report=array();
            if(!empty($accountancy)){
                $total_fees=$total_other=$total_paid=$total_penalty=$total_days=0;
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
                    $paid=!empty($single['paid'])?$single['paid']:0;
                    $outstanding=$total;
                    if($single['date']!=''){
                        $acc_fees=$fees/$count;
                    }
                    else{
                        $acc_fees=0;
                    }
                    $other_fee=$single['other_fee'];
                    $total_other+=$other_fee;
                    $balance=$outstanding+$acc_fees+$other_fee;
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
                    $row=array('month'=>$month,'outstanding'=>round($outstanding,4),
                               'gto'=>round($single['turnover'],4),'acc_fees'=>round($acc_fees,4),
                               'other_fee'=>round($other_fee,4),'paid'=>round($paid,4),'balance'=>round($balance,4),
                               'due_date'=>$due_date,
                               'penalty'=>round($penalty,4),'total'=>round($total,4),'due_days'=>$days
                              );

                    $report[]=$row;
                }
                $row=array('month'=>'Total','outstanding'=>0,'gto'=>round($total_turnover,4),
                           'acc_fees'=>round($total_fees,4),'other_fee'=>round($total_other,4),
                           'paid'=>round($total_paid,4),'balance'=>0,'due_date'=>'','penalty'=>round($total_penalty,4),
                           'total'=>round(($total_fees+$total_penalty-$total_paid),4),'due_days'=>$total_days
                          );

                $report[]=$row;
                $data['report']=$report;
                $data['package']=$package;
                $this->template->load('reports','accountancy',$data);
            }
            else{
                $this->session->set_flashdata('err_msg','No Data Found!');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else{
            $this->session->set_flashdata('err_msg','Package not Active!');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
	
	public function otherfee(){
        $data=['title'=>'Other Fee Report'];
        $data['breadcrumb']=array("active"=>"Other Fee Report");
        $data['alertify']=true;
        $user=getuser();
        $year=$this->session->year;
        $firm_id=$this->session->firm;
        $user_id=$user['id'];
        $years=getyearmonthvalues($year);
        $where=array('t1.user_id'=>$user['id'],'t1.date>='=>$years['year1'].'-04-01','t1.date<='=>$years['year2'].'-03-31');
        $purchases=$this->service->getpurchases($where);
        $services=$this->master->getservices();
        $report=array();
        $row=array('service'=>'Service');
        if(empty($years)){
            if(date('d')<=31 && date('m')<4){
                $start=(date('Y')-1).'-04-01';
            }
            else{
                $start=date('Y').'-04-01';
            }
        }
        else{
            $start=date($years['year1'].'-04-01');
        }
        for($i=0;$i<12;$i++){
            $m=date('F',strtotime($start." +$i month"));
            $row[$m]=date('F-Y',strtotime($start." +$i month"));
        }
        $row['total']="Total";

        $report[]=$row;

        if(!empty($purchases)){
            $service_ids=array_column($purchases,'service_id');
            $dates=array_column($purchases,'date');
            //print_pre($service_ids);
            //print_pre($dates);
        }
        $total_amount=0;
        if(!empty($services)){
            foreach($services as $single){
                $row=array();
                $row['service']=$single['name'];
                for($i=0;$i<12;$i++){
                    $m=date('F',strtotime($start." +$i month"));
                    $row[$m]=0;
                }
                //echo $single['name'];
                $filteredDates=array();
                $indices = !empty($purchases)?array_keys($service_ids, $single['id']):array();
                if(!empty($indices)){
                    foreach($indices as $index){
                        $filteredDates[$index]=$dates[$index];
                    }
                }
                //print_pre($filteredDates);
                $total=0;

                for($i=0;$i<12;$i++){
                    $m=date('F',strtotime($start." +$i month"));
                    if(empty($month[$i])){
                        $month[$i]=0;
                    }
                    $text=0;
                    if(!empty($filteredDates)){
                        //echo date('F-Y',strtotime($start." +$i month"));
                        $first=date('Y-m-01',strtotime($start." +$i month"));
                        $last=date('Y-m-t',strtotime($start." +$i month"));
                        $searches=findDateIndices($filteredDates,$first,$last);
                        if(!empty($searches)){
                            $text=0;
                            foreach($searches as $index){
                                $text+=$purchases[$index]['amount'];
                            }
                            $total+=$text;
                            $month[$i]+=$text;
                            $text=$this->amount->toDecimal($text,false);
                        }
                    }
                    $row[$m]= $text; 
                } 
                $row['total']= $this->amount->toDecimal($total,false);
                $report[]=$row;

                $total_amount+=$total;
            }
        }
        $row=array('service'=>'Total');
        for($i=0;$i<12;$i++){
            $m=date('F',strtotime($start." +$i month"));
            $row[$m]=!empty($month[$i])?$this->amount->toDecimal($month[$i],false):0;
        }
        $row['total']=$this->amount->toDecimal($total_amount,false);
        $report[]=$row;


        if(!empty($report)){
            $data['report']=$report;
            $this->template->load('reports','otherfee',$data);
        }
        else{
            $this->session->set_flashdata('err_msg','No Reports Available!');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
	public function feereport(){
        $data=['title'=>'Fee Report'];
        $data['breadcrumb']=array("active"=>"Fee Report");
        $year=$this->session->year;
        $firm_id=$this->session->firm;
        $user=getuser();
        $folders=array();
        $folder=array();
        $folder['name']="Accountancy Report";
        $folder['count']='';
        $folder['link']=('reports/');
        $folders[]=$folder;
        $folder=array();
        $folder['name']="Other Fee Report";
        $folder['count']='';
        $folder['link']=('reports/otherfee/');
        $folders[]=$folder;
        $data['folders']=$folders;
        //print_pre($data,true);
        $data['datatable']=true;
        $data['styles']=array('file'=>'includes/custom/folder.css');
        //$data['folders']=$folders;
        $this->template->load('pages','folder-view',$data);
    }
    
}
