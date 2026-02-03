<?php
class Employee_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->db->db_debug = false;
    }

    public function saveemployee($data)
    {
        $userdata = $data;
        unset($data['username'], $data['password'], $data['role']);
        //print_pre($userdata,true);
        $this->db->trans_start();
        $data['added_on'] = $data['updated_on'] = date('Y-m-d H:i:s');
        if ($this->db->insert("employees", $data)) {
            $emp_id = $this->db->insert_id();
            $percentdata = array('emp_id' => $emp_id, 'percent' => $data['percent']);
            $this->addemployeepercent($percentdata);
            if (!empty($userdata['username']) && !empty($userdata['password']) && !empty($userdata['role'])) {
                unset($userdata['dob'], $userdata['address'], $userdata['parent_id'], $userdata['state'], $userdata['area_id']);
                unset($userdata['district'], $userdata['pan'], $userdata['aadhar'], $userdata['percent']);
                $userdata['emp_id'] = $emp_id;
                $result = $this->account->adduser($userdata);
                if ($result['status'] === true) {
                    $this->db->trans_complete();
                } else {
                    $error = $this->db->error();
                    $this->db->trans_rollback();
                    return array("status" => false, "message" => $error['message']);
                }
            } else {
                $this->db->trans_complete();
            }
            return array("status" => true, "message" => "Employee Added Successfully!");
        } else {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function generateempid($empid = '')
    {
        if ($empid != '') {
            $empid++;
        } else {
            $this->db->order_by("id desc");
            $array = $this->db->get("employees")->unbuffered_row('array');
            if (!empty($array)) {
                $empid = $array['empid'];
                $empid++;
            } else {
                $empid = "REL0001";
            }
        }

        $where = "empid='$empid'";
        $query = $this->db->get_where("employees", $where);
        if ($query->num_rows() != 0) {
            return $this->generateempid($empid);
        } else {
            return $empid;
        }
    }

    public function getemployees($where = array(), $type = "all")
    {
        //$columns="t1.*,t2.name as jobtitle_name,t3.name as branch_name";
        //$this->db->select($columns);
        $this->db->where($where);
        $this->db->from("employees t1");
        //$this->db->join("jobtitles t2","t1.jobtitle=t2.id");
        //$this->db->join("branches t3","t1.branch=t3.id");
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function updateemployee($data)
    {
        $id = $data['id'];
        unset($data['id']);
        $where = array("id" => $id);
        $data['updated_on'] = date('Y-m-d H:i:s');
        if ($this->db->update("employees", $data, $where)) {
            $percentdata = array('emp_id' => $id, 'percent' => $data['percent']);
            $this->addemployeepercent($percentdata);
            return array("status" => true, "message" => "Employee Updated Successfully!");
        } else {
            $error = $this->db->error();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function addemployeepercent($data)
    {
        $status = TRUE;
        $msg = "Employee Salary Percent Added Successfully!";
        $getlast = $this->db->get_where("emp_percent", ['emp_id' => $data['emp_id'], 'status' => 1]);
        if ($getlast->num_rows() > 0) {
            $msg = "Employee Salary Percent Updated Successfully!";
            $last = $getlast->unbuffered_row('array');
            if ($last['percent'] == $data['percent']) {
                $status = false;
            }
        }
        if ($status) {
            $datetime = date('Y-m-d H:i:s');
            $update = $this->db->update(
                "emp_percent",
                ['status' => 0, 'updated_on' => $datetime],
                ['emp_id' => $data['emp_id'], 'status' => 1]
            );
            $data['added_on'] = $data['updated_on'] = $datetime;
            if ($this->db->insert("emp_percent", $data)) {
                $service_id = $this->db->insert_id();
                return array("status" => true, "message" => $msg);
            } else {
                $error = $this->db->error();
                return array("status" => false, "message" => $error['message']);
            }
        } else {
            return array("status" => false, "message" => "No Changes Done!");
        }
    }

    public function myassessments($where = array(), $type = 'all')
    {
        $columns = "t1.*,t2.name,t2.mobile,t2.email,t3.name as service_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('order_assign a');
        $this->db->join('purchases t1', 'a.order_id=t1.id');
        $this->db->join('users t2', 't1.user_id=t2.id');
        $this->db->join('services t3', 't1.service_id=t3.id', 'left');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function generatecommission()
    {
        $data = array();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $this->db->select("t1.*,t2.user_id,t3.emp_id");
        $where = "t1.id not in (SELECT order_id from " . TP . "commission)";
        if (!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost') {
        } else {
            $where .= " and t1.status='1'";
        }
        $this->db->from('purchases t1');
        $this->db->join('order_assign t2', 't2.order_id=t1.id');
        $this->db->join('users t3', 't3.id=t2.user_id');
        $this->db->where($where);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $purchases = $query->result_array();
            //print_pre($purchases,true);
            foreach ($purchases as $single) {
                $where = array('emp_id' => $single['emp_id'], 'date(added_on)<=' => $single['date']);
                $this->db->order_by('added_on desc');
                $getpercent = $this->db->get_where('emp_percent', $where);
                if ($getpercent->num_rows() > 0) {
                    $percent = $getpercent->unbuffered_row()->percent;
                    $amount = ($single['amount'] * $percent) / 100;
                    $amount = round($amount, 2);
                    $data[] = array(
                        'date' => $date,
                        'emp_id' => $single['emp_id'],
                        'order_id' => $single['id'],
                        'order_amount' => $single['amount'],
                        'percent' => $percent,
                        'amount' => $amount,
                        'status' => 0,
                        'added_on' => $datetime,
                        'updated_on' => $datetime
                    );
                }
            }
        }
        if (!empty($data)) {
            $this->db->insert_batch('commission', $data);
        }
    }

    public function getemployeeearnings($where = array(), $type = "all")
    {
        $columns = "t1.*,ifnull(sum(t2.amount),0) as amount,0 as paid,ifnull(sum(t2.amount),0) - 0 as balance";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->group_by("t1.id");
        $this->db->from("employees t1");
        $this->db->join("commission t2", 't1.id=t2.emp_id', 'left');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function getmyearnings($where = array(), $type = "all")
    {
        $columns = "a.*,t2.name as service_name,t3.name,t3.mobile,t3.email";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('commission a');
        $this->db->join('purchases t1', 'a.order_id=t1.id');
        $this->db->join('services t2', 't1.service_id=t2.id', 'left');
        $this->db->join('customers t3', 't1.user_id=t3.id', 'left');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function getemployeebalance($emp_id)
    {
        $this->db->select_sum('amount');
        $this->db->where(array('emp_id' => $emp_id));
        $earnings = $this->db->get('commission')->unbuffered_row()->amount;
        $earnings = empty($earnings) ? 0 : $earnings;

        $this->db->select_sum('amount');
        $this->db->where(array('emp_id' => $emp_id));
        $payments = $this->db->get('payment')->unbuffered_row()->amount;
        $payments = empty($payments) ? 0 : $payments;

        $balance = $earnings - $payments;

        $result = array('earnings' => $earnings, 'payments' => $payments, 'balance' => $balance);
        return $result;
    }

    public function makepayment($data)
    {
        $this->db->trans_start();
        $data['added_on'] = $data['updated_on'] = date('Y-m-d H:i:s');
        if ($this->db->insert("payment", $data)) {
            $this->db->trans_complete();
            return array("status" => true, "message" => "Employee Payment Added Successfully!");
        } else {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function getemployeepayments($where = array(), $type = "all")
    {
        $columns = "t1.*,t2.name as emp_name,t2.mobile as emp_mobile,t2.email as emp_email";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('payment t1');
        $this->db->join('employees t2', 't1.emp_id=t2.id', 'left');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }
}
