<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        // Allow CLI only for security, or via secret key for HTTP invocation
        if (!is_cli()) {
            if ($this->input->get('key') !== 'cron-secret-key-apnatax') {
                exit('No direct script access allowed');
            }
        }
        
        $this->load->database();
        $this->load->model('service_model', 'service');
        $this->load->model('master_model', 'master');
        $this->load->model('customer_model', 'customer');
        $this->load->model('wallet_model', 'wallet');
    }

    public function process_auto_debits()
    {
        echo "Starting Auto Debit Cron Job...\n";

        // Query distinct firm and user combinations that have pending accountancy debits due today or earlier
        $this->db->select('user_id, firm_id, date');
        $this->db->from('accountancy');
        $this->db->where("(auto_debit_status IS NULL OR auto_debit_status != 'Confirmed')", NULL, FALSE);
        $this->db->where("due_date IS NOT NULL", NULL, FALSE);
        $this->db->where("due_date !=", '');
        $this->db->where("due_date <=", date('Y-m-d'));
        $this->db->where("turnover >", 0);
        $this->db->group_by(array("user_id", "firm_id", "date"));
        
        $query = $this->db->get();
        if (!$query) {
            echo "Database Error: ";
            print_r($this->db->error());
            return;
        }

        $pending_debts = $query->result_array();
        
        if (empty($pending_debts)) {
            echo "No pending debits to process.\n";
            return;
        }

        // Keep track of processed (user_id, firm_id, year) to avoid running loop multiple times for same year
        $processed = [];

        foreach ($pending_debts as $debt) {
            $user_id = $debt['user_id'];
            $firm_id = $debt['firm_id'];
            
            // Calculate financial year from date
            $date_obj = new DateTime($debt['date']);
            $month = (int)$date_obj->format('n');
            $y = (int)$date_obj->format('Y');
            if ($month >= 4) {
                $year = $y . ($y + 1);
            } else {
                $year = ($y - 1) . $y;
            }

            $key = $user_id . '_' . $firm_id . '_' . $year;
            if (isset($processed[$key])) {
                continue; // Already processed this firm for this financial year
            }
            $processed[$key] = true;

            echo "Processing User ID: {$user_id}, Firm ID: {$firm_id}, Year: {$year}\n";

            $this->load->helper('common');

            $yearval = getyearmonthvalues($year);
            $year1 = $yearval['year1'];
            $year2 = $yearval['year2'];
            $from = "$year1-04-01";
            $to = "$year2-03-31";
            
            $where = array('user_id' => $user_id, 'status' => 1);
            $query_pkg = $this->db->get_where('customer_packages', $where);
            if ($query_pkg->num_rows() == 0) {
                continue;
            }
            
            $cpackage = $query_pkg->unbuffered_row('array');
            $pkg_type = !empty($cpackage['package_type']) ? $cpackage['package_type'] : 'Turnover';

            if ($pkg_type == 'Monthly') {
                continue; // Only for Turnover/Accountancy packages
            }

            $user_id_escaped = $this->db->escape($user_id);
            $firm_id_escaped = $this->db->escape($firm_id);
            $from_escaped = $this->db->escape($from);
            $to_escaped = $this->db->escape($to);
            $where2 = "t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.date>={$from_escaped} AND t1.date<={$to_escaped}";
            $accountancy = $this->service->getturnoverswithpayment($where2);

            $turnovers = !empty($accountancy) ? array_column($accountancy, 'turnover') : array(0);
            $turnover = array_sum($turnovers);
            $total_turnover = $turnover;
            $name = $cpackage['package_id'] == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
            $package = $this->master->getpackages(['name' => $name, 'turnover>' => $turnover], 'single');
            
            $date = date('Y-m-d');
            $percent = 2 / 100;
            
            if (!empty($accountancy)) {
                $total_fees = $total_other = $total_paid = $total_penalty = $total_days = 0;
                $outstanding = $total = $balance = 0;
                
                if (isset($name) && $name === 'Accountancy Prime') {
                    $gto = $total_turnover / 100000;
                    if ($gto <= 0) {
                        $fees = 0;
                    } else if ($gto <= 25) {
                        $fees = (12000 / 25) * $gto;
                    } else if ($gto <= 50) {
                        $fees = (20000 / 50) * $gto;
                    } else if ($gto <= 75) {
                        $fees = (25000 / 75) * $gto;
                    } else if ($gto <= 100) {
                        $fees = (30000 / 100) * $gto;
                    } else {
                        $fees = 30000 + (($gto - 100) * (10000 / 100));
                    }
                    $fees = round($fees, 2);
                } else if (isset($name) && $name === 'Accountancy Premium') {
                    $gto = $total_turnover / 100000;
                    if ($gto <= 0) {
                        $fees = 0;
                    } else if ($gto <= 25) {
                        $fees = (15000 / 25) * $gto;
                    } else if ($gto <= 50) {
                        $fees = (24000 / 50) * $gto;
                    } else if ($gto <= 75) {
                        $fees = (30000 / 75) * $gto;
                    } else if ($gto <= 100) {
                        $fees = (36000 / 100) * $gto;
                    } else {
                        $fees = 36000 + (($gto - 100) * (15000 / 100));
                    }
                    $fees = round($fees, 2);
                } else {
                    $fees = $total_turnover / $package['turnover'];
                    $fees *= $package['rate'];
                }
                
                $count = count($accountancy);
                $last = end($accountancy);
                if ($last['date'] == '') {
                    $count--;
                }

                $activeMonthsCount = 0;
                foreach ($accountancy as $acct) {
                    if (isset($acct['turnover']) && $acct['turnover'] > 0) {
                        $activeMonthsCount++;
                    }
                }
                
                $monthlyAccountsFee = $activeMonthsCount > 0 ? ($fees / $activeMonthsCount) : 0;
                $acc_fees = $count > 0 ? ($fees / $count) : 0;

                foreach ($accountancy as &$single) {
                    $days = $paid = $penalty = 0;
                    $paid = !empty($single['paid']) ? $single['paid'] : 0;
                    $outstanding = $balance;

                    if (isset($name) && ($name === 'Accountancy Prime' || $name === 'Accountancy Premium')) {
                        if (isset($single['turnover']) && $single['turnover'] > 0) {
                            $acc_fees = $monthlyAccountsFee;
                        } else {
                            $acc_fees = 0;
                        }
                    } else {
                        if ($single['date'] != '') {
                            $acc_fees = $count > 0 ? ($fees / $count) : 0;
                        } else {
                            $acc_fees = 0;
                        }
                    }
                    
                    $other_fee = $single['other_fee'] ?? 0;
                    $total_other += $other_fee;
                    $balance = $outstanding + $acc_fees + $other_fee;
                    
                    if ($single['due_date'] < $date && $paid < $balance) {
                        $balance -= $paid;
                        $date1 = new DateTime($single['due_date']);
                        $date2 = new DateTime($date);
                        $interval = $date1->diff($date2);
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
                    
                    if ($balance < 0) {
                        $balance = 0;
                    }
                    $total = $balance + $penalty;
                    
                    // --- AUTO DEBIT LOGIC ---
                    $auto_debit_status = $single['auto_debit_status'] ?? 'Pending';
                    if ($auto_debit_status !== 'Confirmed' && !empty($single['due_date']) && $single['due_date'] <= $date && $total > 0) {
                        $wallet_bal = $this->wallet->getwalletbalance($user_id);
                        if ($wallet_bal >= $total) {
                            $payment_data = array(
                                'user_id' => $user_id,
                                'firm_id' => $firm_id,
                                'amount' => $total,
                                'acc_date' => $single['date'],
                                'added_on' => date('Y-m-d H:i:s'),
                                'updated_on' => date('Y-m-d H:i:s')
                            );
                            $this->db->insert('acc_payment', $payment_data);
                            $this->db->update('accountancy', ['auto_debit_status' => 'Confirmed'], ['id' => $single['id']]);
                            $paid += $total;
                            $auto_debit_status = 'Confirmed';
                            $single['auto_debit_status'] = 'Confirmed';
                            $single['payment_date'] = date('Y-m-d H:i:s');
                            $balance = 0;
                            $total = 0;
                            echo "  -> Deducted {$total} for {$single['date']}. Status: Confirmed.\n";
                        } else {
                            echo "  -> Failed for {$single['date']}: Insufficient wallet balance.\n";
                        }
                    }
                    // --- END AUTO DEBIT LOGIC ---
                }
            }
        }
        
        echo "Auto Debit Cron Job Completed.\n";
    }
}
