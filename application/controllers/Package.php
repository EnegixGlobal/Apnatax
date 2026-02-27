<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Package extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        logrequest();
        checklogin();
        //checkcookie();
        if ($this->session->role != 'customer') {
            redirect('/');
        }
        // Load invoice model for package-level billing
        $this->load->model('Invoice_model', 'invoice');
    }

    public function index()
    {
        $data = ['title' => 'My Package'];
        $data['breadcrumb'] = array("active" => "My Package");
        $data['alertify'] = true;
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $where = array('user_id' => $user['id'], 'status' => 1);
        $query = $this->db->get_where('customer_packages', $where);
        if ($query->num_rows() > 0) {
            $data['package'] = $query->unbuffered_row('array');
        }
        $where_package = array('t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year);
        $data['service_package'] = $this->customer->getservicepackage($where_package, 'single');

        // Load all services with their options for the package view
        $where_services = array('status' => 1, 'id>' => 1);
        $all_services = $this->master->getservices($where_services);
        $services_with_options = array();
        if (!empty($all_services)) {
            foreach ($all_services as $service) {
                $service_options = $this->master->getserviceoptions(array('service_id' => $service['id'], 'status' => 1), 'all');
                $services_with_options[$service['id']] = array(
                    'service' => $service,
                    'options' => $service_options
                );
            }
        }
        $data['services_with_options'] = $services_with_options;

        $this->template->load('package', 'mypackage', $data);
    }

    public function savepackage()
    {
        if ($this->input->post('savepackage') !== NULL) {
            $data = $this->input->post();
            $user = getuser();
            $firm_id = $this->session->firm;
            $year = $this->session->year;
            $service_id = $data['service_id'];
            $service_ids = implode(',', $service_id);
            $service_ids = trim($service_ids);
            $service_ids = trim($service_ids, ',');

            // Handle service options - collect single option for each service
            $service_option_ids = array();
            if (!empty($data['service_option']) && is_array($data['service_option'])) {
                foreach ($data['service_option'] as $index => $option) {
                    if (!empty($service_id[$index]) && !empty($option)) {
                        $service_id_val = $service_id[$index];
                        // Store single option value (not array)
                        $service_option_ids[$service_id_val] = $option;
                    }
                }
            }

            // Convert to JSON for storage
            $service_option_ids_json = !empty($service_option_ids) ? json_encode($service_option_ids) : NULL;

            //print_pre($service_ids,true);
            $where = array("t1.user_id" => $user['id'], 't1.id' => $firm_id, 't1.status' => 1);
            $firm = $this->customer->getfirms($where, 'single');
            if (!empty($firm)) {
                $s_ids = explode(',', $service_ids);
                $where = "status='1' and id in ('" . implode("','", $s_ids) . "')";
                $services = $this->master->getservices($where);
                if (!empty($services)) {

                    // Check if any selected service has already been purchased directly
                    $conflict_services = array();
                    foreach ($s_ids as $sid) {
                        $sid = trim($sid);
                        if (!empty($sid) && is_numeric($sid)) {
                            $where_check = "t1.user_id='{$user['id']}' AND t1.firm_id='{$firm_id}' AND t1.year='{$year}' AND t1.service_id='{$sid}'";
                            $existing_purchase = $this->service->getpurchases($where_check);
                            if (!empty($existing_purchase)) {
                                $svc = $this->master->getservices("id='{$sid}'", 'single');
                                if (!empty($svc)) {
                                    $conflict_services[] = $svc['name'];
                                }
                            }
                        }
                    }
                    if (!empty($conflict_services)) {
                        $this->session->set_flashdata('err_msg', 'The following service(s) are already purchased directly and cannot be added to a package: ' . implode(', ', $conflict_services) . '. Please remove them or contact admin.');
                        redirect($_SERVER['HTTP_REFERER']);
                        return;
                    }

                    $data = array(
                        'user_id' => $user['id'],
                        'firm_id' => $firm_id,
                        'year' => $year,
                        'service_ids' => $service_ids,
                        'service_option_ids' => $service_option_ids_json
                    );
                    $result = $this->customer->createpackage($data);
                    if ($result['status'] === true) {
                        // Create a bill/invoice for this package configuration
                        try {
                            // Sum up rates of selected services as package subtotal
                            $subtotal = 0.0;
                            if (!empty($services)) {
                                foreach ($services as $svc) {
                                    $rate = isset($svc['rate']) ? (float)$svc['rate'] : 0.0;
                                    $subtotal += $rate;
                                }
                            }

                            // Fetch customer & firm info for billing header
                            $customer = $this->customer->getcustomers(['t1.user_id' => $user['id']], 'single');
                            $firm_info = $this->customer->getfirms(['t1.id' => $firm_id], 'single');

                            $gst_enabled = !empty($customer) && !empty($customer['gst_enabled']) && $customer['gst_enabled'] == 1;
                            $gst_rate = $gst_enabled ? 18.0 : 0.0;
                            $gst_amount = $gst_enabled ? round(($subtotal * $gst_rate) / 100, 2) : 0.0;
                            $total = $subtotal + $gst_amount;

                            $invoice_data = [
                                'user_id'        => $user['id'],
                                'firm_id'        => $firm_id,
                                'year'           => $year,
                                'invoice_date'   => date('Y-m-d'),
                                'billing_name'   => !empty($customer['name']) ? $customer['name'] : $user['name'],
                                'billing_email'  => !empty($customer['email']) ? $customer['email'] : (isset($user['email']) ? $user['email'] : ''),
                                'billing_mobile' => !empty($customer['mobile']) ? $customer['mobile'] : (isset($user['mobile']) ? $user['mobile'] : ''),
                                'firm_name'      => !empty($firm_info['name']) ? $firm_info['name'] : '',
                                'firm_gstin'     => !empty($firm_info['gstin']) ? $firm_info['gstin'] : '',
                                'firm_pan'       => !empty($firm_info['pan']) ? $firm_info['pan'] : '',
                                'service_name'   => 'Service Package (' . $year . ')',
                                'type'           => 'Package',
                                'period_value'   => $year,
                                'subtotal'       => $subtotal,
                                'gst_rate'       => $gst_rate,
                                'gst_amount'     => $gst_amount,
                                'total_amount'   => $total,
                            ];

                            $invoice_result = $this->invoice->create_custom_invoice($invoice_data);
                            if (!empty($invoice_result['status']) && $invoice_result['status'] === true) {
                                $this->session->set_flashdata('msg', $result['message'] . ' Invoice No: ' . $invoice_result['invoice']['invoice_no']);
                            } else {
                                // If invoice fails, still keep package but log error
                                log_message('error', 'Package invoice creation failed for user ' . $user['id'] . ': ' . $invoice_result['message']);
                                $this->session->set_flashdata('msg', $result['message']);
                            }
                        } catch (Exception $e) {
                            // On any unexpected error, log and proceed with original message
                            log_message('error', 'Package invoice unexpected error: ' . $e->getMessage());
                            $this->session->set_flashdata('msg', $result['message']);
                        }
                    } else {
                        $this->session->set_flashdata('err_msg', $result['message']);
                    }
                } else {
                    $this->session->set_flashdata('err_msg', 'Service Not available!');
                }
            } else {
                $this->session->set_flashdata('err_msg', 'Firm not selected!');
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function requestdelete()
    {
        // Check if request column exists
        $check_column = $this->db->query("SHOW COLUMNS FROM `tf_service_packages` LIKE 'request'");
        if ($check_column->num_rows() == 0) {
            $this->session->set_flashdata("err_msg", "Delete request feature is not available. Please contact administrator.");
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $user = getuser();
        $firm_id = $this->session->firm;
        $year = $this->session->year;
        $where = array('t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year);
        $service_package = $this->customer->getservicepackage($where, 'single');
        if (!empty($service_package)) {
            // Check if request field exists in the result
            if (!isset($service_package['request'])) {
                $service_package['request'] = 0;
            }
            // Allow request if no request (0) or if rejected (2) - can re-request after rejection
            if ($service_package['request'] == 0 || $service_package['request'] == 2) {
                if ($this->db->update('service_packages', ['request' => 1], ['id' => $service_package['id']])) {
                    $message = $service_package['request'] == 2 ? "Package Delete Request Resubmitted! Admin will review your request." : "Package Delete Request Saved! Admin will review your request.";
                    $this->session->set_flashdata("msg", $message);
                } else {
                    $this->session->set_flashdata("err_msg", "Failed to save delete request!");
                }
            } else {
                $this->session->set_flashdata("err_msg", "Delete Request already submitted!");
            }
        } else {
            $this->session->set_flashdata("err_msg", "Package not found!");
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
