<?php
defined('BASEPATH') or exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Common extends RestController
{
    function __construct()
    {
        parent::__construct();
        logrequest();
    }

    public function getservices_post()
    {
        $token = $this->post('token');
        if (!empty($token)) {
            $verify = $this->account->verify_token($token);
            if ($verify !== false) {
                $where = ['status' => 1];
                $services = $this->master->getservices($where);
                if (!empty($services)) {
                    foreach ($services as $key => $service) {
                        $services[$key]['types'] = explode(',', $service['type']);
                        $services[$key]['services_for'] = explode(',', $service['service_for']);
                    }
                    $this->response([
                        'status' => true,
                        'services' => $services
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "No Service Available!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "User Not Logged In!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function getpackages_post()
    {
        $packages = array(['id' => 1, 'name' => 'Accountancy Prime'], ['id' => 2, 'name' => 'Accountancy Premium']);
        if (!empty($packages)) {
            $this->response([
                'status' => true,
                'packages' => $packages
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "No Packages Found!"
            ], RestController::HTTP_OK);
        }
    }

    public function getpackagedetails_post()
    {
        $package_id = $this->post('package_id');
        if (!empty($package_id) && ($package_id == 1 || $package_id == 2)) {
            $name = $package_id == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
            $package = $this->master->getpackages(['name' => $name]);
            if (!empty($package)) {
                $this->response([
                    'status' => true,
                    'details' => $package
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "No Packages Found!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "No Packages Found!"
            ], RestController::HTTP_OK);
        }
    }

    public function getstates_post()
    {
        $states = $this->common->getstates();
        if (!empty($states)) {
            $this->response([
                'status' => true,
                'states' => $states
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "No States Found!"
            ], RestController::HTTP_OK);
        }
    }

    public function getdistricts_post()
    {
        $state_id = $this->post('state_id');
        if (!empty($state_id)) {
            $districts = $this->common->getdistricts($state_id);
            if (!empty($districts)) {
                $this->response([
                    'status' => true,
                    'districts' => $districts
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "No Districts Found!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide State ID!"
            ], RestController::HTTP_OK);
        }
    }

    public function getyears_post()
    {
        try {
            // Ensure helper is loaded
            $this->load->helper('common');

            $years = getyearly();
            if ($years === false || $years === null) {
                $years = array();
            }
            $this->response([
                'status' => true,
                'years' => $years
            ], RestController::HTTP_OK);
        } catch (Exception $e) {
            log_message('error', 'getyears_post error: ' . $e->getMessage());
            $this->response([
                'status' => false,
                'message' => 'Error loading years: ' . $e->getMessage()
            ], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function getquarters_post()
    {
        try {
            // Ensure helper is loaded
            $this->load->helper('common');

            $year = $this->post('year');
            $year = empty($year) ? date('Y') : $year;

            // Extract year from 8-digit format if needed (e.g., "20232024" -> "2023")
            if (strlen($year) == 8) {
                $year = substr($year, 0, 4);
            }

            $quarters = getquarterly($year);
            if ($quarters === false || $quarters === null) {
                $quarters = array();
            }
            $this->response([
                'status' => true,
                'quarters' => $quarters
            ], RestController::HTTP_OK);
        } catch (Exception $e) {
            log_message('error', 'getquarters_post error: ' . $e->getMessage());
            $this->response([
                'status' => false,
                'message' => 'Error loading quarters: ' . $e->getMessage()
            ], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function getmonths_post()
    {
        try {
            // Ensure helper is loaded
            $this->load->helper('common');

            $year = $this->post('year');
            $year = empty($year) ? date('Y') : $year;

            // Extract year from 8-digit format if needed (e.g., "20232024" -> "2023")
            if (strlen($year) == 8) {
                $year = substr($year, 0, 4);
            }

            $months = getmonths($year);
            if ($months === false || $months === null) {
                $months = array();
            }
            $this->response([
                'status' => true,
                'months' => $months
            ], RestController::HTTP_OK);
        } catch (Exception $e) {
            log_message('error', 'getmonths_post error: ' . $e->getMessage());
            $this->response([
                'status' => false,
                'message' => 'Error loading months: ' . $e->getMessage()
            ], RestController::HTTP_INTERNAL_ERROR);
        }
    }
}
