<?php
class Wallet_model extends CI_Model{
	
	function __construct(){
		parent::__construct(); 
		$this->db->db_debug = false;
	}
	
    public function addtowallet($data){
        $merchant_transaction_id=generatetransactionid();
        $datetime=date('Y-m-d H:i:s');
        $data['merchant_transaction_id']=$merchant_transaction_id;
        $data['merchant_user_id']=$data['user_id'];
        $data['added_on']=$data['updated_on']=$datetime;
        if($this->db->insert("wallet",$data)){
            return array("status"=>true,"message"=>"Transaction Created! Proceed to Payment!",
                         'merchant_transaction_id'=>$merchant_transaction_id,'merchant_user_id'=>$data['merchant_user_id']);
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function updatepayment($data,$where){
        $where2=$where;
        $where2['status']=0;
        $datetime=date('Y-m-d H:i:s');
        $data['updated_on']=$datetime;
        if($this->db->get_where('wallet',$where2)->num_rows()!=0){
            if($this->db->update("wallet",$data,$where)){
                return array("status"=>true,"message"=>"Payment Successful! Wallet Amount Updated!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Payment Already Approved!");
        }
    }
    
    public function getwallet($where=array(),$type="all"){
        $this->db->where($where);
        $query=$this->db->get("wallet");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function getwalletbalance($user_id){
        $balance=0;
        $this->db->select_sum('amount');
        $this->db->where(['user_id'=>$user_id,'status'=>1]);
        $wallet=$this->db->get("wallet")->unbuffered_row()->amount;
        $wallet=!empty($wallet)?$wallet:0;
        $balance+=$wallet;
        
        $this->db->select_sum('amount');
        $this->db->where(['user_id'=>$user_id]);
        $purchases=$this->db->get("purchases")->unbuffered_row()->amount;
        $purchases=!empty($purchases)?$purchases:0;
        $balance-=$purchases;
        
        $this->db->select_sum('amount');
        $this->db->where(['user_id'=>$user_id]);
        $acc_payment=$this->db->get("acc_payment")->unbuffered_row()->amount;
        $acc_payment=!empty($acc_payment)?$acc_payment:0;
        $balance-=$acc_payment;
        
        // Subtract security deposit
        $security_deposit=$this->getsecuritydeposit($user_id);
        $balance-=$security_deposit;
        
        return $balance;
    }
    
    public function getsecuritydeposit($user_id){
        $this->db->select_sum('amount');
        $this->db->where(['user_id'=>$user_id]);
        $security=$this->db->get("security_deposit")->unbuffered_row()->amount;
        return !empty($security)?$security:0;
    }
    
    public function addsecuritydeposit($data){
        $datetime=date('Y-m-d H:i:s');
        $data['added_on']=$data['updated_on']=$datetime;
        if($this->db->insert("security_deposit",$data)){
            return array("status"=>true,"message"=>"Security Deposit Added Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function updatesecuritydeposit($data){
        $datetime=date('Y-m-d H:i:s');
        $data['updated_on']=$datetime;
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        if($this->db->update("security_deposit",$data,$where)){
            return array("status"=>true,"message"=>"Security Deposit Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getsecuritydeposits($where=array(),$type="all"){
        $columns="t1.*,t2.name as customer_name,t2.mobile";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('security_deposit t1');
        $this->db->join('customers t2','t1.user_id=t2.user_id','left');
        $this->db->order_by('t1.added_on','desc');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function deletesecuritydeposit($id){
        if($this->db->delete("security_deposit",array("id"=>$id))){
            return array("status"=>true,"message"=>"Security Deposit Deleted Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function gettransactions($where1=array(),$where2=array(),$order_by="date"){
        $columns1="concat('transaction-',md5(concat('transaction',id))) as id,date,amount,
                    merchant_transaction_id as transaction_id,'' as remarks,updated_on as added_on,'credit' as trans_type,'topup' as type,'' as service_name";
        $this->db->select($columns1);
        $this->db->where($where1);
        $this->db->from('wallet');
        $sql1=$this->db->get_compiled_select();
        
        $columns2="concat('purchase-',md5(concat('purchase',t1.id))) as id,t1.date,t1.amount,
                    md5(concat('purchase',t1.id)) as transaction_id,concat('Purchased ',t2.name) as remarks,t1.added_on,'debit' as trans_type,'service_purchase' as type,t2.name as service_name";
        $this->db->select($columns2);
        $this->db->where($where2);
        $this->db->from('purchases t1');
        $this->db->join('services t2','t1.service_id=t2.id');
        $sql2=$this->db->get_compiled_select();
        
        $columns3="concat('acc_payment-',md5(concat('acc_payment',t1.id))) as id,t1.date,t1.amount,
                    md5(concat('acc_payment',t1.id)) as transaction_id,concat('Accountancy Payment of ',MONTHNAME(t2.date),'-',YEAR(t2.date)) as remarks,t1.added_on,'debit' as trans_type,'acc_payment' as type,'' as service_name";
        $this->db->select($columns3);
        $this->db->where($where2);
        $this->db->from('acc_payment t1');
        $this->db->join('accountancy t2','t1.acc_date=t2.date and t1.firm_id=t2.firm_id');
        $sql3=$this->db->get_compiled_select();
        
        // Security Deposit transactions excluded from wallet view
        // $columns4="concat('security_deposit-',md5(concat('security_deposit',t1.id))) as id,t1.date,t1.amount,
        //             md5(concat('security_deposit',t1.id)) as transaction_id,COALESCE(t1.remarks,'Security Deposit') as remarks,t1.added_on,'debit' as trans_type,'security_deposit' as type";
        // $this->db->select($columns4);
        // $this->db->where($where2);
        // $this->db->from('security_deposit t1');
        // $sql4=$this->db->get_compiled_select();
        
        $query = $this->db->query($sql1 . ' UNION ' . $sql2.' UNION ' . $sql3.' ORDER BY '.$order_by);

        // Get the result
        $result = $query->result_array();
        return $result;
    }
    
    public function makeaccountancypayment($data){
        $datetime=date('Y-m-d H:i:s');
        $data['added_on']=$data['updated_on']=$datetime;
        $month=date('m',strtotime($data['acc_date']));
        $year=date('Y',strtotime($data['acc_date']));
        $where="user_id='$data[user_id]' and firm_id='$data[firm_id]' and year='$data[year]' and month(acc_date)='$month' and year(acc_date)='$year'";
        if($this->db->get_where('acc_payment',$where)->num_rows()==0){
            if($this->db->insert("acc_payment",$data)){
                return array("status"=>true,"message"=>"Accountancy Payment Done Successfully");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>"Accountancy Payment already paid for this month!");
        }
    }
    
    
}
