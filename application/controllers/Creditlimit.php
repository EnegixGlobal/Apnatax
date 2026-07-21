<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Creditlimit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        checklogin();
    }

    public function index()
    {
        $data = ['title' => 'Credit Limit'];
        $data['breadcrumb'] = array("active" => "Credit Limit");
        
        // Fetch current user details
        $user = getuser();
        
        // Fetch customer record for this user
        $customer = $this->db->get_where('customers', ['user_id' => $user['id']])->unbuffered_row('array');
        
        $credit_limit = 0.00;
        if (!empty($customer) && isset($customer['credit_limit'])) {
            $credit_limit = (float)$customer['credit_limit'];
        }
        
        // Calculate used credit
        $this->db->select_sum('amount');
        $this->db->where(['user_id' => $user['id'], 'type' => 'Credit limit']);
        $used_credit = $this->db->get("purchases")->unbuffered_row()->amount;
        $used_credit = !empty($used_credit) ? (float)$used_credit : 0.00;
        
        $available_limit = $credit_limit - $used_credit;
        if ($available_limit < 0) $available_limit = 0;
        
        $data['credit_limit'] = $credit_limit;
        $data['used_credit'] = $used_credit;
        $data['available_limit'] = $available_limit;
        
        $this->template->load('creditlimit', 'index', $data);
    }
}
