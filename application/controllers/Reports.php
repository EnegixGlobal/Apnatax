<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        logrequest();
        checklogin();
        //checkcookie();
        // Allow admin, employee access for admin reports, customer access for customer reports
        if ($this->session->role != 'customer' && $this->session->role != 'admin' && $this->session->role != 'superadmin' && $this->session->role != 'employee') {
            redirect('/');
        }
    }

    public function index()
    {
        $data = ['title' => 'Accounting Fee'];
        $data['breadcrumb'] = array("active" => "Accounting Fee");
        $data['alertify'] = true;
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $user_id = $user['id'];

        // Validate required session data
        if (empty($year) || empty($firm_id)) {
            $this->session->set_flashdata('err_msg', 'Please select Year and Firm!');
            redirect($_SERVER['HTTP_REFERER'] ?? 'home/');
            return;
        }

        $yearval = getyearmonthvalues($year);
        $year1 = $yearval['year1'];
        $year2 = $yearval['year2'];
        $from = "$year1-04-01";
        $to = "$year2-03-31";
        $where = array('user_id' => $user_id, 'status' => 1);
        $query = $this->db->get_where('customer_packages', $where);
        if ($query->num_rows() > 0) {
            // Use proper escaping for SQL query
            $user_id_escaped = $this->db->escape($user_id);
            $firm_id_escaped = $this->db->escape($firm_id);
            $from_escaped = $this->db->escape($from);
            $to_escaped = $this->db->escape($to);
            $where2 = "t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.date>={$from_escaped} AND t1.date<={$to_escaped}";
            $accountancy = $this->service->getturnoverswithpayment($where2);

            // Log for debugging (only in development)
            if (ENVIRONMENT !== 'production') {
                log_message('debug', 'Reports index - User ID: ' . $user_id . ', Firm ID: ' . $firm_id . ', Year: ' . $year);
                log_message('debug', 'Reports index - Date range: ' . $from . ' to ' . $to);
                log_message('debug', 'Reports index - Accountancy records found: ' . count($accountancy));
            }

            $turnovers = !empty($accountancy) ? array_column($accountancy, 'turnover') : array(0);
            $turnover = array_sum($turnovers);
            $total_turnover = $turnover;
            $cpackage = $query->unbuffered_row('array');
            $name = $cpackage['package_id'] == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
            $package = $this->master->getpackages(['name' => $name, 'turnover>' => $turnover], 'single');
            $date = date('Y-m-d');
            $percent = 2 / 100;
            $report = array();
            if (!empty($accountancy)) {
                $total_fees = $total_other = $total_paid = $total_penalty = $total_days = 0;
                $outstanding = $total = 0;
                $fees = $total_turnover / $package['turnover'];
                $fees *= $package['rate'];
                $count = count($accountancy);
                $last = end($accountancy);
                if ($last['date'] == '') {
                    $count--;
                }
                $acc_fees = $fees / $count;
                foreach ($accountancy as $single) {
                    $days = $paid = $penalty = 0;
                    $paid = !empty($single['paid']) ? $single['paid'] : 0;
                    $outstanding = $total;
                    if ($single['date'] != '') {
                        $acc_fees = $fees / $count;
                    } else {
                        $acc_fees = 0;
                    }
                    $other_fee = $single['other_fee'] ?? 0;
                    $total_other += $other_fee;
                    $balance = $outstanding + $acc_fees + $other_fee;
                    if ($single['due_date'] < $date && $paid < $balance) {
                        $balance -= $paid;
                        $date1 = new DateTime($single['due_date']);
                        $date2 = new DateTime($date);

                        // Calculate the difference
                        $interval = $date1->diff($date2);

                        // Get the difference in days
                        $days = $interval->days;
                        $penalty = ($percent * $balance);
                        if ($days < 30) {
                            $penalty /= 30;
                            $penalty *= $days;
                        }
                        $penalty = round($penalty);
                        $total_penalty += $penalty;
                        $total_days += $days;
                    } else {
                        $balance -= $paid;
                    }
                    $total = $balance + $penalty;
                    $total_fees += $acc_fees;
                    $total_paid += $paid;
                    $month = $single['date'] != '' ? date('F-y', strtotime($single['date'])) : '--';
                    $due_date = $single['due_date'] != '' ? date('d-m-Y', strtotime($single['due_date'])) : '--';
                    $row = array(
                        'month' => $month,
                        'outstanding' => round($outstanding, 4),
                        'gto' => round($single['turnover'], 4),
                        'acc_fees' => round($acc_fees, 4),
                        'penalty' => round($penalty, 4),
                        'total' => round($total, 4),
                        'paid' => round($paid, 4),
                        'balance' => round($balance, 4),
                        'due_date' => $due_date,
                        'due_days' => $days
                    );

                    $report[] = $row;
                }
                $row = array(
                    'month' => 'Total',
                    'outstanding' => 0,
                    'gto' => round($total_turnover, 4),
                    'acc_fees' => round($total_fees, 4),
                    'penalty' => round($total_penalty, 4),
                    'total' => round(($total_fees + $total_penalty - $total_paid), 4),
                    'paid' => round($total_paid, 4),
                    'balance' => 0,
                    'due_date' => '',
                    'due_days' => $total_days
                );

                $report[] = $row;
                $data['report'] = $report;
                $data['package'] = $package;
                $this->template->load('reports', 'accountancy', $data);
            } else {
                $this->session->set_flashdata('err_msg', 'No Data Found!');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('err_msg', 'Package not Active!');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function otherfee()
    {
        $data = ['title' => 'Other Fee Report'];
        $data['breadcrumb'] = array("active" => "Other Fee Report");
        $data['alertify'] = true;
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $user_id = $user['id'];

        // Validate required session data
        if (empty($year) || empty($firm_id)) {
            $this->session->set_flashdata('err_msg', 'Please select Year and Firm!');
            redirect($_SERVER['HTTP_REFERER'] ?? 'home/');
            return;
        }

        $years = getyearmonthvalues($year);
        $where = array('t1.user_id' => $user['id'], 't1.firm_id' => $firm_id, 't1.date>=' => $years['year1'] . '-04-01', 't1.date<=' => $years['year2'] . '-03-31');
        $purchases = $this->service->getpurchases($where);
        $services = $this->master->getservices();
        $report = array();
        $row = array('service' => 'Service');
        if (empty($years)) {
            if (date('d') <= 31 && date('m') < 4) {
                $start = (date('Y') - 1) . '-04-01';
            } else {
                $start = date('Y') . '-04-01';
            }
        } else {
            $start = date($years['year1'] . '-04-01');
        }
        for ($i = 0; $i < 12; $i++) {
            $m = date('F', strtotime($start . " +$i month"));
            $row[$m] = date('F-Y', strtotime($start . " +$i month"));
        }
        $row['total'] = "Total";

        $report[] = $row;

        if (!empty($purchases)) {
            $service_ids = array_column($purchases, 'service_id');
            $dates = array_column($purchases, 'date');
            //print_pre($service_ids);
            //print_pre($dates);
        }
        $total_amount = 0;
        if (!empty($services)) {
            foreach ($services as $single) {
                $row = array();
                $row['service'] = $single['name'];
                for ($i = 0; $i < 12; $i++) {
                    $m = date('F', strtotime($start . " +$i month"));
                    $row[$m] = 0;
                }
                //echo $single['name'];
                $filteredDates = array();
                $indices = !empty($purchases) ? array_keys($service_ids, $single['id']) : array();
                if (!empty($indices)) {
                    foreach ($indices as $index) {
                        $filteredDates[$index] = $dates[$index];
                    }
                }
                //print_pre($filteredDates);
                $total = 0;

                for ($i = 0; $i < 12; $i++) {
                    $m = date('F', strtotime($start . " +$i month"));
                    if (empty($month[$i])) {
                        $month[$i] = 0;
                    }
                    $text = 0;
                    if (!empty($filteredDates)) {
                        //echo date('F-Y',strtotime($start." +$i month"));
                        $first = date('Y-m-01', strtotime($start . " +$i month"));
                        $last = date('Y-m-t', strtotime($start . " +$i month"));
                        $searches = findDateIndices($filteredDates, $first, $last);
                        if (!empty($searches)) {
                            $text = 0;
                            foreach ($searches as $index) {
                                $text += $purchases[$index]['amount'];
                            }
                            $total += $text;
                            $month[$i] += $text;
                            $text = $this->amount->toDecimal($text, false);
                        }
                    }
                    $row[$m] = $text;
                }
                $row['total'] = $this->amount->toDecimal($total, false);
                $report[] = $row;

                $total_amount += $total;
            }
        }
        $row = array('service' => 'Total');
        for ($i = 0; $i < 12; $i++) {
            $m = date('F', strtotime($start . " +$i month"));
            $row[$m] = !empty($month[$i]) ? $this->amount->toDecimal($month[$i], false) : 0;
        }
        $row['total'] = $this->amount->toDecimal($total_amount, false);
        $report[] = $row;


        if (!empty($report)) {
            $data['report'] = $report;
            $this->template->load('reports', 'otherfee', $data);
        } else {
            $this->session->set_flashdata('err_msg', 'No Reports Available!');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function feereport()
    {
        $data = ['title' => 'Fee Report'];
        $data['breadcrumb'] = array("active" => "Fee Report");
        $year = $this->session->year;
        $firm_id = $this->session->firm;
        $user = getuser();
        $folders = array();
        $folder = array();
        $folder['name'] = "Accounting Fee";
        $folder['count'] = '';
        $folder['link'] = ('reports/');
        $folders[] = $folder;
        $folder = array();
        $folder['name'] = "Other Fee Report";
        $folder['count'] = '';
        $folder['link'] = ('reports/otherfee/');
        $folders[] = $folder;
        $data['folders'] = $folders;
        //print_pre($data,true);
        $data['datatable'] = true;
        $data['styles'] = array('file' => 'includes/custom/folder.css');
        //$data['folders']=$folders;
        $this->template->load('pages', 'folder-view', $data);
    }

    // Admin Income Reports
    public function adminincome()
    {
        if ($this->session->role != 'admin' && $this->session->role != 'superadmin') {
            redirect('home/');
        }

        $data = ['title' => 'Income Reports'];
        $data['breadcrumb'] = array("active" => "Income Reports");
        $data['datatable'] = true;
        $data['alertify'] = true;

        // Get filter parameters
        $period = $this->input->get('period') ? $this->input->get('period') : '';
        $service_id = $this->input->get('service_id') ? $this->input->get('service_id') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $selected_month = $this->input->get('month') ? $this->input->get('month') : NULL;
        $selected_quarter = $this->input->get('quarter') ? $this->input->get('quarter') : NULL;
        $selected_year = $this->input->get('year') ? $this->input->get('year') : NULL;

        // If custom date range is selected but period is not set to custom, set it
        if (!empty($start_date) && !empty($end_date) && $period != 'custom') {
            $period = 'custom';
        }
        // Default to monthly if no period selected
        if (empty($period)) {
            $period = 'monthly';
        }

        // Get all services for dropdown
        $services = $this->master->getservices(['status' => 1]);
        $service_options = array('' => 'All Services');
        if (!empty($services)) {
            foreach ($services as $service) {
                $service_options[$service['id']] = $service['name'];
            }
        }

        // Generate month dropdown options (last 12 months)
        $month_options = array('' => 'Select Month');
        $current_year = date('Y');
        $current_month = date('m');
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $month_value = $date; // Format: YYYY-MM
            $month_label = date('F Y', strtotime($date . '-01'));
            $month_options[$month_value] = $month_label;
        }

        // Generate quarter dropdown options (last 8 quarters)
        $quarter_options = array('' => 'Select Quarter');
        $current_year = date('Y');
        $current_month = date('n');
        $current_quarter = ceil($current_month / 3);

        // Generate quarters: start from current quarter and go back 8 quarters
        $added_quarters = array();
        for ($i = 0; $i < 24; $i++) { // Go back 24 months to cover 8 quarters
            $months_back = $i;
            $date = date('Y-m', strtotime("-$months_back months"));
            $year = date('Y', strtotime($date . '-01'));
            $month = date('n', strtotime($date . '-01'));
            $quarter = ceil($month / 3);
            $quarter_value = $year . '-Q' . $quarter; // Format: YYYY-Q1, YYYY-Q2, etc.

            // Only add if not already added and we haven't reached 8 quarters yet
            if (!isset($added_quarters[$quarter_value]) && count($added_quarters) < 8) {
                $quarter_label = 'Q' . $quarter . ' ' . $year;
                $quarter_options[$quarter_value] = $quarter_label;
                $added_quarters[$quarter_value] = true;
            }
        }

        // Generate year dropdown options (last 5 years)
        $year_options = array('' => 'Select Year');
        $current_year = date('Y');
        for ($i = 0; $i < 5; $i++) {
            $year = $current_year - $i;
            $year_options[$year] = $year;
        }

        $data['services'] = $service_options;
        $data['month_options'] = $month_options;
        $data['quarter_options'] = $quarter_options;
        $data['year_options'] = $year_options;
        $data['selected_service'] = $service_id;
        $data['selected_period'] = $period;
        $data['selected_month'] = $selected_month;
        $data['selected_quarter'] = $selected_quarter;
        $data['selected_year'] = $selected_year;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Get income data - use empty array instead of "1" string
        $where = array();
        $income_data = $this->service->getincomebyperiod($where, $period, $service_id, $start_date, $end_date, $selected_month, $selected_quarter, $selected_year);
        $data['income_data'] = $income_data;

        // Calculate totals
        $total_amount = 0;
        $total_orders = 0;
        $total_customers = 0;
        if (!empty($income_data)) {
            foreach ($income_data as $row) {
                $total_amount += $row['total_amount'];
                $total_orders += $row['total_orders'];
                $total_customers += $row['total_customers'];
            }
        }
        $data['total_amount'] = $total_amount;
        $data['total_orders'] = $total_orders;
        $data['total_customers'] = $total_customers;

        $this->template->load('reports', 'admin_income', $data);
    }

    public function servicecustomers()
    {
        if ($this->session->role != 'admin' && $this->session->role != 'superadmin') {
            redirect('home/');
        }

        $data = ['title' => 'Service Customers'];
        $data['breadcrumb'] = array("active" => "Service Customers");
        $data['datatable'] = true;
        $data['alertify'] = true;

        // Get filter parameters
        $service_id = $this->input->get('service_id') ? $this->input->get('service_id') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        // Get all services for dropdown
        $services = $this->master->getservices(['status' => 1]);
        $service_options = array('' => 'Select Service');
        if (!empty($services)) {
            foreach ($services as $service) {
                $service_options[$service['id']] = $service['name'];
            }
        }
        $data['services'] = $service_options;
        $data['selected_service'] = $service_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Get customers data - use empty array instead of "1" string
        $where = array();
        $customers_data = array();
        if (!empty($service_id)) {
            $customers_data = $this->service->getcustomersbyservice($where, $service_id, $start_date, $end_date);
        }
        $data['customers_data'] = $customers_data;

        // Calculate totals
        $total_amount = 0;
        if (!empty($customers_data)) {
            foreach ($customers_data as $row) {
                $total_amount += $row['amount'];
            }
        }
        $data['total_amount'] = $total_amount;
        $data['total_count'] = count($customers_data);

        $this->template->load('reports', 'service_customers', $data);
    }

    public function assignmentreports()
    {
        if ($this->session->role != 'admin' && $this->session->role != 'superadmin') {
            redirect('home/');
        }

        $data = ['title' => 'Assignment Reports'];
        $data['breadcrumb'] = array("active" => "Assignment Reports");
        $data['datatable'] = true;
        $data['alertify'] = true;

        // Get filter parameters
        $service_id = $this->input->get('service_id') ? $this->input->get('service_id') : NULL;
        $period = $this->input->get('period') ? $this->input->get('period') : '';
        $selected_date = $this->input->get('date') ? $this->input->get('date') : NULL;
        $selected_month = $this->input->get('month') ? $this->input->get('month') : NULL;
        $selected_year = $this->input->get('year') ? $this->input->get('year') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        // Get all services for dropdown
        $services = $this->master->getservices(['status' => 1]);
        $service_options = array('' => 'Select Service');
        if (!empty($services)) {
            foreach ($services as $service) {
                $service_options[$service['id']] = $service['name'];
            }
        }

        // Generate month dropdown options
        $month_options = array('' => 'Select Month');
        $current_year = date('Y');
        for ($i = 0; $i < 12; $i++) {
            $date = date('Y-m', strtotime("-$i months"));
            $year = date('Y', strtotime($date . '-01'));
            $month = date('n', strtotime($date . '-01'));
            $month_value = date('Ym', strtotime($date . '-01'));
            $month_label = date('F Y', strtotime($date . '-01'));
            $month_options[$month_value] = $month_label;
        }

        // Generate year dropdown options
        $year_options = array('' => 'Select Year');
        $current_year = date('Y');
        for ($i = 0; $i < 5; $i++) {
            $year = $current_year - $i;
            $year_options[$year] = $year;
        }

        $data['services'] = $service_options;
        $data['month_options'] = $month_options;
        $data['year_options'] = $year_options;
        $data['selected_service'] = $service_id;
        $data['selected_period'] = $period;
        $data['selected_date'] = $selected_date;
        $data['selected_month'] = $selected_month;
        $data['selected_year'] = $selected_year;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Check if assignment_done column exists
        $table_prefix = $this->db->dbprefix;
        $assessments_table = $table_prefix . 'assessments';
        $check_column = $this->db->query("SHOW COLUMNS FROM `{$assessments_table}` LIKE 'assignment_done'");
        $has_assignment_done = ($check_column && $check_column->num_rows() > 0);

        // Build query to get assignment reports
        // Use aggregate functions to comply with ONLY_FULL_GROUP_BY mode
        if ($has_assignment_done) {
            $this->db->select('a.id, 
                              MAX(a.order_id) as order_id, 
                              MAX(a.date) as assessment_date, 
                              MAX(a.assignment_done) as assignment_done, 
                              MAX(a.assignment_done_date) as assignment_done_date, 
                              MAX(p.service_id) as service_id, 
                              MAX(s.name) as service_name, 
                              MAX(c.user_id) as customer_id, 
                              MAX(c.name) as customer_name,
                              MAX(oa.user_id) as employee_id, 
                              MAX(u.username) as employee_name, 
                              MAX(u.name) as employee_full_name,
                              MAX(p.added_on) as order_date');
        } else {
            // Fallback if columns don't exist yet
            $this->db->select('a.id, 
                              MAX(a.order_id) as order_id, 
                              MAX(a.date) as assessment_date, 
                              0 as assignment_done, 
                              NULL as assignment_done_date, 
                              MAX(p.service_id) as service_id, 
                              MAX(s.name) as service_name, 
                              MAX(c.user_id) as customer_id, 
                              MAX(c.name) as customer_name,
                              MAX(oa.user_id) as employee_id, 
                              MAX(u.username) as employee_name, 
                              MAX(u.name) as employee_full_name,
                              MAX(p.added_on) as order_date');
        }

        // Use subquery to get only the latest order_assign per order_id to prevent duplicates
        $order_assign_table = $this->db->dbprefix . 'order_assign';
        $order_assign_subquery = "(SELECT order_id, MAX(id) as max_id FROM {$order_assign_table} WHERE status = 0 GROUP BY order_id) latest_oa";

        $this->db->from('assessments a');
        $this->db->join('purchases p', 'a.order_id = p.id', 'left');
        $this->db->join('services s', 'p.service_id = s.id', 'left');
        $this->db->join('customers c', 'p.user_id = c.user_id', 'left');
        $this->db->join($order_assign_subquery, 'a.order_id = latest_oa.order_id', 'left', false);
        $this->db->join('order_assign oa', 'latest_oa.order_id = oa.order_id AND latest_oa.max_id = oa.id', 'left');
        $this->db->join('users u', 'oa.user_id = u.id', 'left');

        // Group by assessment ID to ensure one row per assessment
        // Using MAX() for other columns to comply with ONLY_FULL_GROUP_BY mode
        $this->db->group_by('a.id');

        // Apply filters
        if (!empty($service_id)) {
            $this->db->where('p.service_id', $service_id);
        }

        // Ensure date field is not NULL
        $this->db->where('a.date IS NOT NULL');

        // Date filter based on period
        if ($period == 'date' && !empty($selected_date)) {
            $this->db->where('DATE(a.date)', $selected_date);
        } elseif ($period == 'month' && !empty($selected_month)) {
            // selected_month format is YYYYMM (e.g., 202602 for February 2026)
            // Convert to proper date range for better performance
            if (strlen($selected_month) == 6) {
                $year = substr($selected_month, 0, 4);
                $month = substr($selected_month, 4, 2);
                $start_date_filter = $year . '-' . $month . '-01';
                $end_date_filter = date('Y-m-t', strtotime($start_date_filter)); // Last day of month
                $this->db->where('DATE(a.date) >=', $start_date_filter);
                $this->db->where('DATE(a.date) <=', $end_date_filter);
            } else {
                // Fallback to DATE_FORMAT if format is unexpected
                $this->db->where('DATE_FORMAT(a.date, "%Y%m")', $selected_month);
            }
        } elseif ($period == 'year' && !empty($selected_year)) {
            $this->db->where('YEAR(a.date)', $selected_year);
        } elseif ($period == 'custom' && !empty($start_date) && !empty($end_date)) {
            $this->db->where('DATE(a.date) >=', $start_date);
            $this->db->where('DATE(a.date) <=', $end_date);
        }

        $this->db->order_by('a.date', 'DESC');
        $this->db->order_by('a.id', 'DESC');
        $query = $this->db->get();

        // Check if query succeeded
        if ($query === false) {
            $error = $this->db->error();
            $last_query = $this->db->last_query();
            log_message('error', 'Assignment reports query failed: ' . $error['message']);
            log_message('error', 'Failed SQL Query: ' . $last_query);
            log_message('error', 'Month filter value: ' . $selected_month . ', Period: ' . $period);
            $this->session->set_flashdata('err_msg', 'Error loading assignment reports. Check error logs for details.');
            $assignments = array();
        } else {
            $assignments = $query->result_array();
        }

        // Process assignments to handle assignment_done field (backward compatibility)
        foreach ($assignments as $key => $assignment) {
            if (!isset($assignment['assignment_done'])) {
                $assignments[$key]['assignment_done'] = 0;
            }
        }

        $data['assignments'] = $assignments;

        // Calculate totals
        $total_pending = 0;
        $total_done = 0;
        if (!empty($assignments)) {
            foreach ($assignments as $row) {
                if (isset($row['assignment_done']) && $row['assignment_done'] == 1) {
                    $total_done++;
                } else {
                    $total_pending++;
                }
            }
        }
        $data['total_pending'] = $total_pending;
        $data['total_done'] = $total_done;
        $data['total_records'] = count($assignments);

        $this->template->load('reports', 'assignment_reports', $data);
    }

    /**
     * Admin/Employee view: Show all customers with pending package renewals
     * Displays which customers have packages pending to renew
     */
    public function pendingpackagerenewals()
    {
        // Only allow admin and employee access
        if ($this->session->role != 'admin' && $this->session->role != 'superadmin' && $this->session->role != 'employee') {
            redirect('home/');
            return;
        }

        $data = ['title' => 'Pending Package Renewals'];
        $data['breadcrumb'] = array("active" => "Pending Package Renewals");
        $data['datatable'] = true;
        $data['alertify'] = true;

        // Get filter parameters
        $selected_year = $this->input->get('year') ? $this->input->get('year') : NULL;
        $customer_id = $this->input->get('customer_id') ? $this->input->get('customer_id') : NULL;

        // Get current year (default to current financial year)
        $current_date = date('Y-m-d');
        $current_month = date('m');
        $current_year = date('Y');

        // Financial year starts from April, so if current month < April, use previous year
        if ($current_month < 4) {
            $fy_start_year = $current_year - 1;
        } else {
            $fy_start_year = $current_year;
        }

        // Default year format: YYYY(YYYY+1) e.g., 20232024
        $default_year = $fy_start_year . ($fy_start_year + 1);
        $year = $selected_year ? $selected_year : $default_year;

        // Get all customers (filter by employee if needed)
        $where_customers = array();
        if ($this->session->role == 'employee') {
            // Employees can only see customers they added
            $where_customers['md5(t1.added_by)'] = $this->session->user;
        }

        $all_customers = $this->customer->getcustomers($where_customers);

        // Get all firms for these customers
        $customer_user_ids = array();
        if (!empty($all_customers)) {
            $customer_user_ids = array_column($all_customers, 'user_id');
        }

        $pending_renewals = array();

        if (!empty($customer_user_ids)) {
            // Get all service packages for these customers
            $packages_where = array();
            if (!empty($customer_id)) {
                $packages_where['t1.user_id'] = $customer_id;
            }

            $all_packages = $this->customer->getservicepackage($packages_where, 'all');

            // Group packages by user_id and firm_id
            $packages_by_customer = array();
            if (!empty($all_packages)) {
                foreach ($all_packages as $pkg) {
                    $key = $pkg['user_id'] . '_' . $pkg['firm_id'];
                    if (!isset($packages_by_customer[$key])) {
                        $packages_by_customer[$key] = array();
                    }
                    $packages_by_customer[$key][] = $pkg;
                }
            }

            // Process each customer-firm combination
            foreach ($packages_by_customer as $key => $packages) {
                if (empty($packages)) continue;

                $first_pkg = $packages[0];
                $user_id = $first_pkg['user_id'];
                $firm_id = $first_pkg['firm_id'];
                $customer_name = $first_pkg['customer_name'];
                $firm_name = $first_pkg['firm_name'];

                // Get customer ID from user_id
                $customer = $this->customer->getcustomers(array('t1.user_id' => $user_id), 'single');
                $customer_id = !empty($customer) ? $customer['id'] : null;

                // Find current year package
                $current_package = null;
                $expired_packages = array();

                foreach ($packages as $pkg) {
                    if ($pkg['year'] == $year) {
                        $current_package = $pkg;
                    } else {
                        $expired_packages[] = $pkg;
                    }
                }

                // Get current year service IDs
                $current_service_ids = array();
                if (!empty($current_package) && !empty($current_package['service_ids'])) {
                    $current_service_ids = array_filter(array_map('trim', explode(',', $current_package['service_ids'])));
                }

                // Find renewal candidates (services in expired packages but not in current)
                $renewal_services = array();
                $expired_years = array();

                foreach ($expired_packages as $exp_pkg) {
                    if (empty($exp_pkg['service_ids'])) continue;

                    $exp_service_ids = array_filter(array_map('trim', explode(',', $exp_pkg['service_ids'])));
                    $exp_year = $exp_pkg['year'];

                    foreach ($exp_service_ids as $service_id) {
                        if (empty($service_id)) continue;

                        // If service is not in current package, it's a renewal candidate
                        if (!in_array($service_id, $current_service_ids, true)) {
                            if (!isset($renewal_services[$service_id])) {
                                // Get service details
                                $service = $this->master->getservices(array('id' => $service_id, 'status' => 1), 'single');
                                if (!empty($service)) {
                                    $renewal_services[$service_id] = array(
                                        'service_id' => $service_id,
                                        'service_name' => $service['name'],
                                        'expired_year' => $exp_year
                                    );
                                }
                            }
                        }
                    }

                    if (!empty($exp_service_ids)) {
                        $expired_years[] = $exp_year;
                    }
                }

                // Also check for pending purchases (status=0)
                $pending_purchases = array();
                if (!empty($current_service_ids) || !empty($renewal_services)) {
                    $all_service_ids = array_merge($current_service_ids, array_keys($renewal_services));
                    $all_service_ids = array_unique(array_filter($all_service_ids));

                    if (!empty($all_service_ids)) {
                        $user_id_escaped = $this->db->escape($user_id);
                        $firm_id_escaped = $this->db->escape($firm_id);
                        $year_escaped = $this->db->escape($year);
                        $service_ids_str = implode(',', array_map('intval', $all_service_ids));

                        $where_pending = "t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.year={$year_escaped} AND t1.status='0' AND t1.service_id IN ($service_ids_str)";
                        $pending_purchases = $this->service->getpurchasedservices($where_pending, 'all', true);
                    }
                }

                // Only add to list if there are renewal candidates or pending purchases
                if (!empty($renewal_services) || !empty($pending_purchases)) {
                    // Format year display
                    $year_display = $year;
                    if (strlen($year_display) == 8 && is_numeric($year_display)) {
                        $year1 = substr($year_display, 0, 4);
                        $year2 = substr($year_display, 4, 4);
                        $year_display = $year1 . '-' . $year2;
                    }

                    $pending_renewals[] = array(
                        'user_id' => $user_id,
                        'customer_id' => $customer_id,
                        'customer_name' => $customer_name,
                        'firm_id' => $firm_id,
                        'firm_name' => !empty($firm_name) ? $firm_name : 'N/A',
                        'year' => $year,
                        'year_display' => $year_display,
                        'renewal_services' => array_values($renewal_services),
                        'pending_purchases' => $pending_purchases,
                        'renewal_count' => count($renewal_services),
                        'pending_count' => count($pending_purchases),
                        'expired_years' => array_unique($expired_years)
                    );
                }
            }
        }

        // Get customer dropdown for filter
        $customer_options = array('' => 'All Customers');
        if (!empty($all_customers)) {
            foreach ($all_customers as $cust) {
                $customer_options[$cust['user_id']] = $cust['name'] . ' (' . $cust['mobile'] . ')';
            }
        }

        // Get year options (last 5 years)
        $year_options = array('' => 'Select Year');
        $current_fy = $fy_start_year;
        for ($i = 0; $i < 5; $i++) {
            $yr = ($current_fy - $i) . ($current_fy - $i + 1);
            $yr_display = ($current_fy - $i) . '-' . substr(($current_fy - $i + 1), -2);
            $year_options[$yr] = 'AY ' . $yr_display;
        }

        $data['pending_renewals'] = $pending_renewals;
        $data['customers'] = $customer_options;
        $data['years'] = $year_options;
        $data['selected_customer'] = $customer_id;
        $data['selected_year'] = $selected_year ? $selected_year : $year;

        $this->template->load('reports', 'pending_package_renewals', $data);
    }
}
