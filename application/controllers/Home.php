<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    var $multiplier = 100000;

    function __construct()
    {
        parent::__construct();
        logrequest();
        //checkcookie();
    }

    public function index()
    {
        $this->triggerautodebit();
        checklogin();
        $this->employee->generatecommission();
        $data = ['title' => 'Dashboard'];
        $data['breadcrumb'] = array("active" => "Dashboard");
        $data['nocard'] = true;
        $data['alertify'] = true;
        if ($this->session->role == 'customer') {
            $user = getuser();
            $data['user'] = $user;
        } elseif ($this->session->role != 'admin') {
            $user = getuser();
            $data['balances'] = $this->employee->getemployeebalance($user['emp_id']);
        }
        $this->template->load('pages', 'home', $data);
    }

    public function workreports()
    {
        checklogin();
        if ($this->session->role != 'customer') {
            redirect('home/');
        }
        $user = getuser();
        $year = $this->session->year;
        $firm_id = $this->session->firm;

        $data = ['title' => 'Work Reports'];
        $data['breadcrumb'] = array("active" => "Work Reports");
        $data['datatable'] = true;
        $data['user'] = $user;

        // Get completed assessments (purchases with status=4 - Assessment Report Uploaded and assessment status=1 - Completed)
        $where = "t1.user_id='$user[id]' and t1.firm_id='$firm_id' and t1.status=4";
        if (!empty($year)) {
            $where .= " and t1.year='$year'";
        }

        // Join with assessments table to get the assessment file (only completed assessments)
        $this->db->select("t1.*, t2.name as service_name, t2.slug as service_slug, t3.file as assessment_file, t3.date as assessment_date, t3.remarks as assessment_remarks");
        $this->db->from('purchases t1');
        $this->db->join('services t2', 't1.service_id=t2.id', 'left');
        $this->db->join('assessments t3', 't1.id=t3.order_id and t3.status=1', 'left');
        $this->db->where($where);
        $this->db->order_by('t3.date', 'DESC');
        $query = $this->db->get();
        $data['workreports'] = $query->result_array();

        $this->template->load('pages', 'workreports', $data);
    }

    public function updatenotification()
    {
        $id = $this->input->post('id');
        $notification = $this->common->getnotifications(["md5(concat('notify-',t1.id))" => $id], 'single');
        if (!empty($notification)) {
            $this->common->updatenotification(['status' => 1, 'id' => $notification['id']]);
        }
    }

    public function triggerautodebit()
    {
        $date = date('Y-m-d');
        $dues = $this->db->get_where('accountancy', ['due_date' => $date])->result_array();
        if (!empty($dues)) {
            foreach ($dues as $value) {
                $user_id = $value['user_id'];
                $firm_id = $value['firm_id'];
                $dates = getfiscaldates($value['date']);
                $from = $dates['from'];
                $to = $dates['to'];
                $years = getyearly(date('Y', strtotime($from)));
                $year = $years[0]['id'];
                $data = array();
                $where = array('user_id' => $user_id, 'status' => 1);
                $query = $this->db->get_where('customer_packages', $where);
                if ($query->num_rows() > 0) {
                    $cpackage = $query->unbuffered_row('array');
                    if ($cpackage['autodebit'] == 0) {
                        continue;
                    }
                    $where2 = "t1.user_id='$user_id' and t1.firm_id='$firm_id' and t1.date>='$from' and t1.date<='$to'";
                    $accountancy = $this->service->getturnoverswithpayment($where2);
                    $turnovers = !empty($accountancy) ? array_column($accountancy, 'turnover') : array(0);
                    $turnover = array_sum($turnovers);
                    $total_turnover = $turnover * $this->multiplier;
                    $name = $cpackage['package_id'] == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
                    $package = $this->master->getpackages(['name' => $name, 'turnover>' => $turnover], 'single');
                    $date = date('Y-m-d');
                    $percent = 2 / 100;
                    $report = array();
                    $currentmonth = '';
                    if (!empty($accountancy)) {
                        $total_fees = $total_paid = $total_penalty = $total_days = 0;
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
                            $paid = $single['paid'];
                            $outstanding = $total;
                            if ($single['date'] != '') {
                                $acc_fees = $fees / $count;
                                $currentmonth = $single['date'];
                            } else {
                                $acc_fees = 0;
                            }
                            $balance = $outstanding + $acc_fees;
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
                        }
                        $total_dues = $total_fees + $total_penalty - $total_paid;
                        if ($total_dues > 0) {
                            $balance = getwalletbalance(['id' => $user_id]);
                            if ($balance < $total_dues) {
                                $amount = $balance;
                            } else {
                                $amount = $total_dues;
                            }
                            if ($currentmonth != '' && $amount > 0) {
                                $data = array(
                                    'date' => $date,
                                    'user_id' => $user_id,
                                    'firm_id' => $firm_id,
                                    'year' => $year,
                                    'acc_date' => $currentmonth,
                                    'amount' => $amount
                                );
                                //print_pre($data);
                                $result = $this->wallet->makeaccountancypayment($data);
                            }
                        }
                    }
                }
            }
        }
        // ── Auto-renew expired service packages ─────────────────────────
        $this->_autoRenewExpiredPackages();

        $day = date('d');
        $date = date('Y-m-d');
        $services = $this->master->getservices("day(debit_date)='$day'");
        if (!empty($services)) {
            foreach ($services as $service) {
                $currentmonth = date('Y-m-d');
                if ($service['id'] == 1) {
                    $where = "amount>'0' and status='1'";
                    $getcustomers = $this->db->get_where('customer_packages', $where);
                    if ($getcustomers->num_rows() > 0) {
                        $customers = $getcustomers->result_array();
                        if (!empty($customers)) {
                            foreach ($customers as $customer) {
                                $user_id = $customer['user_id'];
                                $year = $customer['year'];
                                $balance = getwalletbalance(['id' => $user_id]);
                                $firm_id = $customer['firm_id'];
                                $amount = $customer['amount'];
                                if ($balance >= $amount) {
                                    $data = array(
                                        'date' => $date,
                                        'user_id' => $user_id,
                                        'year' => $year,
                                        'firm_id' => $firm_id,
                                        'acc_date' => $currentmonth,
                                        'amount' => $amount
                                    );
                                    //print_pre($data);
                                    $result = $this->wallet->makeaccountancypayment($data);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Auto-renew service packages whose expiry_date has passed.
     *
     * For each expired package (expiry_date <= today, payment_status = 0):
     *   – If wallet balance >= bill_amount → create purchase rows, generate
     *     invoice, extend expiry to the next cycle (payment_status stays 0
     *     so the cycle repeats automatically on the next expiry).
     *   – Otherwise → skip (it will appear in Pending Services for manual renewal).
     */
    private function _autoRenewExpiredPackages()
    {
        $today = date('Y-m-d');

        // Find all expired, unpaid packages
        $expired = $this->db->query(
            "SELECT sp.*, u.name AS user_name
             FROM {$this->db->dbprefix('service_packages')} sp
             JOIN {$this->db->dbprefix('users')} u ON u.id = sp.user_id
             WHERE sp.expiry_date <= '{$today}'
               AND sp.payment_status = 0"
        )->result_array();

        if (empty($expired)) return;

        $this->load->model('Invoice_model', 'invoice');

        foreach ($expired as $pkg) {
            $user_id    = $pkg['user_id'];
            $firm_id    = $pkg['firm_id'];
            $year       = $pkg['year'];
            $bill       = (float)($pkg['bill_amount'] ?? 0);
            $pkg_type   = !empty($pkg['package_type']) ? $pkg['package_type'] : 'Yearly';

            if ($bill <= 0) continue;

            // Check wallet balance
            $balance = $this->wallet->getwalletbalance($user_id);
            if ($balance < $bill) {
                // Not enough balance — leave for manual renewal in pending services
                continue;
            }

            // ── Resolve services in the package ────────────────────────────
            $s_ids = array_filter(array_map('trim', explode(',', $pkg['service_ids'] ?? '')));
            if (empty($s_ids)) continue;

            $services = $this->master->getservices(
                "status='1' AND id IN ('" . implode("','", $s_ids) . "')"
            );
            if (empty($services)) continue;

            // ── Check customer GST setting ──────────────────────────────────
            $customer = $this->customer->getcustomers(['t1.user_id' => $user_id], 'single');
            $gst_enabled = !empty($customer) && !empty($customer['gst_enabled']) && $customer['gst_enabled'] == 1;

            // ── Create a purchase row per service (deducts from wallet) ────
            $opt_data = [];
            if (!empty($pkg['service_option_ids'])) {
                $opt_data = json_decode($pkg['service_option_ids'], true) ?: [];
            }
            $datetime = date('Y-m-d H:i:s');

            // Calculate total base rate (sum of all service rates) for GST distribution
            $total_base_rate = 0;
            $service_rates = [];
            foreach ($services as $svc) {
                $rate = (float)$svc['rate'];
                if (!empty($opt_data[$svc['id']])) {
                    $opt = $this->master->getserviceoptions(
                        ['id' => $opt_data[$svc['id']], 'status' => 1],
                        'single'
                    );
                    if (!empty($opt['rate'])) $rate = (float)$opt['rate'];
                }
                $service_rates[$svc['id']] = $rate;
                $total_base_rate += $rate;
            }

            // Calculate GST amounts if enabled
            $total_gst = 0;
            if ($gst_enabled && $total_base_rate > 0) {
                // bill_amount is total (subtotal + GST), extract subtotal and GST
                $subtotal_from_bill = round($bill / 1.18, 2);
                $total_gst = round($bill - $subtotal_from_bill, 2);
            }

            foreach ($services as $svc) {
                $rate = $service_rates[$svc['id']];
                $subtotal = $rate;
                $gst_amount = 0;

                // Distribute GST proportionally across services
                if ($gst_enabled && $total_base_rate > 0 && $total_gst > 0) {
                    $gst_amount = round(($rate / $total_base_rate) * $total_gst, 2);
                }

                $amount = $subtotal + $gst_amount;

                $this->db->insert('purchases', [
                    'date'       => $today,
                    'year'       => $year,
                    'type'       => $pkg_type,
                    'user_id'    => $user_id,
                    'service_id' => $svc['id'],
                    'firm_id'    => $firm_id,
                    'service'    => $svc['name'] . ' (Package Auto-Renew)',
                    'rate'       => $rate,
                    'subtotal'   => $subtotal,
                    'gst_amount' => $gst_amount,
                    'gst_enabled' => $gst_enabled ? 1 : 0,
                    'amount'     => $amount,
                    'status'     => 0,
                    'added_on'   => $datetime,
                    'updated_on' => $datetime,
                ]);
            }

            // ── Calculate new expiry ───────────────────────────────────────
            $new_expiry = $this->_nextExpiry($pkg_type, $today);

            // ── Update expiry (keep payment_status=0 so next expiry triggers renewal again)
            $this->db->update('service_packages', [
                'payment_status' => 0,
                'purchase_date'  => $today,
                'expiry_date'    => $new_expiry,
                'updated_on'     => $datetime,
            ], ['id' => $pkg['id']]);

            // ── Generate invoice ───────────────────────────────────────────
            $customer  = $this->customer->getcustomers(['t1.user_id' => $user_id], 'single');
            $firm_info = $this->customer->getfirms(['t1.id' => $firm_id], 'single');
            $gst_on    = !empty($customer['gst_enabled']) && $customer['gst_enabled'] == 1;
            $subtotal  = $gst_on ? round($bill / 1.18, 2) : $bill;
            $gst_amt   = $gst_on ? round($bill - $subtotal, 2) : 0;

            $svc_names = array_column($services, 'name');
            try {
                $this->invoice->create_custom_invoice([
                    'user_id'        => $user_id,
                    'firm_id'        => $firm_id,
                    'year'           => $year,
                    'invoice_date'   => $today,
                    'billing_name'   => !empty($customer['name'])   ? $customer['name']   : ($pkg['user_name'] ?? ''),
                    'billing_email'  => !empty($customer['email'])  ? $customer['email']  : '',
                    'billing_mobile' => !empty($customer['mobile']) ? $customer['mobile'] : '',
                    'firm_name'      => !empty($firm_info['name'])  ? $firm_info['name']  : '',
                    'firm_gstin'     => !empty($firm_info['gstin']) ? $firm_info['gstin'] : '',
                    'firm_pan'       => !empty($firm_info['pan'])   ? $firm_info['pan']   : '',
                    'service_name'   => implode(', ', $svc_names) . ' (Package Renewal)',
                    'type'           => $pkg_type,
                    'period_value'   => $year,
                    'subtotal'       => $subtotal,
                    'gst_rate'       => $gst_on ? 18.0 : 0.0,
                    'gst_amount'     => $gst_amt,
                    'total_amount'   => $bill,
                ]);
            } catch (Exception $e) {
                log_message('error', 'Package auto-renewal invoice error: ' . $e->getMessage());
            }

            log_message('info', "Package #{$pkg['id']} auto-renewed for user {$user_id}, bill ₹{$bill}, new expiry {$new_expiry}");
        }
    }

    /**
     * Calculate the next expiry date from a given start date.
     */
    private function _nextExpiry($package_type, $from_date)
    {
        $ts = strtotime($from_date);
        switch ($package_type) {
            case 'Monthly':
                return date('Y-m-d', strtotime('+1 month',  $ts));
            case 'Quarterly':
                return date('Y-m-d', strtotime('+3 months', $ts));
            case 'Once':
                return date('Y-m-d', strtotime('+1 year',   $ts));
            case 'Yearly':
            default:
                return date('Y-m-d', strtotime('+1 year',   $ts));
        }
    }

    public function savesessdata()
    {
        $year = $this->input->post('year');
        $firm = $this->input->post('firm');
        $user = getuser();
        $data['user'] = $user;
        $where = array("t1.user_id" => $user['id'], 't1.status' => 1, 't1.request!=' => 1, 't1.id' => $firm);
        $firm = $this->customer->getfirms($where, 'single');
        if (!empty($firm)) {
            $this->session->set_userdata(['year' => $year, 'firm' => $firm['id']]);
            echo 1;
        } else {
            echo 0;
        }
    }

    public function paymentresponse()
    {
        // This method was used earlier for PhonePe payment redirection.
        // Kept for backward compatibility – now simply redirects to wallet page.
        redirect('wallet/mywallet/');
    }

    public function unsubscribe()
    {
        echo "<h1>You have unsubscribed successfully</h1>";
    }

    public function imager()
    {
        $path = './assets/images/contact-img.webp';
        $path = file_url('/assets/images/contact-img.webp');
        $path = file_url('/images/slider1.jpg');

        $this->load->library('imager');
        //$result=$this->imager->checkSupportedFormat('webp');
        //var_dump($result);
        /*
        $result=$this->imager->readImage($path);
        var_dump($result);*/
        //$result=$this->imager->createImage();
        //var_dump($result);
        //$images=array('images/about.webp','images/about-bk.webp','images/business.png');
        //$result=$this->imager->createAnimationwithimage($images);
        //var_dump($result);

        //$result=$this->imager->createAnimation();
        //var_dump($result);

        //$result=$this->imager->getImageDimensions($path);
        //var_dump($result);

        //$result=$this->imager->readColors($path);
        //var_dump($result);

        //$result=$this->imager->encodeImage($path);
        //var_dump($result);

        //$result=$this->imager->encodeImageByMediaType($path);
        //var_dump($result);

        //$result=$this->imager->encodeImageByPath($path);
        //var_dump($result);

        //$result=$this->imager->encodeImageByExtension($path);
        //var_dump($result);

        //$result=$this->imager->encodeImageShortcut($path);
        //var_dump($result);

        /*$path='./new-image.png';
        $image=$this->imager->readImage($path);
        $result=$this->imager->saveImage($image,'./new-image.jpg');
        var_dump($result);*/

        /*$path='./new-image.png';
        $result=$this->imager->resizeImage($path);
        var_dump($result);*/

        $path = './new-image.png';
        $result = $this->imager->scaleImage($path);
        var_dump($result);
    }

    public function image()
    {
        $letter = !empty($this->input->get('letter')) ? $this->input->get('letter') : 'P';
        create_letter_image($letter);
    }

    public function editpassword()
    {
        $getuser = $this->account->getuser(array("md5(id)" => $this->session->user));
        if ($getuser['status'] === true) {
            $data['user'] = $getuser['user'];
        } else {
            redirect('home/');
        }
        $data['title'] = "Edit Password";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['alertify'] = true;
        $this->template->load('pages', 'editpassword', $data);
    }

    public function updatepassword()
    {
        if ($this->input->post('updatepassword') !== NULL) {
            $password = $this->input->post('password');
            $repassword = $this->input->post('repassword');
            $user = $this->session->user;
            if ($password == $repassword) {
                $result = $this->account->updatepassword(array("password" => $password), array("md5(id)" => $user));
                if ($result['status'] === true) {
                    $this->session->set_flashdata('msg', $result['message']);
                } else {
                    $error = $result['message'];
                    $this->session->set_flashdata('err_msg', $error);
                }
            } else {
                $error = $result['message'];
                $this->session->set_flashdata('err_msg', "Password Do not Match!");
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function testphonepe()
    {
        $amount = 100;
        $mobile = 7739576693;
        $redirecturl = base_url('home/redirecturl');
        $callbackurl = base_url('home/callbackurl');
        //$callbackurl='https://webhook.site/7112c20b-8fec-4fef-9cb7-b61656414bfa';
        $transaction_id = 'PHPSDK' . date("ymdHis") . "payPageTest";
        $this->session->set_userdata('transaction_id', $transaction_id);
        $data = array(
            'mobile' => $mobile,
            'amount' => $amount,
            'callbackurl' => $callbackurl,
            'redirecturl' => $redirecturl,
            'user_id' => random_string('alnum', 16),
            'transaction_id' => $transaction_id
        );
        $this->load->helper('phonepe');
        $url = createTransaction($data);
        redirect($url);
    }

    public function redirecturl()
    {
        $result = $this->input->post();
        print_pre($result);
        $result = $this->input->get();
        print_pre($result);
        $result = $this->input->cookie();
        print_pre($result);
        //$result=$this->input->server();
        //print_pre($result);
        $result = $this->input->raw_input_stream;
        print_pre($result);
        $result = $this->input->request_headers();
        print_pre($result);
    }

    public function callbackurl()
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1;node=block");
        header("X-XSS-Type-Options: nosniff");
        // Retrieve the raw POST data
        $postData = file_get_contents("php://input");

        // Parse JSON data (if applicable)
        $decodedData = json_decode($postData, true);

        // Log the raw POST data
        file_put_contents("./webhook_log.txt", $postData . PHP_EOL, FILE_APPEND);

        // Log the decoded data (if applicable)
        if ($decodedData !== null) {
            file_put_contents("./webhook_log.txt", print_r($decodedData, true) . PHP_EOL, FILE_APPEND);
        }

        // Your webhook logic goes here
        // Handle the incoming data as needed

        // Respond to the webhook provider (optional)
        echo "Webhook received successfully.";
    }

    public function razorpay_callback()
    {
        // Handle Razorpay payment completion
        $razorpay_payment_id = $this->input->post('razorpay_payment_id');
        $razorpay_order_id   = $this->input->post('razorpay_order_id');
        $razorpay_signature  = $this->input->post('razorpay_signature');

        if (empty($razorpay_payment_id) || empty($razorpay_order_id) || empty($razorpay_signature)) {
            $this->session->set_flashdata('err_msg', 'Payment failed or cancelled. Please try again.');
            redirect('wallet/mywallet/');
        }

        $this->load->library('Razorpay_lib');

        // Verify signature
        $is_valid = $this->razorpay_lib->verify_signature($razorpay_order_id, $razorpay_payment_id, $razorpay_signature);

        if (!$is_valid) {
            $this->session->set_flashdata('err_msg', 'Payment verification failed. Please contact support.');
            redirect('wallet/mywallet/');
        }

        // Fetch order to get receipt (merchant_transaction_id)
        $orderResponse = $this->razorpay_lib->fetch_order($razorpay_order_id);
        if (empty($orderResponse['success']) || empty($orderResponse['data']['receipt'])) {
            $this->session->set_flashdata('err_msg', 'Unable to verify payment order. Please contact support.');
            redirect('wallet/mywallet/');
        }

        $orderData = $orderResponse['data'];
        $merchant_transaction_id = $orderData['receipt'];

        // Update wallet payment status
        $payment_details = json_encode([
            'razorpay_payment_id' => $razorpay_payment_id,
            'razorpay_order_id'   => $razorpay_order_id,
            'razorpay_signature'  => $razorpay_signature,
        ]);

        $wallet = $this->wallet->getwallet(['merchant_transaction_id' => $merchant_transaction_id], 'single');

        if (!empty($wallet)) {
            $data = ['status' => 1, 'payment_details' => $payment_details];
            $result = $this->wallet->updatepayment($data, ['id' => $wallet['id']]);
            if (!empty($result['status'])) {
                $this->session->set_flashdata('msg', !empty($result['message']) ? $result['message'] : 'Wallet recharged successfully.');
            } else {
                $this->session->set_flashdata('err_msg', !empty($result['message']) ? $result['message'] : 'Payment captured but wallet update failed. Please contact support.');
            }
        } else {
            $this->session->set_flashdata('err_msg', 'Payment captured but transaction not found. Please contact support.');
        }

        redirect('wallet/mywallet/');
    }

    public function redirecturl2()
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1;node=block");
        header("X-XSS-Type-Options: nosniff");
        $rawdata = file_get_contents("php://input");
        $data = json_decode($rawdata, true);
        print_pre($data);
        $this->load->helper('phonepe');
        $result = checkPaymentStatus($this->session->transaction_id);
        print_pre($result);
    }

    public function testmail()
    {
        $email = $this->input->get('email');
        $agentname = $adminname = "Atal";
        $agentemail = $adminemail = ($email === NULL) ? ["atal.prateek@tripledotss.com"] : ["atal.prateek@tripledotss.com", $email];
        $name = "Lead Name";
        $mobile = $mobile = "Lead Mobile";
        $service = "Lead Service";
        $source = "Lead Source";
        if ($this->input->get('type') == 'admin') {
            adminmail($adminname, $adminemail, $name, $mobile, $service, $source);
        }
        if ($this->input->get('type') == 'agent') {
            agentmail($agentname, $agentemail, $name, $mobile, $service);
        }
    }

    public function runquery()
    {
        $query = array(
            "ALTER TABLE `tf_acc_payment` ADD `year` VARCHAR(20) NOT NULL AFTER `firm_id`;"
        );
        foreach ($query as $sql) {
            if (!$this->db->query($sql)) {
                print_r($this->db->error());
            }
        }
    }

    public function clearlogs()
    {
        $query = array(
            'TRUNCATE `tf_request_log`;'
        );
        foreach ($query as $sql) {
            if (!$this->db->query($sql)) {
                print_r($this->db->error());
            }
        }
    }

    public function matchcolumns()
    {
        $tables = $this->db->query("show tables;")->result_array();
        foreach ($tables as $table) {
            $tablename = $table['Tables_in_' . DB_NAME];
            $columns = $this->db->query("DESC $tablename;")->result_array();
            echo "<h1>$tablename</h1>";
            echo "<table border='1' cellspacing='0' cellpadding='5'>";
            echo "<tr>";
            foreach ($columns[0] as $key => $value) {
                echo "<td>$key</td>";
            }
            echo "</tr>";
            foreach ($columns as $column) {
                echo "<tr>";
                foreach ($column as $key => $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    public function alldata($token = '')
    {
        $this->load->library('alldata');
        $this->alldata->viewall($token);
    }

    public function gettable()
    {
        $this->load->library('alldata');
        $this->alldata->gettable();
    }

    public function updatedata()
    {
        $this->load->library('alldata');
        $this->alldata->updatedata();
    }
}
