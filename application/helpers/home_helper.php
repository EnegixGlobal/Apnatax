<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
	if(!function_exists('gethomedata')) {
  		function gethomedata() {
            $CI = get_instance();
            $user=getuser();
            $user_id=$user['id'];
            $result=array();
            return $result;
        }
    }

	if(!function_exists('userimage')) {
  		function userimage($user=NULL) {
            $CI = get_instance();
            if($user===NULL){
                $user=$CI->session->user;
            }
            $result=$CI->account->getuser(array("md5(id)"=>$user));
            $photo=file_url('includes/images/avatar/img-5.jpg');
            if($result['status']===true){
                $user=$result['user'];
                $photo=$user['photo'];
            }
            return $photo;
        }
    }

	if(!function_exists('countcustomers')) {
  		function countcustomers($user=NULL) {
            $CI = get_instance();
            if($user===NULL){
                $user=$CI->session->user;
            }
            $where=array();
            if($CI->session->role!='admin'){
                $where["md5(added_by)"]=$user;
            }
            $count=$CI->db->get_where('customers',$where)->num_rows();
            return $count;
        }
    }

	if(!function_exists('countemployees')) {
  		function countemployees($user=NULL) {
            $CI = get_instance();
            if($user===NULL){
                $user=$CI->session->user;
            }
            $where=array();
            if($CI->session->role!='admin'){
            }
            $count=$CI->db->get_where('employees',$where)->num_rows();
            return $count;
        }
    }

	if(!function_exists('getnotifications')) {
  		function getnotifications() {
    		$CI = get_instance();
            $notifications=$CI->common->getnotifications(array('t1.status'=>0));
            if($CI->session->role=='customer'){
                $notifications=array();
            }
            return $notifications;
		}  
	}

	if(!function_exists('countservices')) {
  		function countservices() {
    		$CI = get_instance();
            $services=$CI->master->getservices();
            return !empty($services)?count($services):0;
		}  
	}

	if(!function_exists('countpurchasedservices')) {
  		function countpurchasedservices() {
    		$CI = get_instance();
            $user=getuser();
            $year=$CI->session->year;
            $firm_id=$CI->session->firm;
            $data['user']=$user;
            $where="t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.year='$year'";
            $CI->db->group_by('t1.service_id');
            $services=$CI->service->getpurchasedservices($where);
            return !empty($services)?count($services):0;
		}  
	}

	if(!function_exists('countpendingservices')) {
  		function countpendingservices() {
    		$CI = get_instance();
            $user=getuser();
            $year=$CI->session->year;
            $firm_id=$CI->session->firm;
            $data['user']=$user;
            $where="t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.year='$year' and t1.status='0'";
            $CI->db->group_by('t1.service_id');
            $services=$CI->service->getpurchasedservices($where);
            return !empty($services)?count($services):0;
		}  
	}

	if(!function_exists('countfirms')) {
  		function countfirms() {
    		$CI = get_instance();
            $user=getuser();
            $where=array("t1.user_id"=>$user['id'],'t1.status'=>1,'t1.request!='=>1);
            $firms=$CI->customer->getfirms($where);
            return !empty($firms)?count($firms):0;
		}  
	}

	if(!function_exists('getwalletbalance')) {
  		function getwalletbalance($user=NULL) {
    		$CI = get_instance();
            $user=$user===NULL?getuser():$user;
            $balance=$CI->wallet->getwalletbalance($user['id']);
            return $balance;
		}  
	}

	if(!function_exists('countmessages')) {
  		function countmessages() {
    		$CI = get_instance();
            $user=getuser();
            $where=array("receiver_id"=>$user['id'],'status'=>0);
            $count=$CI->db->get_where('chats',$where)->num_rows();
            return $count;
		}  
	}


?>
