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
    }

    public function index()
    {
        $data = ['title' => 'Services'];
        $data['breadcrumb'] = array("active" => "Services");
        $user = getuser();
        $data['user'] = $user;
        $where = array();
        $data['services'] = $this->master->getservices($where);
        //print_pre($data,true);
        $data['datatable'] = true;
        $this->template->load('services', 'services', $data);
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

        // Log for debugging
        if (ENVIRONMENT !== 'production') {
            log_message('debug', 'purchasedservices - User ID: ' . $user['id'] . ', Firm ID: ' . $firm_id . ', Year: ' . $year);
            log_message('debug', 'purchasedservices - Services found: ' . count($services));
            // Also log the WHERE clause for debugging
            log_message('debug', 'purchasedservices - WHERE: ' . json_encode($where));
        }

        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $services[$key]['name'] = $service['service_name'];
                $services[$key]['count'] = '';
                $services[$key]['link'] = ('services/monthlyservices/' . $service['service_slug']);
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
        if (!empty($service_package) && !empty($service_package['service_ids'])) {
            // Get service IDs from the package
            $package_service_ids = explode(',', $service_package['service_ids']);
            if (!empty($package_service_ids)) {
                // Filter to only show pending services that are part of the package
                $service_ids_str = implode(',', array_map('intval', $package_service_ids));
                // Use proper escaping for SQL query
                $user_id_escaped = $this->db->escape($user['id']);
                $firm_id_escaped = $this->db->escape($firm_id);
                $year_escaped = $this->db->escape($year);
                $where = "t1.user_id={$user_id_escaped} AND t1.firm_id={$firm_id_escaped} AND t1.year={$year_escaped} AND t1.status='0' AND t1.service_id IN ($service_ids_str)";
                $services = $this->service->getpurchasedservices($where, 'all', true); // Pass flag for group_by
            }
        }

        $data['services'] = $services;
        //print_pre($data,true);
        $data['datatable'] = true;
        //$data['folders']=$folders;
        $this->template->load('services', 'pendingservices', $data);
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
                    /*if($service['month']!=''){
                        $month=getyearmonthvalues($service['month']);
                        $slug=$month['value'];
                    }
                    else{
                        $slug=date('F-Y',strtotime($service['date']));
                    }*/
                    $documents = $this->service->getuploadeddocuments(['a.order_id' => $service['id']]);
                    $doc_names = !empty($documents) ? array_column($documents, 'display_name') : array();
                    if (!empty($doc_names)) {
                        $index = array_search('Month', $doc_names);
                        if ($index !== false) {
                            $name = $documents[$index]['formvalue'];
                        }
                    }
                } elseif ($service['purchased_type'] == 'Quarterly') {
                    /*if($service['month']!=''){
                        $month=getyearmonthvalues($service['month']);
                        $slug=$month['value'];
                    }
                    else{
                        $slug=date('F-Y',strtotime($service['date']));
                    }*/
                    $documents = $this->service->getuploadeddocuments(['a.order_id' => $service['id']]);
                    $doc_names = !empty($documents) ? array_column($documents, 'display_name') : array();
                    if (!empty($doc_names)) {
                        $index = array_search('Quarter', $doc_names);
                        if ($index !== false) {
                            $name = $documents[$index]['formvalue'];
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
                        $getassessment = $this->db->get_where('assessments', ['order_id' => $order['id'], 'status' => 0]);
                        $assessment = array();
                        if ($getassessment->num_rows() > 0) {
                            $assessment = $getassessment->unbuffered_row('array');
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
                } elseif (!in_array($type, $types)) {
                    $status = false;
                    $message = $type . " option not available for " . $service['name'];
                } elseif ($service['type'] == 'Once') {
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id'";
                    if ($service_for == 'Firm') {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);
                    if (!empty($purchases)) {
                        $status = false;
                        $message = "You have already Purchased " . $service['name'] . "!";
                    }
                } elseif ($types[0] == 'Yearly' && count($types) == 1) {
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                    if ($service_for == 'Firm') {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);
                    if (!empty($purchases)) {
                        $status = false;
                        $years = getyearmonthvalues($year);
                        $message = "You have already Purchased " . $service['name'] . " for " . $years['value'] . "!";
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
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                    if ($service_for == 'Firm') {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);
                    if (!empty($purchases) && count($purchases) >= 12) {
                        $status = false;
                        $years = getyearmonthvalues($year);
                        $message = "You have already Purchased " . $service['name'] . " for all Months of " . $years['value'] . "!";
                    }
                } elseif ($type == 'Quarterly') {
                    $where2 = "t1.user_id='$user[id]' and t1.service_id='$service_id' and t1.year='$year'";
                    if ($service_for == 'Firm') {
                        $where2 .= " and t1.firm_id='$firm_id'";
                    }
                    $purchases = $this->service->getpurchases($where2);
                    if (!empty($purchases) && count($purchases) >= 4) {
                        $status = false;
                        $years = getyearmonthvalues($year);
                        $message = "You have already Purchased " . $service['name'] . " for all Quarters of " . $years['value'] . "!";
                    }
                }
                if ($status) {
                    $service['rate'] = $service['rate'];
                    $subtotal = $service['rate'];
                    if (!empty($types) && count($types) > 1) {
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
                    $balance = $this->wallet->getwalletbalance($user['id']);

                    if ($balance >= $total) {
                        $data = array($single);
                        $data['name'] = $user['name'];
                        //print_pre($data,true);
                        $result = $this->service->purchaseservices($data);
                        //print_pre($result);
                        if ($result['status'] == true) {
                            $this->session->set_flashdata("msg", $result['message']);
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
