<?php
class Customer_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->db->db_debug = false;
    }

    public function savecustomer($data)
    {
        $this->db->trans_start();
        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            $userdata = array(
                'username' => $data['mobile'],
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'email' => $data['email']
            );
            $userdata['role'] = 'customer';
            // Use provided password, or default to mobile number if not provided
            $userdata['password'] = !empty($data['password']) ? $data['password'] : $data['mobile'];
            $userdata['status'] = 1; // Set status to active for bulk imported users
            $result = $this->account->register($userdata);
        } else {
            $result = array('status' => true, 'user_id' => $data['user_id'], 'old' => $data['old']);
            unset($data['old']);
        }
        //print_pre($result,true);
        if ($result['status'] === true && (empty($result['old']) || $result['old'] === false)) {
            $user_id = $result['user_id'];
            $data['user_id'] = $user_id;
            $datetime = date('Y-m-d H:i:s');
            $data['added_on'] = $data['updated_on'] = $datetime;
            // Remove password from customer data (it's stored in users table)
            unset($data['password']);
            if ($this->db->get_where('customers', array('mobile' => $data['mobile']))->num_rows() == 0) {
                if ($this->db->insert("customers", $data)) {
                    $customer_id = $this->db->insert_id();
                    $this->db->trans_complete();
                    return array("status" => true, "message" => "Customer Added Successfully!", 'customer_id' => $customer_id);
                } else {
                    $error = $this->db->error();
                    return array("status" => false, "message" => $error['message']);
                }
            } else {
                return array("status" => false, "message" => "Customer Already Added!");
            }
        } else {
            if (!empty($result['old'])) {
                $message = "Mobile no. Already Registered to a Customer!";
            } else {
                $message = $result['message'];
            }
            return array("status" => false, "message" => $message);
        }
    }

    public function getcustomers($where = array(), $type = "all")
    {
        $columns = "t1.*,t2.name as state_name,t3.name as district_name,t4.name as user_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('customers t1');
        $this->db->join('area t2', 't1.parent_id=t2.id', 'left');
        $this->db->join('area t3', 't1.area_id=t3.id', 'left');
        $this->db->join('users t4', 't1.added_by=t4.id', 'left');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function updatecustomer($data)
    {
        $datetime = date('Y-m-d H:i:s');
        $data['updated_on'] = $datetime;
        $id = $data['id'];
        unset($data['id']);
        $where = array("id" => $id);
        if ($this->db->get_where('customers', array('mobile' => $data['mobile'], "id!=" => $id))->num_rows() == 0) {
            if ($this->db->update("customers", $data, $where)) {
                return array("status" => true, "message" => "Customer Updated Successfully!");
            } else {
                $error = $this->db->error();
                return array("status" => false, "message" => $error['message']);
            }
        } else {
            return array("status" => false, "message" => "Customer Already Added!");
        }
    }

    public function saveaddress($data)
    {
        $datetime = date('Y-m-d H:i:s');
        $data['added_on'] = $data['updated_on'] = $datetime;
        if ($this->db->insert("addresses", $data)) {
            $address_id = $this->db->insert_id();
            return array("status" => true, "message" => "Address Added Successfully!", 'address_id' => $address_id);
        } else {
            $error = $this->db->error();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function getaddresses($where = array(), $type = "all")
    {
        $columns = "t1.*";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('addresses t1');
        $query = $this->db->get();
        
        // Check if query was successful
        if ($query === false) {
            return ($type == 'all') ? array() : array();
        }
        
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            if ($query->num_rows() > 0) {
                $array = $query->unbuffered_row('array');
            } else {
                $array = array();
            }
        }
        return $array;
    }

    public function addfirm($data)
    {
        $this->db->trans_start();
        $datetime = date('Y-m-d H:i:s');
        $data['added_on'] = $data['updated_on'] = $datetime;
        $where = "((name='$data[name]' and user_id='$data[user_id]')";
        if (!empty($data['gstin'])) {
            $where .= " or (gstin='$data[gstin]') ";
        }
        $where .= ") and request='0'";
        if ($this->db->get_where('firms', $where)->num_rows() == 0) {
            if ($this->db->insert("firms", $data)) {
                $firm_id = $this->db->insert_id();
                $this->db->trans_complete();
                return array("status" => true, "message" => "Firm Added Successfully!", 'firm_id' => $firm_id);
            } else {
                $error = $this->db->error();
                return array("status" => false, "message" => $error['message']);
            }
        } else {
            return array("status" => false, "message" => "Firm Already Added!");
        }
    }

    public function updatefirm($data)
    {
        if (empty($data['id'])) {
            return array("status" => false, "message" => "Firm ID is required!");
        }
        $id = $data['id'];
        unset($data['id']);
        $datetime = date('Y-m-d H:i:s');
        $data['updated_on'] = $datetime;

        // Check if firm exists and belongs to the user
        $firm = $this->db->get_where('firms', array('id' => $id))->row_array();
        if (empty($firm)) {
            return array("status" => false, "message" => "Firm not found!");
        }

        // Check for duplicate name or GSTIN (excluding current firm)
        $where = "((name='$data[name]' and user_id='$firm[user_id]')";
        if (!empty($data['gstin'])) {
            $where .= " or (gstin='$data[gstin]') ";
        }
        $where .= ") and request='0' and id!='$id'";
        if ($this->db->get_where('firms', $where)->num_rows() > 0) {
            return array("status" => false, "message" => "Firm with same name or GSTIN already exists!");
        }

        if ($this->db->update("firms", $data, array('id' => $id))) {
            return array("status" => true, "message" => "Firm Updated Successfully!");
        } else {
            $error = $this->db->error();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function getfirms($where = array(), $type = "all")
    {
        $columns = "t1.*,t2.name as customer_name,t2.mobile,t2.email";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('firms t1');
        $this->db->join('customers t2', 't1.user_id=t2.user_id');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function getcustomerpackages($where = array(), $type = "all")
    {
        $columns = "t2.name,t2.mobile,t2.email,t1.*, CASE WHEN t3.package_id=1 THEN 'Accountancy Prime' ELSE 'Accountancy Premium' END as current_package, CASE WHEN t1.package_id=1 THEN 'Accountancy Prime' ELSE 'Accountancy Premium' END as package";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('customer_packages t1');
        $this->db->join('customers t2', 't1.user_id=t2.user_id');
        $this->db->join('customer_packages t3', "t1.user_id=t3.user_id and t3.status='1'");
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function customerwithfirm($where = array(), $type = "all")
    {
        // Filter at the firm level: only include firms that have an active package (any year)
        $prefix  = $this->db->dbprefix;
        $columns = "t1.id as firm_id,t1.user_id,t1.name as firm_name,t2.name as customer_name";
        $this->db->select($columns);
        $this->db->where($where);
        // Use FALSE to prevent CI from escaping the raw EXISTS subquery
        $this->db->where("EXISTS (SELECT 1 FROM {$prefix}customer_packages cp WHERE cp.user_id = t1.user_id AND cp.firm_id = t1.id AND cp.status = '1')", NULL, FALSE);
        $this->db->from('firms t1');
        $this->db->join('customers t2', 't1.user_id=t2.user_id');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function createpackage($data)
    {
        $this->db->trans_start();
        $datetime = date('Y-m-d H:i:s');
        $data['added_on'] = $data['updated_on'] = $datetime;
        $where = array('user_id' => $data['user_id'], 'firm_id' => $data['firm_id'], 'year' => $data['year']);
        if ($this->db->get_where('service_packages', $where)->num_rows() == 0) {
            if ($this->db->insert("service_packages", $data)) {
                $firm_id = $this->db->insert_id();
                $this->db->trans_complete();
                return array("status" => true, "message" => "Package Created Successfully!");
            } else {
                $error = $this->db->error();
                return array("status" => false, "message" => $error['message']);
            }
        } else {
            unset($data['added_on']);
            if ($this->db->update("service_packages", $data, $where)) {
                if ($this->db->affected_rows() > 0) {
                    $message = "Package Updated Successfully!";
                } else {
                    $message = "No changes done in Package!";
                }
                $this->db->trans_complete();
                return array("status" => true, "message" => $message);
            } else {
                $error = $this->db->error();
                return array("status" => false, "message" => $error['message']);
            }
        }
    }

    public function getservicepackage($where = array(), $type = "all")
    {
        $columns = "t1.*,t2.name as customer_name,t3.name as firm_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('service_packages t1');
        $this->db->join('customers t2', 't1.user_id=t2.user_id');
        $this->db->join('firms t3', 't1.firm_id=t3.id', 'left');
        $query = $this->db->get();

        // Check if query failed
        if ($query === false) {
            $error = $this->db->error();
            log_message('error', 'getservicepackage query failed: ' . $error['message']);
            return $type == 'all' ? array() : null;
        }

        if ($type == 'all') {
            $array = $query->result_array();
            // Populate services for each package
            foreach ($array as $key => $package) {
                if (!empty($package['service_ids'])) {
                    $s_ids = explode(',', $package['service_ids']);
                    $s_ids = array_filter(array_map('trim', $s_ids));
                    if (!empty($s_ids)) {
                        $where_services = "status='1' and id in ('" . implode("','", $s_ids) . "')";
                        $array[$key]['services'] = $this->master->getservices($where_services);
                    } else {
                        $array[$key]['services'] = array();
                    }
                } else {
                    $array[$key]['services'] = array();
                }
            }
        } else {
            $array = $query->unbuffered_row('array');
            if (!empty($array)) {
                $s_ids = explode(',', $array['service_ids']);
                $s_ids = array_filter(array_map('trim', $s_ids));
                if (!empty($s_ids)) {
                    $where_services = "status='1' and id in ('" . implode("','", $s_ids) . "')";
                    $array['services'] = $this->master->getservices($where_services);
                } else {
                    $array['services'] = array();
                }
            }
        }
        return $array;
    }

    public function saveoldclientdata($data)
    {
        $datetime = date('Y-m-d H:i:s');
        $data['added_on'] = $data['updated_on'] = $datetime;
        if ($this->db->insert("old_client_data", $data)) {
            $id = $this->db->insert_id();
            return array("status" => true, "message" => "Old Data Uploaded Successfully!", 'id' => $id);
        } else {
            $error = $this->db->error();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function getoldclientdata($where = array(), $type = "all")
    {
        $columns = "t1.*,t2.name as service_name,t2.slug as service_slug,t3.name as customer_name,t3.mobile as customer_mobile,t4.name as uploaded_by_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('old_client_data t1');
        $this->db->join('services t2', 't1.service_id=t2.id', 'left');
        $this->db->join('customers t3', 't1.user_id=t3.user_id', 'left');
        $this->db->join('users t4', 't1.uploaded_by=t4.id', 'left');
        $this->db->order_by('t1.added_on', 'DESC');
        $query = $this->db->get();
        if ($type == 'all') {
            $array = $query->result_array();
        } else {
            $array = $query->unbuffered_row('array');
        }
        return $array;
    }

    public function updateoldclientdata($data)
    {
        $id = $data['id'];
        unset($data['id']);
        $where = array("id" => $id);
        $data['updated_on'] = date('Y-m-d H:i:s');
        if ($this->db->update("old_client_data", $data, $where)) {
            return array("status" => true, "message" => "Old Data Updated Successfully!");
        } else {
            $error = $this->db->error();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function deleteoldclientdata($id)
    {
        $where = array("id" => $id);
        $data = array('status' => 0, 'updated_on' => date('Y-m-d H:i:s'));
        if ($this->db->update("old_client_data", $data, $where)) {
            // Also delete the physical file
            $old_data = $this->getoldclientdata($where, 'single');
            if (!empty($old_data) && !empty($old_data['file_path'])) {
                $file_path = FCPATH . $old_data['file_path'];
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }
            return array("status" => true, "message" => "Old Data Deleted Successfully!");
        } else {
            $error = $this->db->error();
            return array("status" => false, "message" => $error['message']);
        }
    }

    public function bulkupdategst($enable = 1)
    {
        $datetime = date('Y-m-d H:i:s');
        $data = array(
            'gst_enabled' => $enable,
            'updated_on' => $datetime
        );

        // Update all customers
        if ($this->db->update("customers", $data)) {
            $affected_rows = $this->db->affected_rows();
            $message = $enable == 1
                ? "GST (18%) enabled successfully for all customers!"
                : "GST (18%) disabled successfully for all customers!";
            return array(
                "status" => true,
                "message" => $message,
                "affected_rows" => $affected_rows
            );
        } else {
            $error = $this->db->error();
            return array(
                "status" => false,
                "message" => $error['message'] ?: "Failed to update GST settings"
            );
        }
    }

    public function deletecustomer($customer_id)
    {
        // Enable error reporting temporarily to capture database errors
        $old_debug = $this->db->db_debug;
        $this->db->db_debug = TRUE;

        $this->db->trans_start();

        // Get customer data
        $customer = $this->getcustomers(array('t1.id' => $customer_id), 'single');
        if (empty($customer)) {
            $this->db->db_debug = $old_debug;
            return array("status" => false, "message" => "Customer not found!");
        }

        $user_id = $customer['user_id'];
        $deleted_data = array();

        // 1. Delete bank creditors statements files
        $bank_statement_ids = $this->db->select('id')->where('user_id', $user_id)->get('bank_statements')->result_array();
        $bank_statement_ids = array_column($bank_statement_ids, 'id');

        if (!empty($bank_statement_ids)) {
            $creditors_statements = $this->db->select('file_path')
                ->where_in('bank_statement_id', $bank_statement_ids)
                ->get('bank_creditors_statements')->result_array();

            foreach ($creditors_statements as $cred) {
                if (!empty($cred['file_path']) && file_exists(FCPATH . $cred['file_path'])) {
                    @unlink(FCPATH . $cred['file_path']);
                }
            }
            $this->db->where_in('bank_statement_id', $bank_statement_ids);
            $this->db->delete('bank_creditors_statements');
        }

        // 2. Delete bank statements files
        $bank_statements = $this->db->select('statement, creditors_statement')
            ->where('user_id', $user_id)
            ->get('bank_statements')->result_array();

        foreach ($bank_statements as $stmt) {
            if (!empty($stmt['statement']) && file_exists(FCPATH . $stmt['statement'])) {
                @unlink(FCPATH . $stmt['statement']);
            }
            if (!empty($stmt['creditors_statement']) && file_exists(FCPATH . $stmt['creditors_statement'])) {
                @unlink(FCPATH . $stmt['creditors_statement']);
            }
        }
        $this->db->delete('bank_statements', array('user_id' => $user_id));

        // 3. Delete KYC files
        $kyc = $this->db->where('user_id', $user_id)->get('kyc')->row_array();
        if (!empty($kyc)) {
            $kyc_files = array('pan_image', 'aadhar_image', 'aadhar_back', 'tds_certificate', 'gst_certificate', 'audit_report', 'income_tax_certificate');
            foreach ($kyc_files as $file_field) {
                if (!empty($kyc[$file_field]) && file_exists(FCPATH . $kyc[$file_field])) {
                    @unlink(FCPATH . $kyc[$file_field]);
                }
            }
            $this->db->delete('kyc', array('user_id' => $user_id));
        }

        // 4. Delete old client data files
        $old_data = $this->db->select('file_path')->where('user_id', $user_id)->get('old_client_data')->result_array();
        foreach ($old_data as $old) {
            if (!empty($old['file_path']) && file_exists(FCPATH . $old['file_path'])) {
                @unlink(FCPATH . $old['file_path']);
            }
        }
        $this->db->delete('old_client_data', array('user_id' => $user_id));

        // 5. Delete user photo if exists
        $user = $this->db->where('id', $user_id)->get('users')->row_array();
        if (!empty($user['photo']) && file_exists(FCPATH . $user['photo'])) {
            @unlink(FCPATH . $user['photo']);
        }

        // 6. Delete assessments, order_assign, commission, and documents (need to get purchase IDs first)
        $purchase_ids = $this->db->select('id')->where('user_id', $user_id)->get('purchases')->result_array();
        $purchase_ids = array_column($purchase_ids, 'id');
        if (!empty($purchase_ids)) {
            $this->db->where_in('order_id', $purchase_ids);
            $this->db->delete('assessments');

            $this->db->where_in('order_id', $purchase_ids);
            $this->db->delete('order_assign');

            $this->db->where_in('order_id', $purchase_ids);
            $this->db->delete('commission');

            // Delete documents linked to these orders
            $this->db->where_in('order_id', $purchase_ids);
            $this->db->delete('documents');
        }

        // 7. Delete related data from various tables
        $this->db->delete('accountancy', array('user_id' => $user_id));
        $this->db->delete('acc_payment', array('user_id' => $user_id));
        $this->db->delete('wallet', array('user_id' => $user_id));
        // Note: 'payment' table is for employee payments (uses emp_id), not customer payments, so skip it
        $this->db->delete('purchases', array('user_id' => $user_id));
        $this->db->delete('formdata', array('user_id' => $user_id));
        $this->db->delete('chats', array('sender_id' => $user_id));
        $this->db->delete('chats', array('receiver_id' => $user_id));

        // Delete addresses if table exists
        if ($this->db->table_exists('addresses')) {
            $this->db->delete('addresses', array('user_id' => $user_id));
        }

        $this->db->delete('customer_packages', array('user_id' => $user_id));
        $this->db->delete('service_packages', array('user_id' => $user_id));
        $this->db->delete('tokens', array('user_id' => $user_id));
        $this->db->delete('notify', array('user_id' => $user_id));

        // Delete security deposit if table exists
        $check_security = $this->db->query("SHOW TABLES LIKE 'security_deposit'");
        if ($check_security->num_rows() > 0) {
            $this->db->delete('security_deposit', array('user_id' => $user_id));
        }

        // 8. Delete firms (and any firm-related files if they exist)
        $firms = $this->db->where('user_id', $user_id)->get('firms')->result_array();
        $this->db->delete('firms', array('user_id' => $user_id));

        // 9. Delete customer record
        $this->db->delete('customers', array('id' => $customer_id));

        // 10. Delete user account
        $this->db->delete('users', array('id' => $user_id));

        $this->db->trans_complete();

        // Restore original debug setting
        $this->db->db_debug = $old_debug;

        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
            $error_message = 'Unknown database error occurred';
            $error_code = '';

            // Try to get error message
            if (!empty($error['message'])) {
                $error_message = $error['message'];
            } else {
                // Check last query error
                $last_error = $this->db->last_query();
                if ($last_error) {
                    $error_message = 'Database query failed. Please check logs for details.';
                }
            }

            if (!empty($error['code'])) {
                $error_code = ' (Error Code: ' . $error['code'] . ')';
            }

            // Log detailed error information
            log_message('error', 'Customer deletion failed for customer_id: ' . $customer_id . ', user_id: ' . $user_id);
            log_message('error', 'Database error: ' . $error_message . $error_code);
            log_message('error', 'Last query: ' . $this->db->last_query());

            return array("status" => false, "message" => "Failed to delete customer: " . $error_message . $error_code);
        }

        return array("status" => true, "message" => "Customer and all related data deleted successfully!");
    }
}
