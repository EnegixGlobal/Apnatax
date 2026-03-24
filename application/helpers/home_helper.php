<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('gethomedata')) {
    function gethomedata()
    {
        $CI = get_instance();
        $user = getuser();
        $user_id = $user['id'];
        $result = array();
        return $result;
    }
}

if (!function_exists('userimage')) {
    function userimage($user = NULL)
    {
        $CI = get_instance();
        if ($user === NULL) {
            $user = $CI->session->user;
        }
        $result = $CI->account->getuser(array("md5(id)" => $user));
        $photo = file_url('includes/images/avatar/img-5.jpg');
        if ($result['status'] === true) {
            $user = $result['user'];
            $photo = $user['photo'];
        }
        return $photo;
    }
}

if (!function_exists('countcustomers')) {
    function countcustomers($user = NULL)
    {
        $CI = get_instance();
        if ($user === NULL) {
            $user = $CI->session->user;
        }
        $where = array();
        if (!in_array($CI->session->role, array('ca', 'admin'), true)) {
            $where["md5(added_by)"] = $user;
        }
        $count = $CI->db->get_where('customers', $where)->num_rows();
        return $count;
    }
}

if (!function_exists('countemployees')) {
    function countemployees($user = NULL)
    {
        $CI = get_instance();
        if ($user === NULL) {
            $user = $CI->session->user;
        }
        $where = array();
        if ($CI->session->role != 'admin') {
        }
        $count = $CI->db->get_where('employees', $where)->num_rows();
        return $count;
    }
}

if (!function_exists('getnotifications')) {
    function getnotifications()
    {
        $CI = get_instance();
        $notifications = $CI->common->getnotifications(array('t1.status' => 0));
        if ($CI->session->role == 'customer') {
            $notifications = array();
        }
        return $notifications;
    }
}

if (!function_exists('countservices')) {
    function countservices()
    {
        $CI = get_instance();
        $services = $CI->master->getservices();
        return !empty($services) ? count($services) : 0;
    }
}

if (!function_exists('countpurchasedservices')) {
    function countpurchasedservices()
    {
        $CI = get_instance();
        $user = getuser();
        $year = $CI->session->year;
        $firm_id = $CI->session->firm;
        $data['user'] = $user;
        $where = "t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.year='$year'";
        $CI->db->group_by('t1.service_id');
        $services = $CI->service->getpurchasedservices($where);
        return !empty($services) ? count($services) : 0;
    }
}

if (!function_exists('countpendingservices')) {
    function countpendingservices()
    {
        $CI   = get_instance();
        $user = getuser();
        $firm_id = $CI->session->firm;

        if (empty($user['id']) || empty($firm_id)) {
            return 0;
        }

        // Count only expired & unpaid packages (payment_status=0, expiry_date <= today)
        // These are packages that failed to auto-renew due to insufficient wallet balance.
        $count = 0;
        $today = time();

        // Count expired service packages
        $all_pkgs = $CI->customer->getservicepackage(
            ['t1.user_id' => $user['id'], 't1.firm_id' => $firm_id],
            'all'
        );
        if (!empty($all_pkgs)) {
            foreach ($all_pkgs as $_pkg) {
                $exp = !empty($_pkg['expiry_date']) ? strtotime($_pkg['expiry_date']) : 0;
                $is_unpaid = empty($_pkg['payment_status']) || $_pkg['payment_status'] == 0;
                if ($exp && $exp <= $today && $is_unpaid) {
                    $count++;
                }
            }
        }

        // Count expired Account Work packages (customer_packages)
        $account_work_pkgs = $CI->db->get_where('customer_packages', [
            'user_id' => $user['id'],
            'firm_id' => $firm_id,
            'status' => 1
        ])->result_array();

        if (!empty($account_work_pkgs)) {
            foreach ($account_work_pkgs as $_acpkg) {
                $exp = !empty($_acpkg['expiry_date']) ? strtotime($_acpkg['expiry_date']) : 0;
                $is_unpaid = empty($_acpkg['payment_status']) || $_acpkg['payment_status'] == 0;
                if ($exp && $exp <= $today && $is_unpaid) {
                    $count++;
                }
            }
        }

        return $count;
    }
}

if (!function_exists('countfirms')) {
    function countfirms()
    {
        $CI = get_instance();
        $user = getuser();
        $where = array("t1.user_id" => $user['id'], 't1.status' => 1, 't1.request!=' => 1);
        $firms = $CI->customer->getfirms($where);
        return !empty($firms) ? count($firms) : 0;
    }
}

if (!function_exists('getwalletbalance')) {
    function getwalletbalance($user = NULL)
    {
        $CI = get_instance();
        $user = $user === NULL ? getuser() : $user;
        $balance = $CI->wallet->getwalletbalance($user['id']);
        return $balance;
    }
}

if (!function_exists('countworkreports')) {
    function countworkreports()
    {
        $CI = get_instance();
        $user = getuser();
        $year = $CI->session->year;
        $firm_id = $CI->session->firm;

        // Count completed assessments (purchases with status=4 and assessment status=1)
        $where = "t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.status=4";
        if (!empty($year)) {
            $where .= " and t1.year='$year'";
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

if (!function_exists('countmessages')) {
    function countmessages()
    {
        $CI = get_instance();
        $user = getuser();
        $where = array("receiver_id" => $user['id'], 'status' => 0);
        $count = $CI->db->get_where('chats', $where)->num_rows();
        return $count;
    }
}
