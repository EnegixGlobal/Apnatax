<?php
defined('BASEPATH') or exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Profile extends RestController
{
    function __construct()
    {
        parent::__construct();
        logrequest();
    }

    public function getprofile_post()
    {
        $token = $this->post('token');
        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $firstLetter = !empty($user['name']) ? strtoupper(substr($user['name'], 0, 1)) : 'T';
                $photo = !empty($user['photo']) ? file_url($user['photo']) : base_url('profileimage/?letter=' . $firstLetter);

                // Get customer details
                $customer = $this->customer->getcustomers(['t1.user_id' => $user['id']], 'single');
                $address = $this->account->getaddress(['t1.user_id' => $user['id']], 'single');

                $result = array(
                    'name' => $user['name'],
                    'mobile' => $user['mobile'],
                    'email' => $user['email'],
                    'photo' => $photo,
                    'customer' => $customer,
                    'address' => $address
                );
                $this->response([
                    'status' => true,
                    'profile' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function updateprofile_post()
    {
        $token = $this->post('token');
        $name = $this->post('name');
        $mobile = $this->post('mobile');
        $email = $this->post('email');

        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array();

                // Handle photo upload (optional)
                if (!empty($_FILES['photo']) && isset($_FILES['photo']['tmp_name']) && !empty($_FILES['photo']['tmp_name'])) {
                    $upload_path = './assets/images/profile/';
                    $allowed_types = 'gif|jpg|jpeg|png|svg';
                    $upload = upload_file('photo', $upload_path, $allowed_types, generate_slug($user['name']));
                    if ($upload['status'] === true) {
                        $this->load->library('imager');
                        $path = $this->imager->processimage('.' . $upload['path'], 'cropscale', 80, ['width' => 300, 'height' => 300]);
                        $data['photo'] = $path;
                    }
                }

                if (!empty($name)) {
                    $data['name'] = $name;
                }
                if (!empty($email)) {
                    $data['email'] = $email;
                }
                if (!empty($mobile)) {
                    $data['mobile'] = $mobile;
                }
                if (!empty($data)) {
                    $result = $this->account->updateuser($data, array("id" => $user['id']));
                    if ($result['status'] === true) {
                        $data['name'] = isset($data['name']) ? $data['name'] : $user['name'];
                        $data['email'] = isset($data['email']) ? $data['email'] : $user['email'];
                        $data['mobile'] = isset($data['mobile']) ? $data['mobile'] : $user['mobile'];
                        $data['photo'] = isset($data['photo']) ? file_url($data['photo']) : file_url($user['photo']);
                        $this->response([
                            'status' => true,
                            'message' => $result['message'],
                            'profile' => $data
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => $result['message']
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Please Try Again!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function saveaddress_post()
    {
        $token = $this->post('token');
        $address = $this->post('address');
        $state_id = $this->post('state_id');
        $district_id = $this->post('district_id');
        $pincode = $this->post('pincode');

        if (!empty($token) && !empty($address) && !empty($state_id) && !empty($district_id) && !empty($pincode)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array(
                    "user_id" => $user['id'],
                    "address" => $address,
                    "parent_id" => $state_id,
                    "area_id" => $district_id,
                    "pincode" => $pincode
                );
                $getstate = $this->db->get_where("area", array("id" => $data['parent_id'], 'type' => 'State'));
                $getdistrict = $this->db->get_where("area", array("id" => $data['area_id'], "parent_id" => $data['parent_id'], 'type' => 'District'));
                if ($getdistrict->num_rows() == 1 && $getstate->num_rows() == 1) {
                    $data['state'] = $getstate->unbuffered_row()->name;
                    $data['district'] = $getdistrict->unbuffered_row()->name;

                    //print_pre($data,true);
                    $result = $this->account->saveaddress($data);
                    if ($result['status'] === true) {
                        $this->response([
                            'status' => true,
                            'message' => $result['message']
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => $result['message']
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $message = $getstate->num_rows() == 0 ? 'State' : 'District';
                    $message .= " not found!";
                    $this->response([
                        'status' => false,
                        'message' => $message
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function getaddress_post()
    {
        $token = $this->post('token');
        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $address = $this->account->getaddress(['t1.user_id' => $user['id']], 'single');
                if (!empty($address)) {
                    $this->response([
                        'status' => true,
                        'address' => $address
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Address not added!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function savekyc_post()
    {
        $token = $this->post('token');
        $pan = $this->post('pan');
        $aadhar = $this->post('aadhar');

        if (!empty($token) && !empty($pan) && !empty($aadhar)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $checkpan = preg_match('/^[A-Z]{5}\d{4}[A-Z]$/', $pan);
                $checkaadhar = preg_match('/[0-9]{12}$/', $aadhar);
                if ($checkaadhar && $checkpan) {
                    $data = array("user_id" => $user['id'], "pan" => $pan, "aadhar" => $aadhar);
                    $status = true;
                    $message = array();
                    $upload_path = './assets/images/profile/kyc/';
                    $allowed_types = 'gif|jpg|jpeg|png|svg';
                    $upload = upload_file('pan_image', $upload_path, $allowed_types, generate_slug($user['name'] . '-pan'));
                    if ($upload['status'] === true) {
                        $data['pan_image'] = $upload['path'];
                    } else {
                        $status = false;
                        $message[] = "PAN- " . trim($upload['msg']);
                    }
                    $upload = upload_file('aadhar_image', $upload_path, $allowed_types, generate_slug($user['name'] . '-aadhar'));
                    if ($upload['status'] === true) {
                        $data['aadhar_image'] = $upload['path'];
                    } else {
                        $status = false;
                        $message[] = "Aadhar Front- " . trim($upload['msg']);
                    }

                    $upload = upload_file('aadhar_back', $upload_path, $allowed_types, generate_slug($user['name'] . '-aadhar-back'));
                    if ($upload['status'] === true) {
                        $data['aadhar_back'] = $upload['path'];
                    } else {
                        $status = false;
                        $message[] = "Aadhar Back- " . trim($upload['msg']);
                    }

                    if ($status) {
                        $result = $this->account->savekyc($data);
                        if ($result['status'] === true) {
                            $this->response([
                                'status' => true,
                                'message' => $result['message']
                            ], RestController::HTTP_OK);
                        } else {
                            $this->response([
                                'status' => false,
                                'message' => $result['message']
                            ], RestController::HTTP_OK);
                        }
                    } else {
                        $message = implode('; ', $message);
                        $this->response([
                            'status' => false,
                            'message' => $message
                        ], RestController::HTTP_OK);
                    }
                } else {
                    if ($checkaadhar && !$checkpan) {
                        $message = "Enter Valid PAN!";
                    } elseif (!$checkaadhar && $checkpan) {
                        $message = "Enter Valid Aadhar No!";
                    } else {
                        $message = "Enter Valid PAN and Aadhar No!";
                    }
                    $this->response([
                        'status' => false,
                        'message' => $message
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function getkyc_post()
    {
        $token = $this->post('token');
        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $kyc = $this->account->getkyc(['t1.user_id' => $user['id']], 'single');
                if (!empty($kyc)) {
                    $this->response([
                        'status' => true,
                        'kyc' => $kyc
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "KYC not Uploaded!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function addfirm_post()
    {
        $token = $this->post('token');
        $name = trim($this->post('name'));
        $gstin = trim($this->post('gstin'));
        $id = $this->post('id'); // For update operations

        // Validate required fields
        if (empty($token)) {
            $this->response([
                'status' => false,
                'message' => "Token is required!"
            ], RestController::HTTP_OK);
            return;
        }

        if (empty($name)) {
            $this->response([
                'status' => false,
                'message' => "Firm name is required!"
            ], RestController::HTTP_OK);
            return;
        }

        if (!empty($token) && !empty($name)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                // Check if this is an update operation
                if (!empty($id)) {
                    // Verify that the firm belongs to the user
                    $where = array("t1.id" => $id, "t1.user_id" => $user['id']);
                    $existingFirm = $this->customer->getfirms($where, 'single');
                    if (empty($existingFirm)) {
                        $this->response([
                            'status' => false,
                            'message' => "Firm not found or unauthorized!"
                        ], RestController::HTTP_OK);
                        return;
                    }

                    // Update firm
                    $data = array("id" => $id, "name" => $name, "gstin" => $gstin);
                    $result = $this->customer->updatefirm($data);
                    if ($result['status'] === true) {
                        // Get the updated firm
                        $where = array("t1.id" => $id, "t1.user_id" => $user['id']);
                        $firm = $this->customer->getfirms($where, 'single');
                        $this->response([
                            'status' => true,
                            'message' => $result['message'],
                            'response' => $firm
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => $result['message']
                        ], RestController::HTTP_OK);
                    }
                } else {
                    // Add new firm
                    $data = array("user_id" => $user['id'], "name" => $name, "gstin" => $gstin);
                    $result = $this->customer->addfirm($data);
                    if ($result['status'] === true) {
                        // Get the newly created firm to return it using the firm_id from result
                        $firm_id = isset($result['firm_id']) ? $result['firm_id'] : null;
                        if ($firm_id) {
                            $where = array("t1.id" => $firm_id, "t1.user_id" => $user['id']);
                        } else {
                            // Fallback: get by name and user_id (most recent)
                            $where = array("t1.user_id" => $user['id'], "t1.name" => $name, "t1.request" => 0);
                        }
                        $firm = $this->customer->getfirms($where, 'single');

                        $this->response([
                            'status' => true,
                            'message' => $result['message'],
                            'response' => $firm
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => $result['message']
                        ], RestController::HTTP_OK);
                    }
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details! (Token and Firm Name are required)"
            ], RestController::HTTP_OK);
        }
    }

    public function myfirms_post()
    {
        $token = $this->post('token');

        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.status' => 1, 't1.request!=' => 1);
                $firms = $this->customer->getfirms($data);
                if (!empty($firms)) {
                    // Add can_request_delete: false if firm has package or purchases (matches web checkfirmservice)
                    foreach ($firms as &$firm) {
                        $firm['can_request_delete'] = !checkfirmservice($user, $firm['id']);
                    }
                    unset($firm);
                    $this->response([
                        'status' => true,
                        'response' => $firms,
                        'message' => 'Firms retrieved successfully'
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'response' => [],
                        'message' => "No Firm Added!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'response' => [],
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'response' => [],
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function deletefirm_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');

        if (!empty($token) && !empty($firm_id)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.id' => $firm_id);
                $firm = $this->customer->getfirms($data, 'single');
                if (!empty($firm)) {
                    if (checkfirmservice($user, $firm['id'])) {
                        $this->response([
                            'status' => false,
                            'message' => "Cannot request deletion - firm has active package or purchases!"
                        ], RestController::HTTP_OK);
                        return;
                    }
                    if ($firm['request'] == 0) {
                        $this->db->update('firms', ['request' => 1], ['id' => $firm['id']]);
                        $this->response([
                            'status' => true,
                            'message' => "Firm delete request saved successfully!"
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "Delete Request already saved!"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Added!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function createpackage_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');
        $service_ids = $this->post('service_ids');
        $service_option_ids = $this->post('service_option_ids'); // JSON format: {"service_id": ["option_id1", "option_id2"], ...}
        $year = $this->post('year');
        if (!empty($token) && !empty($service_ids) && !empty($year) && !empty($firm_id)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.id' => $firm_id);
                $firm = $this->customer->getfirms($data, 'single');
                if (!empty($firm)) {
                    $s_ids = explode(',', $service_ids);
                    $where = "status='1' and id in ('" . implode("','", $s_ids) . "')";
                    $services = $this->master->getservices($where);
                    if (!empty($services)) {
                        // Process service options - convert to JSON if provided
                        $service_option_ids_json = NULL;
                        if (!empty($service_option_ids)) {
                            // If it's already a JSON string, validate it; otherwise try to decode
                            if (is_string($service_option_ids)) {
                                $decoded = json_decode($service_option_ids, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $service_option_ids_json = $service_option_ids;
                                } else {
                                    // If not valid JSON, try to parse as array
                                    $service_option_ids_json = json_encode($service_option_ids);
                                }
                            } else {
                                $service_option_ids_json = json_encode($service_option_ids);
                            }
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
                            // Generate invoice for service package creation
                            $invoice_no = '';
                            $invoice_id = 0;
                            try {
                                // Calculate total from service rates
                                $subtotal = 0;
                                $service_names = [];
                                foreach ($services as $svc) {
                                    if (!empty($svc['rate']) && is_numeric($svc['rate'])) {
                                        $subtotal += floatval($svc['rate']);
                                    }
                                    $service_names[] = $svc['name'];
                                }
                                // Only generate invoice if there is an amount
                                if ($subtotal > 0) {
                                    $this->load->model('Customer_model', 'customer_inv');
                                    $customer = $this->customer->getcustomers(['t1.user_id' => $user['id']], 'single');
                                    $gst_enabled = !empty($customer) && !empty($customer['gst_enabled']) && $customer['gst_enabled'] == 1;
                                    $gst_amount  = $gst_enabled ? round(($subtotal * 18) / 100, 2) : 0;
                                    $total_amount = $subtotal + $gst_amount;

                                    $this->load->model('Invoice_model', 'invoice_model');
                                    $inv_data = [
                                        'user_id'        => $user['id'],
                                        'firm_id'        => $firm_id,
                                        'year'           => $year,
                                        'invoice_date'   => date('Y-m-d'),
                                        'billing_name'   => $user['name'],
                                        'billing_email'  => !empty($user['email']) ? $user['email'] : '',
                                        'billing_mobile' => !empty($user['mobile']) ? $user['mobile'] : '',
                                        'firm_name'      => !empty($firm['name']) ? $firm['name'] : '',
                                        'firm_gstin'     => !empty($firm['gstin']) ? $firm['gstin'] : '',
                                        'service_name'   => implode(', ', $service_names),
                                        'type'           => 'Yearly',
                                        'period'         => $year,
                                        'subtotal'       => $subtotal,
                                        'gst_rate'       => $gst_enabled ? 18 : 0,
                                        'gst_amount'     => $gst_amount,
                                        'total_amount'   => $total_amount,
                                    ];
                                    $inv_result = $this->invoice_model->create_custom_invoice($inv_data);
                                    if ($inv_result['status'] && !empty($inv_result['invoice'])) {
                                        $invoice_no = $inv_result['invoice']['invoice_no'];
                                        $invoice_id = (int)$inv_result['invoice']['id'];
                                    }
                                }
                            } catch (Exception $e) {
                                // Invoice generation failure should not block package save
                            }
                            $this->response([
                                'status'     => true,
                                'message'    => $result['message'],
                                'invoice_no' => $invoice_no,
                                'invoice_id' => $invoice_id,
                            ], RestController::HTTP_OK);
                        } else {
                            $this->response([
                                'status' => false,
                                'message' => $result['message']
                            ], RestController::HTTP_OK);
                        }
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "Service not Available"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "User not Logged In!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function myservicepackage_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');
        $year = $this->post('year');

        if (!empty($token) && !empty($firm_id) && !empty($year)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.id' => $firm_id);
                $firm = $this->customer->getfirms($data, 'single');
                if (!empty($firm)) {
                    $data = array("t1.user_id" => $user['id'], 't1.firm_id' => $firm_id, 't1.year' => $year);
                    $package = $this->customer->getservicepackage($data, 'single');
                    if (!empty($package)) {
                        $this->response([
                            'status' => true,
                            'package' => $package
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "Package not Created!"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function requestpackagedelete_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');
        $year = $this->post('year');

        if (!empty($token) && !empty($firm_id) && !empty($year)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                // Check if request column exists
                $check_column = $this->db->query("SHOW COLUMNS FROM `tf_service_packages` LIKE 'request'");
                if ($check_column->num_rows() == 0) {
                    $this->response([
                        'status' => false,
                        'message' => "Delete request feature is not available. Please contact administrator."
                    ], RestController::HTTP_OK);
                    return;
                }

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
                            $this->response([
                                'status' => true,
                                'message' => $message
                            ], RestController::HTTP_OK);
                        } else {
                            $this->response([
                                'status' => false,
                                'message' => "Failed to save delete request!"
                            ], RestController::HTTP_OK);
                        }
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "Delete Request already submitted!"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Package not found!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function checkaccountancy_post()
    {
        $token = $this->post('token');
        $year = $this->post('year');
        $firm_id = $this->post('firm_id');
        $year = $this->post('year');

        if (!empty($token) && !empty($firm_id) && !empty($year)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.id' => $firm_id);
                $firm = $this->customer->getfirms($data, 'single');
                if (!empty($firm)) {
                    $data = array("t1.user_id" => $user['id']);
                    $status = checkaccountancy($user, $firm_id);
                    $this->response([
                        'status' => true,
                        'accountancy' => $status
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function savemonthlystatement_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');
        $year = $this->post('year');
        $month = $this->post('month');

        if (!empty($token) && !empty($firm_id) && !empty($year) && !empty($month)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.id' => $firm_id);
                $firm = $this->customer->getfirms($data, 'single');
                if (!empty($firm)) {
                    $data = array("t1.user_id" => $user['id']);
                    $status = checkaccountancy($user, $firm_id);
                    if ($status) {
                        $months = getmonths($year);
                        if (!empty($months)) {
                            $month_ids = array_column($months, 'id');
                            $index = array_search($month, $month_ids);
                            if ($index !== false) {
                                $month_name = $months[$index]['value'];
                                $where = array('user_id' => $user['id'], 'firm_id' => $firm_id, 'year' => $year, 'month' => $month);
                                $check = $this->db->get_where('bank_statements', $where)->num_rows();
                                if ($check == 0) {
                                    $data = $where;
                                    $upload_path = './assets/documents/statements/';
                                    $allowed_types = 'pdf';
                                    $upload = upload_file('statement', $upload_path, $allowed_types, generate_slug($user['name'] . '-bank-statement-' . $month));
                                    if ($upload['status'] === true) {
                                        $data['statement'] = $upload['path'];
                                        $data['uploaded_by'] = $user['id'];

                                        // Save bank statement first to get the ID
                                        $result = $this->service->savebankstatement($data);

                                        if ($result['status'] === true) {
                                            $bank_statement_id = $result['bank_statement_id'];

                                            // Handle multiple creditors statement uploads
                                            $creditors_upload_errors = array();
                                            $creditors_upload_success = 0;

                                            if (!empty($_FILES['creditors_statement']['name'][0])) {
                                                $file_count = count($_FILES['creditors_statement']['name']);

                                                for ($i = 0; $i < $file_count; $i++) {
                                                    // Create a temporary single file array for upload_file function
                                                    $_FILES['creditors_statement_single'] = array(
                                                        'name' => $_FILES['creditors_statement']['name'][$i],
                                                        'type' => $_FILES['creditors_statement']['type'][$i],
                                                        'tmp_name' => $_FILES['creditors_statement']['tmp_name'][$i],
                                                        'error' => $_FILES['creditors_statement']['error'][$i],
                                                        'size' => $_FILES['creditors_statement']['size'][$i]
                                                    );

                                                    $upload = upload_file(
                                                        'creditors_statement_single',
                                                        $upload_path,
                                                        $allowed_types,
                                                        generate_slug($user['name'] . '-creditors-statement-' . $month . '-' . ($i + 1))
                                                    );

                                                    if ($upload['status'] === true) {
                                                        // Save to bank_creditors_statements table
                                                        $creditors_data = array(
                                                            'bank_statement_id' => $bank_statement_id,
                                                            'user_id' => $user['id'],
                                                            'firm_id' => $firm_id,
                                                            'file_path' => $upload['path'],
                                                            'file_name' => $_FILES['creditors_statement']['name'][$i],
                                                            'uploaded_by' => $user['id'],
                                                            'added_on' => date('Y-m-d H:i:s'),
                                                            'updated_on' => date('Y-m-d H:i:s')
                                                        );

                                                        if ($this->db->insert('bank_creditors_statements', $creditors_data)) {
                                                            $creditors_upload_success++;
                                                        } else {
                                                            $creditors_upload_errors[] = 'Failed to save creditors statement file: ' . $_FILES['creditors_statement']['name'][$i];
                                                        }
                                                    } else {
                                                        $creditors_upload_errors[] = 'Creditors Statement ' . ($i + 1) . ': ' . $upload['msg'];
                                                    }

                                                    // Clean up temporary file array
                                                    unset($_FILES['creditors_statement_single']);
                                                }

                                                // Set success/error messages
                                                if ($creditors_upload_success > 0) {
                                                    $msg = $result['message'];
                                                    if ($creditors_upload_success > 1) {
                                                        $msg .= " " . $creditors_upload_success . " creditors statements uploaded successfully.";
                                                    } else {
                                                        $msg .= " Creditors statement uploaded successfully.";
                                                    }
                                                    if (!empty($creditors_upload_errors)) {
                                                        $msg .= " Errors: " . implode('; ', $creditors_upload_errors);
                                                    }
                                                    $this->response([
                                                        'status' => true,
                                                        'message' => $msg
                                                    ], RestController::HTTP_OK);
                                                } else if (!empty($creditors_upload_errors)) {
                                                    $this->response([
                                                        'status' => true,
                                                        'message' => $result['message'] . " Creditors statements failed: " . implode('; ', $creditors_upload_errors)
                                                    ], RestController::HTTP_OK);
                                                } else {
                                                    $this->response([
                                                        'status' => true,
                                                        'message' => $result['message']
                                                    ], RestController::HTTP_OK);
                                                }
                                            } else {
                                                // No creditors statements uploaded, but bank statement is saved
                                                $this->response([
                                                    'status' => true,
                                                    'message' => $result['message']
                                                ], RestController::HTTP_OK);
                                            }
                                        } else {
                                            $this->response([
                                                'status' => false,
                                                'message' => $result['message']
                                            ], RestController::HTTP_OK);
                                        }
                                    } else {
                                        $this->response([
                                            'status' => false,
                                            'message' => $upload['msg']
                                        ], RestController::HTTP_OK);
                                    }
                                } else {
                                    $this->response([
                                        'status' => false,
                                        'message' => "Statement Already uploaded for " . $month_name
                                    ], RestController::HTTP_OK);
                                }
                            } else {
                                $this->response([
                                    'status' => false,
                                    'message' => "Select Valid Month!"
                                ], RestController::HTTP_OK);
                            }
                        } else {
                            $this->response([
                                'status' => false,
                                'message' => "Select Valid Year!"
                            ], RestController::HTTP_OK);
                        }
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "Accountancy Service not Active"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function getbankstatements_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');
        $year = $this->post('year');

        if (!empty($token) && !empty($firm_id) && !empty($year)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $data = array("t1.user_id" => $user['id'], 't1.id' => $firm_id);
                $firm = $this->customer->getfirms($data, 'single');
                if (!empty($firm)) {
                    $where = array("t1.user_id" => $user['id'], 't1.firm_id' => $firm_id);
                    $statements = $this->service->getbankstatements($where, 'all', 't1.month desc');

                    if (!empty($statements)) {
                        foreach ($statements as $key => $single) {
                            $year = getyearmonthvalues($single['year']);
                            $month = getyearmonthvalues($single['month']);
                            $statements[$key]['year_value'] = $year['value'];
                            $statements[$key]['month_value'] = $month['value'];
                            $statements[$key]['statement'] = file_url($single['statement']);

                            // Get multiple creditors statements for this bank statement
                            $creditors_statements = $this->db->get_where('bank_creditors_statements', array('bank_statement_id' => $single['id']))->result_array();
                            $statements[$key]['creditors_statements'] = array();
                            if (!empty($creditors_statements)) {
                                foreach ($creditors_statements as $cred) {
                                    $statements[$key]['creditors_statements'][] = array(
                                        'id' => $cred['id'],
                                        'file_path' => file_url($cred['file_path']),
                                        'file_name' => $cred['file_name']
                                    );
                                }
                            }

                            // Keep backward compatibility with old single creditors_statement field
                            if (!empty($single['creditors_statement'])) {
                                $statements[$key]['creditors_statement'] = file_url($single['creditors_statement']);
                            }
                        }
                        $this->response([
                            'status' => true,
                            'response' => $statements
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "Bank Statements not Uploaded!"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not Selected!"
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Unauthorized Access!"
                ], RestController::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Please provide all Details!"
            ], RestController::HTTP_OK);
        }
    }

    public function getcertificates_post()
    {
        $token = $this->post('token');
        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $kyc = $this->account->getkyc(['t1.user_id' => $user['id']], 'single');
                if (!empty($kyc)) {
                    $certificates = array();
                    $allowed_types = array('tds_certificate', 'gst_certificate', 'audit_report', 'income_tax_certificate');
                    foreach ($allowed_types as $type) {
                        if (!empty($kyc[$type])) {
                            $certificates[] = array(
                                'type' => $type,
                                'name' => ucfirst(str_replace('_', ' ', $type)),
                                'download_url' => file_url($kyc[$type]),
                                'file_name' => basename($kyc[$type])
                            );
                        }
                    }
                    $this->response([
                        'status' => true,
                        'certificates' => $certificates,
                        'response' => $certificates
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "KYC not Uploaded!"
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

    public function getolddata_post()
    {
        $token = $this->post('token');
        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $where = array('t1.user_id' => $user['id'], 't1.status' => 1);
                $old_data = $this->customer->getoldclientdata($where);
                if (!empty($old_data)) {
                    foreach ($old_data as $key => $data) {
                        $old_data[$key]['download_url'] = file_url($data['file_path']);
                    }
                    $this->response([
                        'status' => true,
                        'old_data' => $old_data
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "No Old Data Available!"
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

    public function savesessdata_post()
    {
        $token = $this->post('token');
        $year = trim($this->post('year'));
        $firm = trim($this->post('firm'));

        // Validate required fields with specific error messages
        if (empty($token)) {
            $this->response([
                'status' => false,
                'message' => "Token is required!"
            ], RestController::HTTP_OK);
            return;
        }

        if (empty($year)) {
            $this->response([
                'status' => false,
                'message' => "Year is required!"
            ], RestController::HTTP_OK);
            return;
        }

        if (empty($firm)) {
            $this->response([
                'status' => false,
                'message' => "Firm is required!"
            ], RestController::HTTP_OK);
            return;
        }

        if (!empty($token) && !empty($year) && !empty($firm)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $where = array("t1.user_id" => $user['id'], 't1.status' => 1, 't1.request!=' => 1, 't1.id' => $firm);
                $firmData = $this->customer->getfirms($where, 'single');
                if (!empty($firmData)) {
                    // For mobile app, we return the selected year and firm data
                    // The app will store it locally
                    $this->response([
                        'status' => true,
                        'message' => 'Session data saved successfully',
                        'year' => $year,
                        'firm_id' => $firmData['id'],
                        'firm' => $firmData
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Firm not found or not authorized!"
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
                'message' => "Please provide all Details (token, year, firm)!"
            ], RestController::HTTP_OK);
        }
    }
}
