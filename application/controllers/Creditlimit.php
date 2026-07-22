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
        $data['datatable'] = true;
        
        if ($this->session->role == 'admin') {
            $data['customers'] = $this->db->select('t1.id as customer_id, t1.name, t1.mobile, t1.email, t1.credit_limit, t1.user_id')
                                          ->from('customers as t1')
                                          ->get()->result_array();
                                          
            // Calculate used credit for all customers
            if (!empty($data['customers'])) {
                foreach ($data['customers'] as $key => $cust) {
                    $this->db->select_sum('amount');
                    $this->db->where(['user_id' => $cust['user_id'], 'type' => 'Credit limit']);
                    $used_credit = $this->db->get("purchases")->unbuffered_row()->amount;
                    $data['customers'][$key]['used_credit'] = !empty($used_credit) ? (float)$used_credit : 0.00;
                }
            }
            
            $this->template->load('creditlimit', 'admin_index', $data);
            return;
        }
        
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

    public function update_limit()
    {
        if ($this->session->role != 'admin') {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $customer_id = $this->input->post('customer_id');
        $credit_limit = $this->input->post('credit_limit');

        if (!empty($customer_id)) {
            $this->db->where('id', $customer_id);
            $this->db->update('customers', ['credit_limit' => $credit_limit]);
            echo json_encode(['status' => true, 'message' => 'Credit Limit updated successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Invalid Customer']);
        }
    }
}
