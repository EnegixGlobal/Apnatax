<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Services extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        logrequest();
        //checkcookie();
        if ($this->session->role != 'customer') {
            redirect('home/');
        }
        // Load invoice model for billing
        $this->load->model('Invoice_model', 'invoice');
    }

    public function index()
    {
        $data = ['title' => 'Services'];
        $data['breadcrumb'] = array("active" => "Services");
        $user = getuser();
        $data['user'] = $user;
        $firm_id = $this->session->firm;
        $year    = $this->session->year;
        $where = array();
        $services = $this->master->getservices($where);

        // Load service options for services that have dynamic options
        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $service_options = $this->master->getserviceoptions(array('service_id' => $service['id'], 'status' => 1), 'all');
                if (!empty($service_options)) {
                    $services[$key]['has_options'] = true;
                    $services[$key]['options'] = $service_options;
                } else {
                    $services[$key]['has_options'] = false;
                }
            }
        }

        // Collect service IDs that are already in the user's service package
        $package_service_ids = array();
        if (!empty($user['id']) && !empty($firm_id) && !empty($year)) {
            $service_package = $this->customer->getservicepackage(
                ['t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year],
                'single'
            );
            if (!empty($service_package) && !empty($service_package['service_ids'])) {
                $package_service_ids = array_map('trim', explode(',', $service_package['service_ids']));
                $package_service_ids = array_filter($package_service_ids);
            }
        }

        $data['services'] = $services;
        $data['package_service_ids'] = $package_service_ids;
        $data['datatable'] = true;
        $this->template->load('services', 'services', $data);
    }

    /**
     * AJAX endpoint to get service options
     */
    public function getserviceoptions()
    {
        $service_id = $this->input->post('service_id');
        if (empty($service_id)) {
            echo json_encode(array('status' => false, 'message' => 'Service ID required'));
            return;
        }

        $options = $this->master->getserviceoptions(array('service_id' => $service_id, 'status' => 1), 'all');
        $pricing = array();
        $display_names = array();

        if (!empty($options)) {
            foreach ($options as $option) {
                $pricing[$option['option_key']] = $option['rate'];
                $display_names[$option['option_key']] = $option['display_name'];
            }
        }

        echo json_encode(array(
            'status' => true,
            'pricing' => $pricing,
            'display_names' => $display_names,
            'options' => $options
        ));
    }

    public function purchasedservices()
    {
        $data = ['title' => 'Purchased Services'];
        $data['breadcrumb'] = array("active" => "Purchased Services");
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $user = getuser();
        $data['user'] = $user;

        // Validate required session data
        if (empty($year) || empty($firm_id) || empty($user['id'])) {
            $this->session->set_flashdata('err_msg', 'Please select Year and Firm!');
            redirect($_SERVER['HTTP_REFERER'] ?? 'home/');
            return;
        }

        // Use array format for WHERE clause (more reliable than string)
        $where = array(
            't1.user_id' => $user['id'],
            't1.firm_id' => $firm_id,
            't1.year' => $year
        );

        // Pass group_by as a parameter or handle it in the model
        $services = $this->service->getpurchasedservices($where, 'all', true); // Pass flag for group_by

        // Get service package to retrieve service options from package
        $service_package = $this->customer->getservicepackage(['t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year], 'single');
        $package_service_options = array();
        if (!empty($service_package) && !empty($service_package['service_option_ids'])) {
            $package_service_options = json_decode($service_package['service_option_ids'], true);
            if (!is_array($package_service_options)) {
                $package_service_options = array();
            }
        }

        // Log for debugging
        if (ENVIRONMENT !== 'production') {
            log_message('debug', 'purchasedservices - User ID: ' . $user['id'] . ', Firm ID: ' . $firm_id . ', Year: ' . $year);
            log_message('debug', 'purchasedservices - Services found: ' . count($services));
            log_message('debug', 'purchasedservices - Package service options: ' . json_encode($package_service_options));
            // Also log the WHERE clause for debugging
            log_message('debug', 'purchasedservices - WHERE: ' . json_encode($where));
        }

        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $services[$key]['name'] = $service['service_name'];
                $services[$key]['count'] = '';
                $services[$key]['link'] = ('services/monthlyservices/' . $service['service_slug']);

                // First check if service_option_display exists in purchase record (for manually purchased services)
                $service_option_display = !empty($service['service_option_display']) ? $service['service_option_display'] : '';

                // If not found in purchase record, check package service options
                if (empty($service_option_display) && !empty($package_service_options)) {
                    $service_id = $service['service_id'];
                    if (!empty($package_service_options[$service_id])) {
                        $option_id = $package_service_options[$service_id];
                        // Handle both old array format and new single value format for backward compatibility
                        if (is_array($option_id)) {
                            $option_id = !empty($option_id[0]) ? $option_id[0] : '';
                        }

                        if (!empty($option_id)) {
                            // Get option display name from service_options table
                            $option = $this->master->getserviceoptions(array('id' => $option_id, 'status' => 1), 'single');
                            if (!empty($option) && !empty($option['display_name'])) {
                                $service_option_display = $option['display_name'];
                            }
                        }
                    }
                }

                $services[$key]['service_option_display'] = $service_option_display;
            }
        } else {
            // Log when no services found for debugging
            log_message('info', 'purchasedservices - No services found for User ID: ' . $user['id'] . ', Firm ID: ' . $firm_id . ', Year: ' . $year);
        }
        $data['services'] = $services;
        //print_pre($data,true);
        $data['datatable'] = true;
        //$data['styles']=array('file'=>'includes/custom/folder.css');
        //$data['folders']=$folders;
        $this->template->load('services', 'purchasedservices', $data);
    }

    public function pendingservices()
    {
        $data = ['title' => 'Pending Services'];
        $data['breadcrumb'] = array("active" => "Purchased Services");
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $user = getuser();
        $data['user'] = $user;

        // Validate required session data
        if (empty($year) || empty($firm_id)) {
            $this->session->set_flashdata('err_msg', 'Please select Year and Firm!');
            redirect($_SERVER['HTTP_REFERER'] ?? 'home/');
            return;
        }

        // Get service package for this user/firm/year to get package service IDs
        $service_package = $this->customer->getservicepackage(['t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year], 'single');

        $services = array();
        $package_service_ids = array();

        // Get service IDs from current year package
        if (!empty($service_package) && !empty($service_package['service_ids'])) {
            $package_service_ids = explode(',', $service_package['service_ids']);
        }

        // Also check for expired packages (packages from previous years)
        // Use raw query for != operator
        $user_id_escaped = $this->db->escape($user['id']);
        $firm_id_escaped = $this->db->escape($firm_id);
        $year_escaped = $this->db->escape($year);
        $expired_query = $this->db->query("SELECT t1.* FROM tf_service_packages t1 
            LEFT JOIN tf_customers t2 ON t1.user_id=t2.user_id 
            LEFT JOIN tf_firms t3 ON t1.firm_id=t3.id 
            WHERE t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.year!={$year_escaped}");
        $expired_packages = $expired_query->result_array();

        // Collect service IDs from expired packages
        if (!empty($expired_packages)) {
            foreach ($expired_packages as $expired_package) {
                if (!empty($expired_package['service_ids'])) {
                    $expired_service_ids = explode(',', $expired_package['service_ids']);
                    $package_service_ids = array_merge($package_service_ids, $expired_service_ids);
                }
            }
        }

        // Remove duplicates
        $package_service_ids = array_unique(array_filter($package_service_ids));

        if (!empty($package_service_ids)) {
            // Filter to show pending services that are part of current or expired packages
            $service_ids_str = implode(',', array_map('intval', $package_service_ids));
            // Use proper escaping for SQL query
            $user_id_escaped = $this->db->escape($user['id']);
            $firm_id_escaped = $this->db->escape($firm_id);
            $year_escaped = $this->db->escape($year);
            $where = "t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.year={$year_escaped} AND t1.status='0' AND t1.service_id IN ($service_ids_str)";
            $services = $this->service->getpurchasedservices($where, 'all', true); // Pass flag for group_by

            // Also get pending services from expired packages
            if (!empty($expired_packages)) {
                foreach ($expired_packages as $expired_package) {
                    if (!empty($expired_package['service_ids'])) {
                        $expired_year = $expired_package['year'];
                        $expired_service_ids = explode(',', $expired_package['service_ids']);
                        $expired_service_ids_str = implode(',', array_map('intval', $expired_service_ids));
                        $expired_year_escaped = $this->db->escape($expired_year);
                        $where_expired = "t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.year={$expired_year_escaped} AND t1.status='0' AND t1.service_id IN ($expired_service_ids_str)";
                        $expired_services = $this->service->getpurchasedservices($where_expired, 'all', true);

                        // Mark expired services
                        if (!empty($expired_services)) {
                            foreach ($expired_services as $key => $expired_service) {
                                $expired_services[$key]['expired_package'] = true;
                                $expired_services[$key]['expired_year'] = $expired_year;
                            }
                            $services = array_merge($services, $expired_services);
                        }
                    }
                }
            }
        }

        /**
         * Renewal candidates:
         * - Services that existed in any expired package for this firm/user
         * - But are NOT present in the current year's package
         * These are shown as "Renew" rows without requiring an existing purchase.
         */
        $renewals = array();

        // Build quick lookup for current year package service IDs
        $current_service_ids = array();
        if (!empty($service_package) && !empty($service_package['service_ids'])) {
            $current_ids_raw = explode(',', $service_package['service_ids']);
            $current_service_ids = array_filter(array_map('trim', $current_ids_raw));
        }

        if (!empty($expired_packages)) {
            foreach ($expired_packages as $expired_package) {
                if (empty($expired_package['service_ids'])) {
                    continue;
                }
                $expired_service_ids = array_filter(array_map('trim', explode(',', $expired_package['service_ids'])));
                foreach ($expired_service_ids as $sid) {
                    if ($sid === '') {
                        continue;
                    }
                    // Skip if already in current package
                    if (in_array($sid, $current_service_ids, true)) {
                        continue;
                    }
                    // Avoid duplicates across multiple expired years
                    if (isset($renewals[$sid])) {
                        continue;
                    }

                    // Get service details
                    $service = $this->master->getservices(array('id' => $sid), 'single');
                    if (empty($service)) {
                        continue;
                    }

                    // Optional: if you want to consider debit_date, you can add checks here
                    // For now, any service from expired packages that is not in current package is a renewal candidate.

                    $renewals[$sid] = array(
                        'id' => 0,
                        'service_id' => (int)$sid,
                        'service_name' => $service['name'],
                        'service_slug' => $service['slug'],
                        'month' => '',
                        'amount' => $service['rate'],
                        'is_renewal' => 1,
                        'expired_year' => $expired_package['year']
                    );
                }
            }
        }

        // Merge real pending purchases with renewal candidates
        if (!empty($renewals)) {
            $services = array_merge($services, array_values($renewals));
        }

        $data['services'] = $services;
        //print_pre($data,true);
        $data['datatable'] = true;
        //$data['folders']=$folders;
        $this->template->load('services', 'pendingservices', $data);
    }

    /**
     * Renew a service from expired package into current year's package.
     * - Adds the service_id back into current year service_packages row (create or update).
     * - Does NOT create a purchase; this is purely package-level renewal for now.
     */
    public function renewservice()
    {
        // Only customers can renew
        if ($this->session->role != 'customer') {
            echo json_encode(array('status' => false, 'message' => 'Unauthorized request.'));
            return;
        }

        $service_id = $this->input->post('service_id');
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;

        if (empty($service_id) || !is_numeric($service_id)) {
            echo json_encode(array('status' => false, 'message' => 'Invalid service selected.'));
            return;
        }
        if (empty($user['id']) || empty($firm_id) || empty($year)) {
            echo json_encode(array('status' => false, 'message' => 'Please select Year and Firm before renewing services.'));
            return;
        }

        // Make sure service exists and is active
        $service = $this->master->getservices(array('id' => $service_id, 'status' => 1), 'single');
        if (empty($service)) {
            echo json_encode(array('status' => false, 'message' => 'Service not found or inactive.'));
            return;
        }

        // Check that this service existed in at least one expired package for this user/firm
        $user_id_escaped = $this->db->escape($user['id']);
        $firm_id_escaped = $this->db->escape($firm_id);
        $year_escaped = $this->db->escape($year);
        $service_id_int = (int)$service_id;

        $expired_check_sql = "
            SELECT t1.*
            FROM " . $this->db->dbprefix('service_packages') . " t1
            WHERE t1.user_id = {$user_id_escaped}
              AND t1.firm_id = {$firm_id_escaped}
              AND t1.year != {$year_escaped}
              AND FIND_IN_SET(" . $service_id_int . ", t1.service_ids) > 0
            LIMIT 1
        ";
        $expired_row = $this->db->query($expired_check_sql)->unbuffered_row('array');

        if (empty($expired_row)) {
            echo json_encode(array('status' => false, 'message' => 'This service is not part of any previous package for renewal.'));
            return;
        }

        // Get or create current year package
        $current_package = $this->customer->getservicepackage(
            array('t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year),
            'single'
        );

        $current_ids = array();
        if (!empty($current_package) && !empty($current_package['service_ids'])) {
            $current_ids_raw = explode(',', $current_package['service_ids']);
            $current_ids = array_filter(array_map('trim', $current_ids_raw));
        }

        // If already present in current package, nothing to do
        if (in_array((string)$service_id_int, $current_ids, true)) {
            echo json_encode(array('status' => true, 'message' => 'Service is already active in your current package.'));
            return;
        }

        $current_ids[] = (string)$service_id_int;
        $current_ids = array_unique(array_filter($current_ids));

        $data = array(
            'user_id' => $user['id'],
            'firm_id' => $firm_id,
            'year' => $year,
            'service_ids' => implode(',', $current_ids)
        );

        // Preserve existing service_option_ids if any
        if (!empty($current_package) && isset($current_package['service_option_ids'])) {
            $data['service_option_ids'] = $current_package['service_option_ids'];
        }

        $result = $this->customer->createpackage($data);

        if (!empty($result['status']) && $result['status'] === true) {
            // Create an invoice for this renewed service (package-level billing)
            try {
                // Fetch customer & firm info for billing header
                $customer = $this->customer->getcustomers(['t1.user_id' => $user['id']], 'single');
                $firm_info = $this->customer->getfirms(['t1.id' => $firm_id], 'single');

                $subtotal = isset($service['rate']) ? (float)$service['rate'] : 0.0;
                $gst_enabled = !empty($customer) && !empty($customer['gst_enabled']) && $customer['gst_enabled'] == 1;
                $gst_rate = $gst_enabled ? 18.0 : 0.0;
                $gst_amount = $gst_enabled ? round(($subtotal * $gst_rate) / 100, 2) : 0.0;
                $total = $subtotal + $gst_amount;

                // Determine a sensible type/period for renewal billing
                $types = !empty($service['type']) ? explode(',', $service['type']) : [];
                $primary_type = !empty($types[0]) ? trim($types[0]) : 'Yearly';

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
                    'service_name'   => $service['name'] . ' (Renewal)',
                    'type'           => $primary_type,
                    'period_value'   => $year,
                    'subtotal'       => $subtotal,
                    'gst_rate'       => $gst_rate,
                    'gst_amount'     => $gst_amount,
                    'total_amount'   => $total,
                ];

                if (isset($this->invoice) && method_exists($this->invoice, 'create_custom_invoice')) {
                    $invoice_result = $this->invoice->create_custom_invoice($invoice_data);
                    if (empty($invoice_result['status']) || $invoice_result['status'] !== true) {
                        log_message('error', 'Renewal invoice creation failed for user ' . $user['id'] . ', service ' . $service_id . ': ' . $invoice_result['message']);
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Renewal invoice unexpected error: ' . $e->getMessage());
            }

            // Send notification to admin that user has renewed a service in package
            $notifydata = array(
                "type"    => "Service Renewed",
                "user_id" => $user['id'],
                // No specific order_id here, this is a package-level renewal
                "message" => $user['name'] . ' has renewed service "' . $service['name'] . '" for firm ID ' . $firm_id . ' and year ' . $year . '.'
            );
            if (isset($this->common) && method_exists($this->common, 'savenotification')) {
                $this->common->savenotification($notifydata);
            }

            echo json_encode(array('status' => true, 'message' => 'Service renewed and added to your current package successfully.'));
        } else {
            $message = !empty($result['message']) ? $result['message'] : 'Failed to renew service. Please try again.';
            echo json_encode(array('status' => false, 'message' => $message));
        }
    }

    public function monthlyservices($slug = NULL)
    {
        $where = "status='1' and slug ='$slug'";
        $service = $this->master->getservices($where, 'single');
        if (empty($service)) {
            redirect('services/purchasedservices/');
        }
        $data = ['title' => $service['name']];
        $data['breadcrumb'] = array('services/purchasedservices' => 'Purchased Services', "active" => $service['name']);
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $user = getuser();
        $data['user'] = $user;
        $years = getyearmonthvalues($year);
        $from = $years['year1'] . '-04-01';
        $to = $years['year2'] . '-03-31';
        $where = "t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.year='$year' and t1.service_id='$service[id]'";
        $services = $this->service->getpurchasedservices($where);
        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $name = "<span class=\"text-danger\">Pending</span>";
                if ($service['status'] == 0) {
                    $link = 'services/openform/' . $service['service_slug'] . '/' . $service['id'];
                } else {
                    $link = 'services/previewform/' . $service['service_slug'] . '/' . $service['id'];
                }
                if ($service['purchased_type'] == 'Yearly') {
                    $slug = date('Y', strtotime($service['date']));
                    redirect($link);
                } elseif ($service['purchased_type'] == 'Monthly') {
                    // First check period_value from purchase record
                    if (!empty($service['period_value'])) {
                        $period_info = getyearmonthvalues($service['period_value']);
                        if (!empty($period_info['value'])) {
                            $name = $period_info['value'];
                        }
                    }
                    // Fall back to formdata if period_value not available
                    if ($name == "<span class=\"text-danger\">Pending</span>") {
                        $documents = $this->service->getuploadeddocuments(['a.order_id' => $service['id']]);
                        $doc_names = !empty($documents) ? array_column($documents, 'display_name') : array();
                        if (!empty($doc_names)) {
                            $index = array_search('Month', $doc_names);
                            if ($index !== false) {
                                $name = $documents[$index]['formvalue'];
                            }
                        }
                    }
                } elseif ($service['purchased_type'] == 'Quarterly') {
                    // First check period_value from purchase record
                    if (!empty($service['period_value'])) {
                        $period_info = getyearmonthvalues($service['period_value']);
                        if (!empty($period_info['value'])) {
                            $name = $period_info['value'];
                        }
                    }
                    // Fall back to formdata if period_value not available
                    if ($name == "<span class=\"text-danger\">Pending</span>") {
                        $documents = $this->service->getuploadeddocuments(['a.order_id' => $service['id']]);
                        $doc_names = !empty($documents) ? array_column($documents, 'display_name') : array();
                        if (!empty($doc_names)) {
                            $index = array_search('Quarter', $doc_names);
                            if ($index !== false) {
                                $name = $documents[$index]['formvalue'];
                            }
                        }
                    }
                } else {
                    $slug = date('F-Y', strtotime($service['date']));
                }
                $services[$key]['name'] = $name;
                $services[$key]['count'] = '';
                $services[$key]['link'] = $link;
            }
        }
        $data['services'] = $services;
        //print_pre($data,true);
        $data['datatable'] = true;
        //$data['styles']=array('file'=>'includes/custom/folder.css');
        //$data['folders']=$folders;
        //$this->template->load('pages','folder-view',$data);
        $this->template->load('services', 'monthlyservices', $data);
    }

    public function openform($slug = NULL, $order_id = NULL)
    {
        $where = "status='1' and slug ='$slug'";
        $service = $this->master->getservices($where, 'single');
        if (empty($service)) {
            redirect('services/purchasedservices/');
        }
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;

        $kyc = $this->account->getkyc(['t1.user_id' => $user['id']], 'single');
        if (!empty($kyc)) {
            $where = "t1.user_id='$user[id]' and t1.service_id='$service[id]' and t1.status=0";
            $purchasedservice = $this->service->getpurchasedservices($where, 'single');
            if (!empty($purchasedservice)) {
                $documents = $this->master->getservicedocuments(['t1.service_id' => $service['id']]);
                //print_pre($documents,true);
                $finaldocuments = array();
                if (!empty($documents)) {
                    foreach ($documents as $key => $field) {
                        $value = '';
                        $editable = true;
                        if ($field['document_id'] == 1) {
                            $value = $user['mobile'];
                            $editable = false;
                        } elseif ($field['document_id'] == 2) {
                            $value = $user['email'];
                            $editable = false;
                        } elseif ($field['document_id'] == 3) {
                            $value = $kyc['pan'];
                            $documents[$key]['file'] = 0;
                            $editable = false;
                        } elseif ($field['document_id'] == 4) {
                            $value = $kyc['aadhar'];
                            $documents[$key]['file'] = 0;
                            $editable = false;
                        } elseif ($field['document_id'] == 3 || $field['document_id'] == 4) {
                            unset($documents[$key]);
                            continue;
                        }
                        $documents[$key]['field_value'] = $value;
                        $documents[$key]['editable'] = $editable;
                        $finaldocuments[] = $documents[$key];
                    }
                    $type = !empty($purchasedservice['purchased_type']) ? $purchasedservice['purchased_type'] : '';
                    $data = ['title' => 'Service Form'];
                    $data['breadcrumb'] = array('services/purchasedservices' => 'Purchased Services', 'services/monthlyservices/' . $service['slug'] => $service['name'], "active" => "Service Form");
                    $data['user'] = $user;
                    $data['finaldocuments'] = $finaldocuments;
                    $where = "t1.user_id='$user[id]' and t1.id='$order_id' and t1.firm_id='$firm_id'";
                    $data['order'] = $this->service->getpurchasedservices($where, 'single');

                    // Get period_value (quarter/month) for pre-selecting dropdown
                    $data['selected_period'] = '';
                    if (!empty($data['order']['period_value'])) {
                        $data['selected_period'] = $data['order']['period_value'];
                    }

                    $data['firm'] = $this->customer->getfirms(array("t1.id" => $firm_id), "single");
                    $data['kyc'] = $kyc;
                    $this->template->load('services', 'serviceform', $data);
                } else {
                    $message = "Required Documents Not Added! Please Try Again Later!";
                    $this->session->set_flashdata("err_msg", $message);
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $message = "Service Not Purchased!";
                $this->session->set_flashdata("err_msg", $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $message = "KYC Not Uploaded! Please Upload KYC before Submitting this Form!";
            $this->session->set_flashdata("err_msg", $message);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function previewform($slug = NULL, $order_id = NULL)
    {
        $where = "status='1' and slug ='$slug'";
        $service = $this->master->getservices($where, 'single');
        if (empty($service)) {
            redirect('services/purchasedservices/');
        }
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;

        $kyc = $this->account->getkyc(['t1.user_id' => $user['id']], 'single');
        if (!empty($kyc)) {
            $where = "t1.user_id='$user[id]' and t1.id='$order_id' and t1.firm_id='$firm_id'";
            $order = $this->service->getpurchasedservices($where, 'single');
            if (!empty($order)) {
                $data = ['title' => 'Service Form Preview'];
                $data['breadcrumb'] = array('services/purchasedservices' => 'Purchased Services', 'services/monthlyservices/' . $service['slug'] => $service['name'], "active" => "Service Form Preview");
                $data['user'] = $user;
                $data['firm'] = $this->customer->getfirms(array("t1.id" => $firm_id), "single");
                $data['kyc'] = $kyc;
                $order['name'] = $user['name'];
                $data['order'] = $order;
                if ($order['status'] == 0) {
                    $message = "Form not Submitted!";
                    $this->session->set_flashdata("err_msg", $message);
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $documents = $this->service->getuploadeddocuments(['a.order_id' => $order['id']]);
                    if (!empty($documents)) {
                        foreach ($documents as $key => $value) {
                            if (strpos($value['formvalue'], '/assets/') === 0) {
                                $documents[$key]['formvalue'] = file_url($value['formvalue']);
                            }
                        }
                        $data['documents'] = $documents;
                        // Get assessment - check for status=1 (completed) first, then status=0 (pending) as fallback
                        $getassessment = $this->db->get_where('assessments', ['order_id' => $order['id'], 'status' => 1]);
                        $assessment = array();
                        if ($getassessment->num_rows() > 0) {
                            $assessment = $getassessment->unbuffered_row('array');
                        } else {
                            // Fallback to status=0 for backward compatibility
                            $getassessment = $this->db->get_where('assessments', ['order_id' => $order['id'], 'status' => 0]);
                            if ($getassessment->num_rows() > 0) {
                                $assessment = $getassessment->unbuffered_row('array');
                            }
                        }
                        $data['assessment'] = $assessment;
                        //print_pre($data,true);
                        $this->template->load('services', 'formpreview', $data);
                    } else {
                        $message = "Form not Submitted!";
                        $this->session->set_flashdata("err_msg", $message);
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
            } else {
                $message = "Service Not Purchased!";
                $this->session->set_flashdata("err_msg", $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $message = "KYC Not Uploaded! Please Upload KYC before Submitting this Form!";
            $this->session->set_flashdata("err_msg", $message);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function saveformdata()
    {
        if ($this->input->post('saveformdata') !== NULL) {
            $data = $this->input->post();
            $formdata = $data['formdata'] ?? array();
            if (!empty($data['month'])) {
                $month = $data['month'];
            }
            //print_pre($data,true);
            $where = "status='1' and slug ='$data[slug]'";
            $service = $this->master->getservices($where, 'single');
            //print_pre($service,true);
            if (empty($service)) {
                $this->session->set_flashdata("err_msg", "Please Try Again!");
                redirect($_SERVER['HTTP_REFERER']);
            }
            $user = getuser();
            $year = $this->session->year;
            $firm_id = $this->session->firm;

            $kyc = $this->account->getkyc(['t1.user_id' => $user['id']], 'single');
            if (!empty($kyc)) {
                $where = "t1.user_id='$user[id]' and t1.id='$data[order_id]' and t1.firm_id='$firm_id'";
                $order = $this->service->getpurchasedservices($where, 'single');
                if (!empty($order)) {
                    $documents = $this->master->getservicedocuments(['t1.service_id' => $order['service_id']]);
                    if (!empty($documents)) {
                        $message = array();
                        $data = array();
                        $date = date('Y-m-d');
                        foreach ($documents as $document) {
                            $slug = $document['slug'];
                            $single = array();
                            if (
                                $document['value'] == 1 && isset($formdata[$slug]) && $document['document_id'] != 3 &&
                                $document['document_id'] != 4
                            ) {
                                $single[] = array(
                                    'date' => $date,
                                    'user_id' => $user['id'],
                                    'order_id' => $order['id'],
                                    'service_id' => $order['service_id'],
                                    'field' => $slug,
                                    'field_id' => $document['id'],
                                    'value' => $formdata[$slug]
                                );
                            }
                            if ($document['document_id'] == 3) {
                                $single[] = array(
                                    'date' => $date,
                                    'user_id' => $user['id'],
                                    'order_id' => $order['id'],
                                    'service_id' => $order['service_id'],
                                    'field' => $slug,
                                    'field_id' => $document['id'],
                                    'value' => $kyc['pan']
                                );
                            } elseif ($document['document_id'] == 4) {
                                $single[] = array(
                                    'date' => $date,
                                    'user_id' => $user['id'],
                                    'order_id' => $order['id'],
                                    'service_id' => $order['service_id'],
                                    'field' => $slug,
                                    'field_id' => $document['id'],
                                    'value' => $kyc['aadhar']
                                );
                            }
                            $newslug = '';
                            if ($document['file'] > 0) {
                                $upload_path = './assets/service/documents/';
                                $allowed_types = $document['file_type'];
                                if ($document['file'] == 1) {
                                    $newslug = $slug . '-file';
                                    if (isset($_FILES[$newslug]['tmp_name'])) {
                                        $upload = upload_file($newslug, $upload_path, $allowed_types, $newslug);
                                        if ($upload['status'] === true) {
                                            $single[] = array(
                                                'date' => $date,
                                                'user_id' => $user['id'],
                                                'order_id' => $order['id'],

                                                'service_id' => $order['service_id'],
                                                'field' => $newslug,
                                                'field_id' => $document['id'],
                                                'value' => $upload['path']
                                            );
                                        } else {
                                            $message[] = $document['display_name'] . ' File.' . $upload['msg'];
                                        }
                                    } elseif ($document['document_id'] == 3) {
                                        $single[] = array(
                                            'date' => $date,
                                            'user_id' => $user['id'],
                                            'order_id' => $order['id'],
                                            'service_id' => $order['service_id'],
                                            'field' => $newslug,
                                            'field_id' => $document['id'],
                                            'value' => str_replace(file_url(), '', $kyc['pan_image'])
                                        );
                                    } elseif ($document['document_id'] == 4) {
                                        $single[] = array(
                                            'date' => $date,
                                            'user_id' => $user['id'],
                                            'order_id' => $order['id'],
                                            'service_id' => $order['service_id'],
                                            'field' => $newslug,
                                            'field_id' => $document['id'],
                                            'value' => str_replace(file_url(), '', $kyc['aadhar_image'])
                                        );
                                    } else {
                                        $message[] = $document['display_name'] . ' File';
                                    }
                                } elseif ($document['file'] == 2) {
                                    for ($i = 1; $i <= 2; $i++) {
                                        $newslug = $slug . '-file-' . $i;
                                        if (isset($_FILES[$newslug]['tmp_name'])) {
                                            $upload = upload_file($newslug, $upload_path, $allowed_types, $newslug);
                                            if ($upload['status'] === true) {
                                                $single[] = array(
                                                    'date' => $date,
                                                    'user_id' => $user['id'],
                                                    'order_id' => $order['id'],
                                                    'service_id' => $order['service_id'],
                                                    'field' => $newslug,
                                                    'field_id' => $document['id'],
                                                    'value' => $upload['path']
                                                );
                                            }
                                        } elseif ($document['document_id'] == 4) {
                                            $image = $i == 1 ? str_replace(file_url(), '', $kyc['aadhar_image']) : str_replace(file_url(), '', $kyc['aadhar_back']);
                                            $single[] = array(
                                                'date' => $date,
                                                'user_id' => $user['id'],
                                                'order_id' => $order['id'],
                                                'service_id' => $order['service_id'],
                                                'field' => $slug,
                                                'field_id' => $document['id'],
                                                'value' => $image
                                            );
                                        } else {
                                            $message[] = $document['display_name'] . ' Image ' . $i;
                                        }
                                    }
                                }
                            }
                            if (empty($single) && $document['value'] == 1) {
                                $message[] = $document['display_name'];
                            } else {
                                $data = array_merge($data, $single);
                            }
                        }
                        $documents[] = $formdata;
                        //print_pre($data,true);
                        if (!empty($data) && empty($message)) {
                            if (!empty($year)) {
                                $data[] = array(
                                    'date' => $date,
                                    'user_id' => $user['id'],
                                    'order_id' => $order['id'],
                                    'service_id' => $order['service_id'],
                                    'field' => $order['service_slug'] . '-year',
                                    'field_id' => 0,
                                    'value' => $year
                                );
                            }
                            if (!empty($month)) {
                                $data[] = array(
                                    'date' => $date,
                                    'user_id' => $user['id'],
                                    'order_id' => $order['id'],
                                    'service_id' => $order['service_id'],
                                    'field' => $order['service_slug'] . '-month',
                                    'field_id' => 0,
                                    'value' => $month
                                );
                            }
                            foreach ($data as $key => $value) {
                                $data[$key]['firm_id'] = $firm_id;
                            }
                            //print_pre($data,true);
                            $result = $this->service->saveformdata($data);
                            if ($result['status'] === true) {
                                $notifydata = array(
                                    "type" => "Documents Uploaded",
                                    "user_id" => $user['id'],
                                    'order_id' => $order['id'],
                                    'message' => $user['name'] . ' has Successfully Uploaded the documents for ' . $order['service_name'] . '.',
                                    'added_on' => date('Y-m-d H:i:s'),
                                    'updated_on' => date('Y-m-d H:i:s')
                                );
                                $this->common->savenotification($notifydata);
                                $this->session->set_flashdata("msg", "Formdata Saved Successfully!");
                                redirect('services/monthlyservices/' . $slug);
                            } else {
                                $this->session->set_flashdata("err_msg", $result['message']);
                            }
                        } else {
                            $message = implode(',', $message);
                            $message = "You have not provided " . $message;
                            /*$this->response([
                                'status' => false,
                                'message' => $message,
                                'documents' => $documents,'data'=>$data,'newslug'=>$newslug], RestController::HTTP_OK);*/
                            $this->session->set_flashdata("err_msg", $message);
                        }
                    } else {
                        $message = "Required Documents Not Added! Please Try Again Later!";
                        $this->session->set_flashdata("err_msg", $message);
                    }
                } else {
                    $message = "Service Not Purchased!";
                    $this->session->set_flashdata("err_msg", $message);
                }
            } else {
                $message = "KYC Not Uploaded! Please Upload KYC before Submitting this Form!";
                $this->session->set_flashdata("err_msg", $message);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function buyservice()
    {
        $url = '';
        $user = getuser();
        $firm_id = $this->session->firm;
        $year = $this->session->year;
        $service_id = $this->input->post('id');
        $package_id = $this->input->post('package_id');
        $type = $this->input->post('type');
        $amount = $this->input->post('amount');
        $service_option = $this->input->post('service_option'); // Generic parameter for all services with options
        $period_value = $this->input->post('period_value'); // Period value for Monthly/Quarterly/Yearly
        $where = array('t1.id' => $firm_id, "t1.user_id" => $user['id']);
        $firm = $this->customer->getfirms($where, 'single');
        if (!empty($firm)) {
            $firm_id = $firm['id'];
            $where = "status='1' and id ='$service_id'";
            $service = $this->master->getservices($where, 'single');
            if (!empty($service)) {
                $service_for = $service['service_for'];
                $types = explode(',', $service['type']);
                $status = true;
                $message = "";

                // Check if this service has dynamic options (generic for all services)
                $has_service_options = false;
                $service_options_pricing = array();
                $service_options_display_names = array();
                $selected_option_display = '';

                // Check if this service has dynamic options
                $service_options = $this->master->getserviceoptionspricing($service_id);
                if (!empty($service_options['pricing']) && !empty($service_option)) {
                    $service_options_pricing = $service_options['pricing'];
                    $service_options_display_names = $service_options['display_names'];

                    // Validate selected option exists
                    if (in_array($service_option, array_keys($service_options_pricing))) {
                        $has_service_options = true;
                        // Get pricing for selected option
                        $amount = $service_options_pricing[$service_option];
                        // Get display name for selected option
                        $selected_option_display = isset($service_options_display_names[$service_option]) ?
                            $service_options_display_names[$service_option] :
                            ucfirst(str_replace('-', ' ', $service_option));
                        // Update service name to include the option
                        $service['name'] = trim($service['name']) . ' - ' . $selected_option_display;
                        // For services with options, default to Yearly type if not specified
                        if (empty($type) || !in_array($type, $types)) {
                            $type = in_array('Yearly', $types) ? 'Yearly' : (count($types) > 0 ? $types[0] : 'Yearly');
                        }
                    } else {
                        $status = false;
                        $message = "Invalid option selected for " . $service['name'];
                    }
                }

                // Check if service is already included in the user's service package (skip for service_id=1 / accountancy)
                if ($service_id != 1) {
                    $service_package = $this->customer->getservicepackage(
                        ['t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year],
                        'single'
                    );
                    if (!empty($service_package) && !empty($service_package['service_ids'])) {
                        $package_service_ids = array_map('trim', explode(',', $service_package['service_ids']));
                        if (in_array((string)$service_id, $package_service_ids)) {
                            $this->session->set_flashdata("err_msg", $service['name'] . " is already included in your service package. You cannot purchase it separately.");
                            redirect($_SERVER['HTTP_REFERER']);
                            return;
                        }
                    }
                }

                if ($service_id == 1) {
                    //                    $status=false;
                    //                    $message="Select Package to Activate ".$service['name'];
                    //                    if($type=='Monthly'){
                    //                        $message="Select Package and enter Monthly Debit Amount to Activate ".$service['name'];
                    //                    }
                    $package_id = $package_id == 'accountancy-prime' ? 1 : 2;
                    $name = $package_id == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
                    $autodebit = 1;
                    $package = $this->master->getpackages(['name' => $name]);
                    $where = array('user_id' => $user['id'], 'status' => 1, 'firm_id' => $firm_id, 'year' => $year);
                    $query = $this->db->get_where('customer_packages', $where);
                    $status = true;
                    $message = $name . " Selected Successfully!";
                    if ($query->num_rows() > 0) {
                        $cpackage = $query->unbuffered_row('array');
                        $p = $cpackage['package_id'] == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
                        $status = false;
                        $message = "You have already selected " . $p . "!";
                    }
                    if ($type == "Monthly" && empty($amount)) {
                        $status = false;
                        $message = "Enter Monthly Debit Amount to Select Package!";
                    }
                    if ($status) {
                        $datetime = date('Y-m-d H:i:s');
                        $data = array(
                            'user_id' => $user['id'],
                            'package_id' => $package_id,
                            'firm_id' => $firm_id,
                            'year' => $year,
                            'status' => 1,
                            'added_on' => $datetime,
                            'updated_on' => $datetime
                        );

                        if ($type == "Monthly") {
                            $data['amount'] = $amount;
                        }
                        if (!empty($autodebit) && $autodebit != 0) {
                            $data['autodebit'] = 1;
                        }
                        $result = $this->db->insert("customer_packages", $data);
                        if ($result) {
                            $this->session->set_flashdata("msg", "Accountancy Package Selected Successfully!");
                        } else {
                            $error = $this->db->error();
                            $this->session->set_flashdata("err_msg", $error['message']);
                        }
                    } else {
                        $this->session->set_flashdata("err_msg", $message);
                    }
                    return false;
                } elseif (!$has_service_options && !in_array($type, $types)) {
                    $status = false;
                    $message = $type . " option not available for " . $service['name'];
                } elseif ($has_service_options && !empty($service_option)) {
                    // Handle services with dynamic options - check for duplicate purchase of same option
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                    // Always check firm_id if provided (purchases always have firm_id)
                    if (!empty($firm_id)) {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    // If period_value is provided, also check for it
                    if (!empty($period_value)) {
                        $where2 .= " and t1.period_value='$period_value'";
                    }
                    $purchases = $this->service->getpurchases($where2);
                    if (!empty($purchases)) {
                        // Check if this specific option was already purchased
                        foreach ($purchases as $purchase) {
                            // First check service_option column (most reliable)
                            if (!empty($purchase['service_option']) && $purchase['service_option'] == $service_option) {
                                $status = false;
                                $years = getyearmonthvalues($year);
                                $period_msg = '';
                                if (!empty($period_value)) {
                                    $period_info = getyearmonthvalues($period_value);
                                    $period_msg = ' for ' . $period_info['value'];
                                } else {
                                    $period_msg = ' for ' . $years['value'];
                                }
                                $message = "You have already Purchased " . $service['name'] . " (" . $selected_option_display . ")" . $period_msg . "!";
                                break;
                            }
                            // Fallback: check service name for the option
                            $search_keyword = strtolower($selected_option_display);
                            $purchase_service_name = strtolower(!empty($purchase['service']) ? $purchase['service'] : (!empty($purchase['service_name']) ? $purchase['service_name'] : ''));
                            if (strpos($purchase_service_name, $search_keyword) !== false) {
                                $status = false;
                                $years = getyearmonthvalues($year);
                                $period_msg = '';
                                if (!empty($period_value)) {
                                    $period_info = getyearmonthvalues($period_value);
                                    $period_msg = ' for ' . $period_info['value'];
                                } else {
                                    $period_msg = ' for ' . $years['value'];
                                }
                                $message = "You have already Purchased " . $service['name'] . " (" . $selected_option_display . ")" . $period_msg . "!";
                                break;
                            }
                        }
                    }
                } elseif ($service['type'] == 'Once') {
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id'";
                    // Always check firm_id if provided (purchases always have firm_id)
                    if (!empty($firm_id)) {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);
                    if (!empty($purchases)) {
                        $status = false;
                        $message = "You have already Purchased " . $service['name'] . "!";
                    }
                } elseif ($types[0] == 'Yearly' && count($types) == 1) {
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                    // Always check firm_id if provided (purchases always have firm_id)
                    if (!empty($firm_id)) {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    // If period_value is provided, also check for it
                    if (!empty($period_value)) {
                        $where2 .= " and t1.period_value='$period_value'";
                    }
                    // For services with options, duplicate check already handled above
                    if (!$has_service_options) {
                        $purchases = $this->service->getpurchases($where2);
                        if (!empty($purchases)) {
                            $status = false;
                            if (!empty($period_value)) {
                                $period_info = getyearmonthvalues($period_value);
                                $message = "You have already Purchased " . $service['name'] . " for " . $period_info['value'] . "!";
                            } else {
                                $years = getyearmonthvalues($year);
                                $message = "You have already Purchased " . $service['name'] . " for " . $years['value'] . "!";
                            }
                        }
                    }
                } elseif ($types[0] == 'Yearly' && count($types) > 1) {
                    /*$where2="t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                    if($service_for=='Firm'){
                        $where2.=" and t1.firm_id='$firm_id'";
                    }
                    $purchases=$this->service->getpurchases($where2);
                    if(!empty($purchases)){
                        $status=false;
                        $message="You have already Purchased this Service!";
                    }*/
                } elseif ($type == 'Monthly') {
                    // Check annual limit: Maximum 12 monthly purchases per year
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year' and t1.type='Monthly'";
                    // Always check firm_id if provided (purchases always have firm_id)
                    if (!empty($firm_id)) {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);

                    // Check if annual limit (12 months) is reached
                    if (!empty($purchases) && count($purchases) >= 12) {
                        $status = false;
                        $years = getyearmonthvalues($year);
                        $message = "You have reached the annual limit! You can purchase monthly services maximum 12 times per year for " . $years['value'] . "!";
                    } elseif (!empty($period_value)) {
                        // Check for duplicate purchase of specific month
                        $where3 = $where2 . " and t1.period_value='$period_value'";
                        $existing = $this->service->getpurchases($where3);
                        if (!empty($existing)) {
                            $status = false;
                            $period_info = getyearmonthvalues($period_value);
                            $message = "You have already Purchased " . $service['name'] . " for " . $period_info['value'] . "!";
                        }
                    }
                } elseif ($type == 'Quarterly') {
                    // Check annual limit: Maximum 4 quarterly purchases per year
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year' and t1.type='Quarterly'";
                    // Always check firm_id if provided (purchases always have firm_id)
                    if (!empty($firm_id)) {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);

                    // Check if annual limit (4 quarters) is reached
                    if (!empty($purchases) && count($purchases) >= 4) {
                        $status = false;
                        $years = getyearmonthvalues($year);
                        $message = "You have reached the annual limit! You can purchase quarterly services maximum 4 times per year for " . $years['value'] . "!";
                    } elseif (!empty($period_value)) {
                        // Check for duplicate purchase of specific quarter
                        $where3 = $where2 . " and t1.period_value='$period_value'";
                        $existing = $this->service->getpurchases($where3);
                        if (!empty($existing)) {
                            $status = false;
                            $period_info = getyearmonthvalues($period_value);
                            $message = "You have already Purchased " . $service['name'] . " for " . $period_info['value'] . "!";
                        }
                    }
                } elseif ($type == 'Yearly') {
                    // Check annual limit: Maximum 1 yearly purchase per year
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year' and t1.type='Yearly'";
                    // Always check firm_id if provided (purchases always have firm_id)
                    if (!empty($firm_id)) {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);

                    // Check if annual limit (1 yearly) is reached
                    if (!empty($purchases) && count($purchases) >= 1) {
                        $status = false;
                        $years = getyearmonthvalues($year);
                        $message = "You have reached the annual limit! You can purchase yearly services maximum 1 time per year for " . $years['value'] . "!";
                    } elseif (!empty($period_value)) {
                        // Check for duplicate purchase of specific year period
                        $where3 = $where2 . " and t1.period_value='$period_value'";
                        $existing = $this->service->getpurchases($where3);
                        if (!empty($existing)) {
                            $status = false;
                            $period_info = getyearmonthvalues($period_value);
                            $message = "You have already Purchased " . $service['name'] . " for " . $period_info['value'] . "!";
                        }
                    }
                }
                if ($status) {
                    // Use custom amount for services with options, otherwise use service rate
                    if ($has_service_options && !empty($amount)) {
                        $subtotal = floatval($amount);
                        $service['rate'] = $subtotal; // Update rate for display
                    } else {
                        $service['rate'] = $service['rate'];
                        $subtotal = $service['rate'];
                    }

                    if (!$has_service_options && !empty($types) && count($types) > 1) {
                        if ($type == 'Monthly') {
                            $subtotal = $service['rate'];
                        } elseif ($type == 'Quarterly') {
                            if (in_array("Monthly", $types)) {
                                $subtotal = $service['rate'] * 3;
                            } else {
                                $subtotal = $service['rate'];
                            }
                        } elseif ($type == 'Yearly') {
                            if (in_array("Monthly", $types) && !in_array("Quarterly", $types)) {
                                $subtotal = $service['rate'] * 12;
                            } elseif (!in_array("Monthly", $types) && in_array("Quarterly", $types)) {
                                $subtotal = $service['rate'] * 4;
                            } else {
                                $subtotal = $service['rate'];
                            }
                        }
                    }

                    // Check if GST is enabled for this customer
                    $customer = $this->customer->getcustomers(['t1.user_id' => $user['id']], 'single');
                    $gst_enabled = !empty($customer) && !empty($customer['gst_enabled']) && $customer['gst_enabled'] == 1;
                    $gst_amount = 0;
                    $total = $subtotal;

                    if ($gst_enabled) {
                        // Calculate 18% GST
                        $gst_amount = round(($subtotal * 18) / 100, 2);
                        $total = $subtotal + $gst_amount;
                    }

                    $where = array('user_id' => $user['id'], 'status' => 1);
                    $single = array(
                        'date' => date('Y-m-d'),
                        'year' => $year,
                        'type' => $type,
                        'user_id' => $user['id'],
                        'service_id' => $service['id'],
                        'firm_id' => $firm['id'],
                        'service' => $service['name'],
                        'rate' => $service['rate'],
                        'subtotal' => $subtotal,
                        'gst_amount' => $gst_amount,
                        'gst_enabled' => $gst_enabled ? 1 : 0,
                        'amount' => $total
                    );

                    // Store period value for Monthly/Quarterly/Yearly purchases
                    if (!empty($period_value) && ($type == 'Monthly' || $type == 'Quarterly' || $type == 'Yearly')) {
                        $single['period_value'] = $period_value;
                    }

                    // Store service option if applicable (generic for all services with options)
                    if ($has_service_options && !empty($service_option)) {
                        $single['service_option'] = $service_option;
                        $single['service_option_display'] = $selected_option_display;
                    }
                    $balance = $this->wallet->getwalletbalance($user['id']);

                    if ($balance >= $total) {
                        $data = array($single);
                        $data['name'] = $user['name'];
                        //print_pre($data,true);
                        $result = $this->service->purchaseservices($data);
                        //print_pre($result);
                        if ($result['status'] == true) {
                            // Create invoice for this purchase (parent order)
                            if (!empty($result['order_id'])) {
                                $order = $this->service->getpurchases(['t1.id' => $result['order_id']], 'single');
                                if (!empty($order)) {
                                    $invoice_result = $this->invoice->create_for_order($order);
                                    if ($invoice_result['status'] === true) {
                                        $this->session->set_flashdata("msg", $result['message'] . ' Invoice No: ' . $invoice_result['invoice']['invoice_no']);
                                    } else {
                                        // If invoice fails, still keep purchase but log error
                                        log_message('error', 'Invoice creation failed for order ' . $result['order_id'] . ': ' . $invoice_result['message']);
                                        $this->session->set_flashdata("msg", $result['message']);
                                    }
                                } else {
                                    $this->session->set_flashdata("msg", $result['message']);
                                }
                            } else {
                                $this->session->set_flashdata("msg", $result['message']);
                            }
                        } else {
                            $this->session->set_flashdata("err_msg", $result['message']);
                        }
                    } else {
                        $remaining = $total - $balance;
                        $this->session->set_userdata('tobuy', true);
                        $this->session->set_flashdata("remaining", $remaining);
                        $url = base_url('mywallet/');
                    }
                } else {
                    $this->session->set_flashdata("err_msg", $message);
                }
            } else {
                $this->session->set_flashdata("err_msg", "No Service Selected!");
            }
        } else {
            $this->session->set_flashdata("err_msg", "Firm not Selected!");
        }
        echo $url;
    }
}
