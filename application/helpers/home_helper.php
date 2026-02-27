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
    		$CI   = get_instance();
            $user = getuser();
            $year = $CI->session->year;
            $firm_id = $CI->session->firm;

            if (empty($user['id']) || empty($year) || empty($firm_id)) {
                return 0;
            }

            $pending_count  = 0;
            $renewal_ids    = array();
            $current_ids    = array();

            // Get current year service package (to know active services)
            $service_package = $CI->customer->getservicepackage(
                ['t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year],
                'single'
            );

            if (!empty($service_package) && !empty($service_package['service_ids'])) {
                $current_ids_raw = explode(',', $service_package['service_ids']);
                $current_ids = array_filter(array_map('trim', $current_ids_raw));

                if (!empty($current_ids)) {
                    // Count real pending purchases (old behaviour)
                    $service_ids_str = implode(',', array_map('intval', $current_ids));
                    $where = "t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.year='$year' and t1.status='0' and t1.service_id IN ($service_ids_str)";
                    $CI->db->group_by('t1.service_id');
                    $services = $CI->service->getpurchasedservices($where);
                    $pending_count = !empty($services) ? count($services) : 0;
                }
            }

            /**
             * Renewal candidates (same as pendingservices page logic):
             * - Services that were in any expired package (previous years)
             * - But are NOT present in the current year's package
             * These should also be reflected in the dashboard count.
             */
            $prefix = $CI->db->dbprefix;
            $table  = $prefix . 'service_packages';

            $user_id_escaped = $CI->db->escape($user['id']);
            $firm_id_escaped = $CI->db->escape($firm_id);
            $year_escaped    = $CI->db->escape($year);

            $expired_sql = "
                SELECT *
                FROM {$table}
                WHERE user_id = {$user_id_escaped}
                  AND firm_id = {$firm_id_escaped}
                  AND year != {$year_escaped}
            ";
            $expired_packages = $CI->db->query($expired_sql)->result_array();

            if (!empty($expired_packages)) {
                foreach ($expired_packages as $expired_package) {
                    if (empty($expired_package['service_ids'])) {
                        continue;
                    }
                    $expired_ids = array_filter(array_map('trim', explode(',', $expired_package['service_ids'])));
                    foreach ($expired_ids as $sid) {
                        if ($sid === '') {
                            continue;
                        }
                        // Skip if already in current year's package
                        if (in_array($sid, $current_ids, true)) {
                            continue;
                        }
                        // Avoid duplicates across multiple expired packages
                        if (in_array($sid, $renewal_ids, true)) {
                            continue;
                        }
                        $renewal_ids[] = $sid;
                    }
                }
            }

            $renewal_count = count($renewal_ids);

            return $pending_count + $renewal_count;
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

	if(!function_exists('countworkreports')) {
  		function countworkreports() {
    		$CI = get_instance();
            $user=getuser();
            $year=$CI->session->year;
            $firm_id=$CI->session->firm;
            
            // Count completed assessments (purchases with status=4 and assessment status=1)
            $where="t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.status=4";
            if(!empty($year)){
                $where.=" and t1.year='$year'";
            }
            
            $CI->db->select("COUNT(DISTINCT t1.id) as count");
            $CI->db->from('purchases t1');
            $CI->db->join('assessments t3', 't1.id=t3.order_id and t3.status=1', 'left');
            $CI->db->where($where);
            $CI->db->where('t3.id IS NOT NULL');
            $query = $CI->db->get();
            $result = $query->row_array();
            return !empty($result['count']) ? $result['count'] : 0;
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
