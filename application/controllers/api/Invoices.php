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
}

