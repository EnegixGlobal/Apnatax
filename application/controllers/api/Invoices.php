<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Invoices extends RestController
{
    function __construct()
    {
        parent::__construct();
        logrequest();
    }

    /**
     * GET /api/getinvoices
     * Returns a list of all invoices for the authenticated customer.
     */
    public function getinvoices_post()
    {
        $token = $this->post('token');

        if (empty($token)) {
            $this->response(['status' => false, 'message' => 'Token required!'], RestController::HTTP_OK);
            return;
        }

        $user = $this->account->verify_token($token);
        if (empty($user) || !is_array($user) || $user['role'] != 'customer') {
            $this->response(['status' => false, 'message' => 'User Not Logged In!'], RestController::HTTP_OK);
            return;
        }

        $this->load->model('Invoice_model', 'invoice_m');
        $invoices = $this->invoice_m->list_invoices(['user_id' => $user['id']]);

        if (!empty($invoices)) {
            // Append a human-friendly formatted date to each row
            foreach ($invoices as &$inv) {
                $inv['formatted_date'] = !empty($inv['invoice_date']) ? date('d/m/Y', strtotime($inv['invoice_date'])) : '';
                $inv['download_url'] = base_url('invoices/download/' . md5($inv['id']));
            }
            unset($inv);
            $this->response(['status' => true, 'invoices' => $invoices], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'No invoices found!'], RestController::HTTP_OK);
        }
    }

    /**
     * POST /api/getinvoicedetails
     * Returns full details of a single invoice.
     * Params: token, invoice_id (OR order_id)
     */
    public function getinvoicedetails_post()
    {
        $token      = $this->post('token');
        $invoice_id = $this->post('invoice_id');
        $order_id   = $this->post('order_id');

        if (empty($token)) {
            $this->response(['status' => false, 'message' => 'Token required!'], RestController::HTTP_OK);
            return;
        }

        $user = $this->account->verify_token($token);
        if (empty($user) || !is_array($user) || $user['role'] != 'customer') {
            $this->response(['status' => false, 'message' => 'User Not Logged In!'], RestController::HTTP_OK);
            return;
        }

        $this->load->model('Invoice_model', 'invoice_m');

        $inv = [];
        if (!empty($invoice_id) && is_numeric($invoice_id)) {
            $inv = $this->invoice_m->get_invoice(['id' => (int)$invoice_id, 'user_id' => $user['id']], 'single');
        } elseif (!empty($order_id) && is_numeric($order_id)) {
            $inv = $this->invoice_m->get_invoice(['order_id' => (int)$order_id, 'user_id' => $user['id']], 'single');
        }

        if (!empty($inv)) {
            $inv['formatted_date'] = !empty($inv['invoice_date']) ? date('d/m/Y', strtotime($inv['invoice_date'])) : '';
            $inv['download_url']   = base_url('invoices/download/' . md5($inv['id']));
            $this->response(['status' => true, 'invoice' => $inv], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Invoice not found!'], RestController::HTTP_OK);
        }
    }

    /**
     * POST /api/downloadinvoice
     * Generate and stream an invoice PDF for the authenticated customer.
     * Params: token, invoice_id
     */
    public function downloadinvoice_post()
    {
        $token      = $this->post('token');
        $invoice_id = $this->post('invoice_id');

        if (empty($token)) {
            $this->response(['status' => false, 'message' => 'Token required!'], RestController::HTTP_OK);
            return;
        }

        $user = $this->account->verify_token($token);
        if (empty($user) || !is_array($user) || $user['role'] != 'customer') {
            $this->response(['status' => false, 'message' => 'User Not Logged In!'], RestController::HTTP_OK);
            return;
        }

        if (empty($invoice_id) || !is_numeric($invoice_id)) {
            $this->response(['status' => false, 'message' => 'Invoice ID required!'], RestController::HTTP_OK);
            return;
        }

        $this->load->model('Invoice_model', 'invoice_m');
        $this->load->model('Service_model', 'service_m');
        $this->load->model('Customer_model', 'customer_m');
        $this->load->library('pdf');

        // Verify the invoice belongs to this user
        $invoice = $this->invoice_m->get_invoice(['id' => (int)$invoice_id, 'user_id' => $user['id']], 'single');

        if (empty($invoice)) {
            $this->response(['status' => false, 'message' => 'Invoice not found!'], RestController::HTTP_OK);
            return;
        }

        // Fetch linked order for line item details (if any)
        $order = [];
        if (!empty($invoice['order_id'])) {
            $order = $this->service_m->getpurchases(['t1.id' => $invoice['order_id']], 'single');
        }

        // For package invoices, also fetch the service package
        $package = [];
        if (!empty($invoice['type']) && strtolower($invoice['type']) === 'package') {
            if (!empty($invoice['user_id']) && !empty($invoice['firm_id']) && !empty($invoice['year'])) {
                $package = $this->customer_m->getservicepackage(
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
            $customer = $this->customer_m->getcustomers(['t1.user_id' => $invoice['user_id']], 'single');
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
            $admin_address = $this->customer_m->getaddresses(['t1.user_id' => $admin_user['id']], 'single');
            if (!empty($admin_address) && !empty($admin_address['parent_id'])) {
                $admin_state_id = $admin_address['parent_id'];
            }
        }

        $data = [
            'title'      => 'Invoice #' . $invoice['invoice_no'],
            'breadcrumb' => [],
            'invoice'    => $invoice,
            'order'      => $order,
            'package'    => $package,
            'customer_state_id' => $customer_state_id,
            'admin_state_id' => $admin_state_id,
            'admin_gstin' => $admin_gstin,
        ];

        // Render the same HTML view used by the web download
        $html = $this->load->view('invoices/view', $data, true);

        $filename = !empty($invoice['invoice_no'])
            ? $invoice['invoice_no']
            : ('invoice-' . $invoice['id']);

        // Generate raw PDF binary (does NOT stream/exit)
        $pdfBinary = $this->pdf->generateBinary($html);

        // Output PDF directly — bypasses RestController JSON response
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
        header('Content-Length: ' . strlen($pdfBinary));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdfBinary;
        exit;
    }
}
