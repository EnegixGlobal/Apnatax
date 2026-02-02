<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customers extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        checklogin();
    }

    public function index()
    {
        $data['title'] = "Customers";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['datatable'] = true;
        $where = array();
        if ($this->session->role != 'admin') {
            $where['md5(t1.added_by)'] = $this->session->user;
        }
        $data['customers'] = $this->customer->getcustomers($where);
        $this->template->load('customer', 'customers', $data);
    }

    public function addcustomer()
    {
        $data['title'] = "Add Customer";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['states'] = state_dropdown();

        $options = array('' => 'Select District');
        $data['districts'] = $options;



        $data['form'] = 'add';

        $this->template->load('customer', 'customerform', $data);
    }

    public function editcustomer($id = NULL)
    {
        $customer = $this->customer->getcustomers(['md5(t1.id)' => $id], 'single');
        if (empty($customer)) {
            redirect('customers/');
        }
        $data['customer'] = $customer;
        $data['title'] = "Edit customer";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['states'] = state_dropdown();

        $options = district_dropdown($customer['parent_id']);
        $data['districts'] = $options;


        $data['form'] = 'update';

        $this->template->load('customer', 'customerform', $data);
    }


    public function kycdetails($id = NULL)
    {
        $customer = $this->customer->getcustomers(['md5(t1.id)' => $id], 'single');
        if (empty($customer)) {
            redirect('customers/');
        }
        $data['customer'] = $customer;
        $data['title'] = "Customer KYC Details";
        $kyc = $this->account->getkyc(['t1.user_id' => $customer['user_id']], 'single');
        $data['kyc'] = $kyc;
        $data['form'] = 'update';

        //$this->debugger->printdata($kyc,true,true);
        $this->template->load('customer', 'kycdetails', $data);
    }

    public function uploadcertificates($id = NULL)
    {
        $customer = $this->customer->getcustomers(['md5(t1.id)' => $id], 'single');
        if (empty($customer)) {
            redirect('customers/');
        }

        if ($this->input->post('uploadcertificates') !== NULL) {
            $user = getuser();
            $data = array();
            $status = true;
            $message = array();
            $upload_path = './assets/images/profile/kyc/';
            $allowed_types = 'gif|jpg|jpeg|png|pdf';

            // Check if KYC record exists
            $kyc = $this->account->getkyc(['t1.user_id' => $customer['user_id']], 'single');
            if (empty($kyc)) {
                $this->session->set_flashdata("err_msg", "Please upload KYC details first!");
                redirect('customers/kycdetails/' . $id);
            }

            // Get existing KYC data with raw file paths (without file_url conversion)
            $existing_kyc = $this->db->select('tds_certificate, gst_certificate, audit_report, income_tax_certificate')
                ->where('user_id', $customer['user_id'])
                ->get('kyc')
                ->row_array();

            // Upload TDS Certificate
            if (isset($_FILES['tds_certificate']['tmp_name']) && !empty($_FILES['tds_certificate']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_kyc['tds_certificate']) && file_exists(FCPATH . $existing_kyc['tds_certificate'])) {
                    @unlink(FCPATH . $existing_kyc['tds_certificate']);
                }

                $upload = upload_file('tds_certificate', $upload_path, $allowed_types, generate_slug($customer['name'] . '-tds-certificate'));
                if ($upload['status'] === true) {
                    $data['tds_certificate'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "TDS Certificate- " . trim($upload['msg']);
                }
            }

            // Upload GST Certificate
            if (isset($_FILES['gst_certificate']['tmp_name']) && !empty($_FILES['gst_certificate']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_kyc['gst_certificate']) && file_exists(FCPATH . $existing_kyc['gst_certificate'])) {
                    @unlink(FCPATH . $existing_kyc['gst_certificate']);
                }

                $upload = upload_file('gst_certificate', $upload_path, $allowed_types, generate_slug($customer['name'] . '-gst-certificate'));
                if ($upload['status'] === true) {
                    $data['gst_certificate'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "GST Certificate- " . trim($upload['msg']);
                }
            }

            // Upload Audit Report
            if (isset($_FILES['audit_report']['tmp_name']) && !empty($_FILES['audit_report']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_kyc['audit_report']) && file_exists(FCPATH . $existing_kyc['audit_report'])) {
                    @unlink(FCPATH . $existing_kyc['audit_report']);
                }

                $upload = upload_file('audit_report', $upload_path, $allowed_types, generate_slug($customer['name'] . '-audit-report'));
                if ($upload['status'] === true) {
                    $data['audit_report'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "Audit Report- " . trim($upload['msg']);
                }
            }

            // Upload Income Tax Certificate
            if (isset($_FILES['income_tax_certificate']['tmp_name']) && !empty($_FILES['income_tax_certificate']['tmp_name'])) {
                // Delete old file if exists
                if (!empty($existing_kyc['income_tax_certificate']) && file_exists(FCPATH . $existing_kyc['income_tax_certificate'])) {
                    @unlink(FCPATH . $existing_kyc['income_tax_certificate']);
                }

                $upload = upload_file('income_tax_certificate', $upload_path, $allowed_types, generate_slug($customer['name'] . '-income-tax-certificate'));
                if ($upload['status'] === true) {
                    $data['income_tax_certificate'] = $upload['path'];
                } else {
                    $status = false;
                    $message[] = "Income Tax Certificate- " . trim($upload['msg']);
                }
            }

            if (!empty($data)) {
                $data['user_id'] = $customer['user_id'];
                $data['updated_on'] = date('Y-m-d H:i:s');
                $result = $this->account->savekyc($data);
                if ($result['status'] === true) {
                    $this->session->set_flashdata("msg", "Certificates uploaded successfully!");
                } else {
                    $this->session->set_flashdata("err_msg", $result['message']);
                }
            } else if (!$status) {
                $message = implode('; ', $message);
                $this->session->set_flashdata("err_msg", $message);
            } else {
                $this->session->set_flashdata("err_msg", "Please select at least one certificate to upload!");
            }
        }
        redirect('customers/kycdetails/' . $id);
    }

    public function delete_certificate($id = NULL, $type = '')
    {
        $customer = $this->customer->getcustomers(['md5(t1.id)' => $id], 'single');
        if (empty($customer)) {
            redirect('customers/');
        }

        $allowed_types = array('tds_certificate', 'gst_certificate', 'audit_report', 'income_tax_certificate');

        if (empty($type) || !in_array($type, $allowed_types)) {
            $this->session->set_flashdata("err_msg", "Invalid certificate type!");
            redirect('customers/kycdetails/' . $id);
        }

        // Get existing certificate file path
        $kyc = $this->db->select($type)
            ->where('user_id', $customer['user_id'])
            ->get('kyc')
            ->row_array();

        if (!empty($kyc) && !empty($kyc[$type])) {
            $file_path = $kyc[$type];
            $full_path = FCPATH . $file_path;

            // Delete file from server
            if (file_exists($full_path)) {
                @unlink($full_path);
            }

            // Update database - set certificate field to empty
            $update_data = array($type => '', 'updated_on' => date('Y-m-d H:i:s'));
            $result = $this->db->update('kyc', $update_data, array('user_id' => $customer['user_id']));

            if ($result) {
                $this->session->set_flashdata("msg", ucfirst(str_replace('_', ' ', $type)) . " deleted successfully!");
            } else {
                $this->session->set_flashdata("err_msg", "Failed to delete certificate!");
            }
        } else {
            $this->session->set_flashdata("err_msg", "Certificate not found!");
        }

        redirect('customers/kycdetails/' . $id);
    }

    public function customerpurchases()
    {
        $data['title'] = "Customer Purchases";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['datatable'] = true;
        $where = array();
        if ($this->session->role != 'admin') {
            $where['md5(t1.added_by)'] = $this->session->user;
        }
        $data['customers'] = customer_dropdown($where);
        $data['years'] = year_dropdown();
        $this->template->load('customer', 'customerpurchases', $data);
    }

    public function customerwisereport()
    {
        $data['title'] = "Customer Wise Report";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['datatable'] = true;
        $where = array();
        if ($this->session->role != 'admin') {
            $where['md5(t1.added_by)'] = $this->session->user;
        }
        $data['customers'] = customer_dropdown($where);
        $data['years'] = year_dropdown();
        $this->template->load('customer', 'customerwisereport', $data);
    }

    public function packageswitchrequests()
    {
        $data['title'] = "Customer Package Switch Requests";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['datatable'] = true;
        $where = array('t1.status' => 2);
        $data['customers'] = $this->customer->getcustomerpackages($where);
        $this->template->load('customer', 'packageswitchrequests', $data);
    }

    public function firmdeleterequests()
    {
        $data['title'] = "Firm Delete Requests";
        //$data['subtitle']="Sample Subtitle";
        $data['breadcrumb'] = array();
        $data['datatable'] = true;
        $where = array('t1.status' => 1, 't1.request' => 1);
        $data['customers'] = $this->customer->getfirms($where);
        $this->template->load('customer', 'firmdeleterequests', $data);
    }


    public function savecustomer()
    {
        if ($this->input->post('savecustomer') !== NULL) {
            $data = $this->input->post();
            unset($data['savecustomer']);
            $user = getuser();
            $data['added_by'] = $user['id'];
            $result = $this->customer->savecustomer($data);
            if ($result['status'] === true) {
                $this->session->set_flashdata("msg", $result['message']);
            } else {
                $this->session->set_flashdata("err_msg", $result['message']);
            }
            redirect('customers/addcustomer/');
        }
        if ($this->input->post('updatecustomer') !== NULL) {
            $data = $this->input->post();
            unset($data['updatecustomer']);
            $user = getuser();
            //print_pre($data,true);
            $result = $this->customer->updatecustomer($data);
            if ($result['status'] === true) {
                $this->session->set_flashdata("msg", $result['message']);
            } else {
                $this->session->set_flashdata("err_msg", $result['message']);
            }
            redirect('customers/');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function updatepackagerequest()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $getpackage = $this->db->get_where('customer_packages', ["md5(concat('customer-package-',id))" => $id]);
        if ($getpackage->num_rows() > 0) {
            $package = $getpackage->unbuffered_row('array');
            if ($status == 1) {
                $this->db->update('customer_packages', ['status' => 0], [
                    'user_id' => $package['user_id'],
                    'firm_id' => $package['firm_id']
                ]);
            }

            $result = $this->db->update('customer_packages', ['status' => $status], ['id' => $package['id']]);
            if ($result) {
                $this->session->set_flashdata("msg", "Package Switch Request Approved Successfully!");
            } else {
                $error = $this->db->error();
                $this->session->set_flashdata("err_msg", $error['message']);
            }
        }
    }

    public function updatefirmstatus()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $firm = $this->customer->getfirms(["md5(concat('firm-id-',t1.id))" => $id, 'request' => 1, 'status' => 1], 'single');
        if (!empty($firm)) {
            $message = $status == 1 ? "Firm Deleted Successfully" : "Firm Delete Request Rejected!";
            $request = $status == 1 ? 1 : 2;
            $status = $status == 1 ? 0 : 1;
            logupdateoperations('firms', ['status' => $status, 'request' => $request], ['id' => $firm['id']]);
            $result = $this->db->update('firms', ['status' => $status, 'request' => $request], ['id' => $firm['id']]);
            if ($result) {
                $this->session->set_flashdata("msg", $message);
            } else {
                $error = $this->db->error();
                $this->session->set_flashdata("err_msg", $error['message']);
            }
        }
    }

    public function getpurchases()
    {
        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        if (!empty($year)) {
            $years = getyearmonthvalues($year);
            $data['years'] = $years;
            $where = array('t1.user_id' => $user_id, 't1.date>=' => $years['year1'] . '-04-01', 't1.date<=' => $years['year2'] . '-03-31');
            $data['purchases'] = $this->service->getpurchases($where);
        }
        $data['services'] = $this->master->getservices();
        $this->load->view('customer/servicetable', $data);
    }

    public function getcustomerreport()
    {
        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        $where = array('t1.user_id' => $user_id);
        if (!empty($year)) {
            $where['t1.year'] = $year;
        }
        $data['orders'] = $this->service->getpurchases($where);
        $this->load->view('customer/orderlist', $data);
    }

    public function uploadolddata($id = NULL)
    {
        $customer = $this->customer->getcustomers(['md5(t1.id)' => $id], 'single');
        if (empty($customer)) {
            redirect('customers/');
        }
        $data['customer'] = $customer;
        $data['title'] = "Upload Old Data";
        $data['breadcrumb'] = array("customers/" => "Customers", "active" => "Upload Old Data");

        // Get all services
        $data['services'] = $this->master->getservices(array('status' => 1));

        // Get existing old data for this customer
        $where = array('t1.user_id' => $customer['user_id'], 't1.status' => 1);
        $data['old_data'] = $this->customer->getoldclientdata($where);

        $this->template->load('customer', 'uploadolddata', $data);
    }

    public function saveolddata()
    {
        if ($this->input->post('saveolddata') !== NULL) {
            $user = getuser();
            $data = $this->input->post();
            unset($data['saveolddata']);

            $customer = $this->customer->getcustomers(['md5(t1.id)' => $data['customer_id']], 'single');
            if (empty($customer)) {
                $this->session->set_flashdata("err_msg", "Customer not found!");
                redirect('customers/');
            }

            $data['user_id'] = $customer['user_id'];
            $data['uploaded_by'] = $user['id'];
            unset($data['customer_id']);

            // Handle file upload
            if (isset($_FILES['file']['tmp_name']) && !empty($_FILES['file']['tmp_name'])) {
                $upload_path = './assets/documents/old_data/';
                $allowed_types = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|zip|rar';
                $file_name = generate_slug($customer['name'] . '-' . $data['service_id'] . '-' . time());

                $upload = upload_file('file', $upload_path, $allowed_types, $file_name);
                if ($upload['status'] === true) {
                    $data['file_path'] = $upload['path'];
                    $data['file_name'] = $_FILES['file']['name'];
                    $data['file_type'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $data['file_size'] = $_FILES['file']['size'];

                    $result = $this->customer->saveoldclientdata($data);
                    if ($result['status'] === true) {
                        $this->session->set_flashdata("msg", $result['message']);
                    } else {
                        $this->session->set_flashdata("err_msg", $result['message']);
                    }
                } else {
                    $this->session->set_flashdata("err_msg", "File Upload Error: " . $upload['msg']);
                }
            } else {
                $this->session->set_flashdata("err_msg", "Please select a file to upload!");
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function deleteolddata($id = NULL)
    {
        if ($id === NULL) {
            redirect('customers/');
        }
        $old_data = $this->customer->getoldclientdata(['md5(t1.id)' => $id], 'single');
        if (empty($old_data)) {
            $this->session->set_flashdata("err_msg", "Data not found!");
            redirect('customers/');
        }

        $result = $this->customer->deleteoldclientdata($old_data['id']);
        if ($result['status'] === true) {
            $this->session->set_flashdata("msg", $result['message']);
        } else {
            $this->session->set_flashdata("err_msg", $result['message']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function downloadolddata($id = NULL)
    {
        if ($id === NULL) {
            redirect('customers/');
        }
        $old_data = $this->customer->getoldclientdata(['md5(t1.id)' => $id], 'single');
        if (empty($old_data)) {
            $this->session->set_flashdata("err_msg", "Data not found!");
            redirect('customers/');
        }

        $file_path = FCPATH . $old_data['file_path'];
        if (file_exists($file_path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $old_data['file_name'] . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            $this->session->set_flashdata("err_msg", "File not found!");
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
}
//url_title