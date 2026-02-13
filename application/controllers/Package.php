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
        $data['service_package'] = $this->customer->getservicepackage([
            't1.user_id' => $user['id'],
            't1.firm_id' => $firm_id,
            't1.year' => $year
        ], 'single');

        // Check if package has any purchases (orders) - if yes, make it read-only
        $data['has_purchases'] = false;
        if (!empty($data['service_package']) && !empty($data['service_package']['service_ids'])) {
            $package_service_ids = explode(',', $data['service_package']['service_ids']);
            if (!empty($package_service_ids)) {
                $service_ids_str = implode(',', array_map('intval', $package_service_ids));
                $where_purchases = "t1.user_id='$user[id]' AND t1.firm_id='$firm_id' AND t1.year='$year' AND t1.service_id IN ($service_ids_str)";
                $purchases = $this->service->getpurchases($where_purchases);
                if (!empty($purchases)) {
                    $data['has_purchases'] = true;
                }
            }
        }

        $this->template->load('package', 'mypackage', $data);
    }

    public function savepackage()
    {
        if ($this->input->post('savepackage') !== NULL) {
            $data = $this->input->post();
            $user = getuser();
            $firm_id = $this->session->firm;
            $year = $this->session->year;

            // Check if package has purchases - if yes, ensure existing purchased services are preserved
            $service_package = $this->customer->getservicepackage([
                't1.user_id' => $user['id'],
                't1.firm_id' => $firm_id,
                't1.year' => $year
            ], 'single');

            $existing_purchased_service_ids = array();
            if (!empty($service_package) && !empty($service_package['service_ids'])) {
                $package_service_ids = explode(',', $service_package['service_ids']);
                if (!empty($package_service_ids)) {
                    $service_ids_str = implode(',', array_map('intval', $package_service_ids));
                    $where_purchases = "t1.user_id='$user[id]' AND t1.firm_id='$firm_id' AND t1.year='$year' AND t1.service_id IN ($service_ids_str)";
                    $purchases = $this->service->getpurchases($where_purchases);
                    if (!empty($purchases)) {
                        // Get service IDs that have been purchased
                        $existing_purchased_service_ids = array_unique(array_column($purchases, 'service_id'));
                    }
                }
            }

            $service_id = $data['service_id'];

            // Filter out empty values from service_id array
            $service_id = array_filter($service_id, function ($value) {
                return !empty($value) && trim($value) !== '';
            });

            // If there are purchased services, ensure they are included in the update
            if (!empty($existing_purchased_service_ids)) {
                // Merge existing purchased services with new services
                $service_id = array_merge($existing_purchased_service_ids, $service_id);
            }

            if (empty($service_id)) {
                $this->session->set_flashdata('err_msg', 'Please select at least one service!');
                redirect($_SERVER['HTTP_REFERER']);
                return;
            }

            $service_ids = implode(',', $service_id);
            $service_ids = trim($service_ids);
            $service_ids = trim($service_ids, ',');
            //print_pre($service_ids,true);
            $where = array("t1.user_id" => $user['id'], 't1.id' => $firm_id, 't1.status' => 1);
            $firm = $this->customer->getfirms($where, 'single');
            if (!empty($firm)) {
                $s_ids = explode(',', $service_ids);
                // Remove duplicates
                $s_ids = array_unique($s_ids);
                $service_ids = implode(',', $s_ids);
                $where = "status='1' and id in ('" . implode("','", $s_ids) . "')";
                $services = $this->master->getservices($where);
                if (!empty($services)) {
                    $data = array('user_id' => $user['id'], 'firm_id' => $firm_id, 'year' => $year, 'service_ids' => $service_ids);
                    $result = $this->customer->createpackage($data);
                    if ($result['status'] === true) {
                        $this->session->set_flashdata('msg', $result['message']);
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
}
