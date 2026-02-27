<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db->db_debug = false;
    }

    /**
     * Create (or fetch existing) invoice for a given purchase order.
     *
     * @param array $order Row from purchases table (joined via Service_model::getpurchases or getpurchasedservices)
     * @return array ['status' => bool, 'message' => string, 'invoice' => array]
     */
    public function create_for_order($order)
    {
        $result = ['status' => false, 'message' => 'Invalid order data', 'invoice' => []];

        if (empty($order) || empty($order['id'])) {
            return $result;
        }

        $order_id = (int)$order['id'];

        // If invoice already exists, just return it
        $existing = $this->get_invoice(['order_id' => $order_id], 'single');
        if (!empty($existing)) {
            return ['status' => true, 'message' => 'Invoice already exists', 'invoice' => $existing];
        }

        $datetime = date('Y-m-d H:i:s');

        // Basic monetary values (fallbacks to keep it backward compatible)
        $subtotal   = isset($order['subtotal']) ? (float)$order['subtotal'] : (float)$order['amount'];
        $gst_amount = isset($order['gst_amount']) ? (float)$order['gst_amount'] : 0.0;
        $total      = isset($order['amount']) ? (float)$order['amount'] : ($subtotal + $gst_amount);

        // GST rate if gst_amount is present
        $gst_rate = 0.0;
        if ($subtotal > 0 && $gst_amount > 0) {
            $gst_rate = round(($gst_amount * 100) / $subtotal, 2);
        }

        // Basic party / firm info (joined columns may or may not be present)
        $billing_name    = !empty($order['name']) ? $order['name'] : '';
        $billing_email   = !empty($order['email']) ? $order['email'] : '';
        $billing_mobile  = !empty($order['mobile']) ? $order['mobile'] : '';
        $firm_name       = !empty($order['firm_name']) ? $order['firm_name'] : '';
        $firm_gstin      = !empty($order['firm_gstin']) ? $order['firm_gstin'] : '';
        $firm_pan        = !empty($order['firm_pan']) ? $order['firm_pan'] : '';

        $invoice_data = [
            'order_id'        => $order_id,
            'user_id'         => !empty($order['user_id']) ? (int)$order['user_id'] : 0,
            'firm_id'         => !empty($order['firm_id']) ? (int)$order['firm_id'] : 0,
            'year'            => !empty($order['year']) ? $order['year'] : '',
            'invoice_date'    => !empty($order['date']) ? $order['date'] : date('Y-m-d'),
            'billing_name'    => $billing_name,
            'billing_email'   => $billing_email,
            'billing_mobile'  => $billing_mobile,
            'firm_name'       => $firm_name,
            'firm_gstin'      => $firm_gstin,
            'firm_pan'        => $firm_pan,
            'service_name'    => !empty($order['service_name']) ? $order['service_name'] : (!empty($order['service']) ? $order['service'] : ''),
            'type'            => !empty($order['type']) ? $order['type'] : '',
            'period_value'    => !empty($order['period_value']) ? $order['period_value'] : '',
            'subtotal'        => $subtotal,
            'gst_rate'        => $gst_rate,
            'gst_amount'      => $gst_amount,
            'total_amount'    => $total,
            'status'          => 1,
            'created_at'      => $datetime,
            'updated_at'      => $datetime,
        ];

        // Insert first so that we get auto-increment ID to compose human readable invoice_no
        if ($this->db->insert('invoices', $invoice_data)) {
            $id = (int)$this->db->insert_id();

            // Human readable invoice number (e.g. INV-2025-000123)
            $year_part = !empty($order['year']) ? $order['year'] : date('Y');
            $invoice_no = sprintf('INV-%s-%06d', $year_part, $id);

            $this->db->update('invoices', ['invoice_no' => $invoice_no, 'updated_at' => $datetime], ['id' => $id]);

            $invoice = $this->get_invoice(['id' => $id], 'single');

            return ['status' => true, 'message' => 'Invoice created', 'invoice' => $invoice];
        }

        $err = $this->db->error();
        log_message('error', 'Failed to create invoice for order ' . $order_id . ': ' . $err['message']);

        return ['status' => false, 'message' => 'Failed to create invoice: ' . $err['message'], 'invoice' => []];
    }

    /**
     * Generic getter for invoices.
     *
     * @param array|string $where
     * @param string $type 'all' or 'single'
     * @return array
     */
    public function get_invoice($where = [], $type = 'all')
    {
        $this->db->select('*');
        if (is_array($where)) {
            $this->db->where($where);
        } elseif (is_string($where) && $where !== '') {
            $this->db->where($where, null, false);
        }
        $this->db->from('invoices');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();

        if ($type === 'single') {
            return $query->unbuffered_row('array') ?: [];
        }

        return $query->result_array();
    }

    /**
     * List invoices for admin panel with optional filters.
     *
     * @param array $filters
     * @return array
     */
    public function list_invoices($filters = [])
    {
        $this->db->select('*');
        if (!empty($filters)) {
            $this->db->where($filters);
        }
        $this->db->from('invoices');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Create a standalone invoice not directly tied to a purchases/order row.
     * This is used for package-level billing and renewal billing where we
     * don't have (or don't want) a tf_purchases record.
     *
     * @param array $data
     *  Expected keys (all optional but recommended):
     *   - user_id, firm_id, year, invoice_date
     *   - billing_name, billing_email, billing_mobile
     *   - firm_name, firm_gstin, firm_pan
     *   - service_name, type, period_value
     *   - subtotal, gst_rate, gst_amount, total_amount
     * @return array ['status' => bool, 'message' => string, 'invoice' => array]
     */
    public function create_custom_invoice($data = [])
    {
        $result = ['status' => false, 'message' => 'Invalid invoice data', 'invoice' => []];

        if (empty($data) || !is_array($data)) {
            return $result;
        }

        $datetime = date('Y-m-d H:i:s');

        $subtotal   = isset($data['subtotal']) ? (float)$data['subtotal'] : 0.0;
        $gst_amount = isset($data['gst_amount']) ? (float)$data['gst_amount'] : 0.0;
        $total      = isset($data['total_amount']) ? (float)$data['total_amount'] : ($subtotal + $gst_amount);

        // Derive GST rate if not explicitly provided
        $gst_rate = isset($data['gst_rate']) ? (float)$data['gst_rate'] : 0.0;
        if ($gst_rate === 0.0 && $subtotal > 0 && $gst_amount > 0) {
            $gst_rate = round(($gst_amount * 100) / $subtotal, 2);
        }

        $invoice_data = [
            'order_id'      => !empty($data['order_id']) ? (int)$data['order_id'] : 0,
            'user_id'       => !empty($data['user_id']) ? (int)$data['user_id'] : 0,
            'firm_id'       => !empty($data['firm_id']) ? (int)$data['firm_id'] : 0,
            'year'          => !empty($data['year']) ? $data['year'] : date('Y'),
            'invoice_date'  => !empty($data['invoice_date']) ? $data['invoice_date'] : date('Y-m-d'),
            'billing_name'  => isset($data['billing_name']) ? $data['billing_name'] : '',
            'billing_email' => isset($data['billing_email']) ? $data['billing_email'] : '',
            'billing_mobile'=> isset($data['billing_mobile']) ? $data['billing_mobile'] : '',
            'firm_name'     => isset($data['firm_name']) ? $data['firm_name'] : '',
            'firm_gstin'    => isset($data['firm_gstin']) ? $data['firm_gstin'] : '',
            'firm_pan'      => isset($data['firm_pan']) ? $data['firm_pan'] : '',
            'service_name'  => isset($data['service_name']) ? $data['service_name'] : '',
            'type'          => isset($data['type']) ? $data['type'] : '',
            'period_value'  => isset($data['period_value']) ? $data['period_value'] : '',
            'subtotal'      => $subtotal,
            'gst_rate'      => $gst_rate,
            'gst_amount'    => $gst_amount,
            'total_amount'  => $total,
            'status'        => 1,
            'created_at'    => $datetime,
            'updated_at'    => $datetime,
        ];

        if ($this->db->insert('invoices', $invoice_data)) {
            $id = (int)$this->db->insert_id();

            // Human readable invoice number (e.g. INV-2025-000123)
            $year_part = !empty($invoice_data['year']) ? $invoice_data['year'] : date('Y');
            $invoice_no = sprintf('INV-%s-%06d', $year_part, $id);

            $this->db->update('invoices', ['invoice_no' => $invoice_no, 'updated_at' => $datetime], ['id' => $id]);

            $invoice = $this->get_invoice(['id' => $id], 'single');

            return ['status' => true, 'message' => 'Invoice created', 'invoice' => $invoice];
        }

        $err = $this->db->error();
        log_message('error', 'Failed to create custom invoice: ' . $err['message']);

        return ['status' => false, 'message' => 'Failed to create invoice: ' . $err['message'], 'invoice' => []];
    }
}


