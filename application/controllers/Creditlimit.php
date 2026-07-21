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
            $credit_limit = $customer['credit_limit'];
        }
        
        $data['credit_limit'] = $credit_limit;
        
        $this->template->load('creditlimit', 'index', $data);
    }
}
