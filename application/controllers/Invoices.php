<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        checklogin();
        $this->load->model('Invoice_model', 'invoice');
        $this->load->model('Service_model', 'service');
        // Load customer model to resolve package details for package invoices
        $this->load->model('Customer_model', 'customer');
        $this->load->library('pdf');
    }

    /**
     * List all invoices (admin/employee) or current customer's invoices.
     */
    public function index()
    {
        $data['title'] = 'Invoices';
        $data['breadcrumb'] = array("active" => "Invoices");
        $data['datatable'] = true;

        $filters = [];
        if ($this->session->role === 'customer') {
            $user = getuser();
            $filters['user_id'] = $user['id'];
        }

        $data['invoices'] = $this->invoice->list_invoices($filters);

        $this->template->load('invoices', 'list', $data);
    }

    /**
     * View / print a single invoice.
     *
     * @param string|null $id Encrypted (md5) id or plain numeric id
     */
    public function view($id = null)
    {
        if (empty($id)) {
            redirect('invoices/');
            return;
        }

        // Accept both md5(id) and plain id
        $invoice = $this->invoice->get_invoice(['md5(id)' => $id], 'single');
        if (empty($invoice) && ctype_digit($id)) {
            $invoice = $this->invoice->get_invoice(['id' => (int)$id], 'single');
        }

        if (empty($invoice)) {
            $this->session->set_flashdata('err_msg', 'Invoice not found!');
            redirect('invoices/');
            return;
        }

        // Restrict customer to own invoices only
        if ($this->session->role === 'customer') {
            $user = getuser();
            if ((int)$invoice['user_id'] !== (int)$user['id']) {
                $this->session->set_flashdata('err_msg', 'Access Denied!');
                redirect('invoices/');
                return;
            }
        }

        // Fetch order for line item details (if linked to purchases)
        $order = [];
        if (!empty($invoice['order_id'])) {
            $order = $this->service->getpurchases(['t1.id' => $invoice['order_id']], 'single');
        }

        // For package invoices (created from service package), also fetch package + services
        $package = [];
        if (!empty($invoice['type']) && strtolower($invoice['type']) === 'package') {
            if (!empty($invoice['user_id']) && !empty($invoice['firm_id']) && !empty($invoice['year'])) {
                $package = $this->customer->getservicepackage(
                    [
                        't1.user_id' => $invoice['user_id'],
                        't1.firm_id' => $invoice['firm_id'],
                        't1.year'    => $invoice['year'],
                    ],
                    'single'
                );
            }
        }

        // Get customer state for GST calculation
        $customer_state_id = null;
        if (!empty($invoice['user_id'])) {
            $customer = $this->customer->getcustomers(['t1.user_id' => $invoice['user_id']], 'single');
            if (!empty($customer) && !empty($customer['parent_id'])) {
                $customer_state_id = $customer['parent_id'];
            }
        }

        // Get admin/company state and GSTIN (from admin user address and user record)
        $admin_state_id = null;
        $admin_gstin = null;
        // Get admin user (assuming role is 'admin' or 'superadmin')
        $admin_user = $this->db->select('id, gstin')->where_in('role', ['admin', 'superadmin'])->limit(1)->get('users')->row_array();
        if (!empty($admin_user)) {
            // Get admin GSTIN
            if (!empty($admin_user['gstin'])) {
                $admin_gstin = $admin_user['gstin'];
            }
            // Get admin address for state
            $admin_address = $this->customer->getaddresses(['t1.user_id' => $admin_user['id']], 'single');
            if (!empty($admin_address) && !empty($admin_address['parent_id'])) {
                $admin_state_id = $admin_address['parent_id'];
            }
        }

        $data = [
            'title'    => 'Invoice #' . $invoice['invoice_no'],
            'breadcrumb' => array('invoices' => 'Invoices', 'active' => $invoice['invoice_no']),
            'invoice'  => $invoice,
            'order'    => $order,
            'package'  => $package,
            'customer_state_id' => $customer_state_id,
            'admin_state_id' => $admin_state_id,
            'admin_gstin' => $admin_gstin,
        ];

        $this->load->view('invoices/view', $data);
    }

    /**
     * Download invoice as PDF using Dompdf.
     *
     * @param string|null $id Encrypted (md5) id or plain numeric id
     */
    public function download($id = null)
    {
        if (empty($id)) {
            redirect('invoices/');
            return;
        }

        // Accept both md5(id) and plain id
        $invoice = $this->invoice->get_invoice(['md5(id)' => $id], 'single');
        if (empty($invoice) && ctype_digit($id)) {
            $invoice = $this->invoice->get_invoice(['id' => (int)$id], 'single');
        }

        if (empty($invoice)) {
            $this->session->set_flashdata('err_msg', 'Invoice not found!');
            redirect('invoices/');
            return;
        }

        // Restrict customer to own invoices only
        if ($this->session->role === 'customer') {
            $user = getuser();
            if ((int)$invoice['user_id'] !== (int)$user['id']) {
                $this->session->set_flashdata('err_msg', 'Access Denied!');
                redirect('invoices/');
                return;
            }
        }

        // Fetch order for line item details (if linked to purchases)
        $order = [];
        if (!empty($invoice['order_id'])) {
            $order = $this->service->getpurchases(['t1.id' => $invoice['order_id']], 'single');
        }

        // For package invoices (created from service package), also fetch package + services
        $package = [];
        if (!empty($invoice['type']) && strtolower($invoice['type']) === 'package') {
            if (!empty($invoice['user_id']) && !empty($invoice['firm_id']) && !empty($invoice['year'])) {
                $package = $this->customer->getservicepackage(
                    [
                        't1.user_id' => $invoice['user_id'],
                        't1.firm_id' => $invoice['firm_id'],
                        't1.year'    => $invoice['year'],
                    ],
                    'single'
                );
            }
        }

        // Get customer state for GST calculation
        $customer_state_id = null;
        if (!empty($invoice['user_id'])) {
            $customer = $this->customer->getcustomers(['t1.user_id' => $invoice['user_id']], 'single');
            if (!empty($customer) && !empty($customer['parent_id'])) {
                $customer_state_id = $customer['parent_id'];
            }
        }

        // Get admin/company state and GSTIN (from admin user address and user record)
        $admin_state_id = null;
        $admin_gstin = null;
        // Get admin user (assuming role is 'admin' or 'superadmin')
        $admin_user = $this->db->select('id, gstin')->where_in('role', ['admin', 'superadmin'])->limit(1)->get('users')->row_array();
        if (!empty($admin_user)) {
            // Get admin GSTIN
            if (!empty($admin_user['gstin'])) {
                $admin_gstin = $admin_user['gstin'];
            }
            // Get admin address for state
            $admin_address = $this->customer->getaddresses(['t1.user_id' => $admin_user['id']], 'single');
            if (!empty($admin_address) && !empty($admin_address['parent_id'])) {
                $admin_state_id = $admin_address['parent_id'];
            }
        }

        $data = [
            'title'    => 'Invoice #' . $invoice['invoice_no'],
            'breadcrumb' => array(),
            'invoice'  => $invoice,
            'order'    => $order,
            'package'  => $package,
            'customer_state_id' => $customer_state_id,
            'admin_state_id' => $admin_state_id,
            'admin_gstin' => $admin_gstin,
        ];

        // Render HTML from the same invoice view
        $html = $this->load->view('invoices/view', $data, true);

        $filename = !empty($invoice['invoice_no']) ? $invoice['invoice_no'] : ('invoice-' . $invoice['id']);
        $this->pdf->generate($html, $filename);
    }

    /**
     * Convenience: open invoice view by purchase/order id.
     * Used from orders / purchased services screens.
     *
     * @param int|null $order_id
     */
    public function viewbyorder($order_id = null)
    {
        if (empty($order_id) || !ctype_digit((string)$order_id)) {
            $this->session->set_flashdata('err_msg', 'Invalid order reference!');
            redirect('orders/');
            return;
        }

        $invoice = $this->invoice->get_invoice(['order_id' => (int)$order_id], 'single');
        if (empty($invoice)) {
            $this->session->set_flashdata('err_msg', 'Invoice not found for this order!');
            redirect('orders/');
            return;
        }

        redirect('invoices/view/' . md5($invoice['id']));
    }

    /**
     * Convenience: download invoice PDF by purchase/order id.
     *
     * @param int|null $order_id
     */
    public function downloadbyorder($order_id = null)
    {
        if (empty($order_id) || !ctype_digit((string)$order_id)) {
            $this->session->set_flashdata('err_msg', 'Invalid order reference!');
            redirect('orders/');
            return;
        }

        $invoice = $this->invoice->get_invoice(['order_id' => (int)$order_id], 'single');
        if (empty($invoice)) {
            $this->session->set_flashdata('err_msg', 'Invoice not found for this order!');
            redirect('orders/');
            return;
        }

        redirect('invoices/download/' . md5($invoice['id']));
    }
}


