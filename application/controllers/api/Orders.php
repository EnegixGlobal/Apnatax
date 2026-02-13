<?php
defined('BASEPATH') or exit('No direct script access allowed');
//include Rest Controller library
use chriskacerguis\RestServer\RestController;

class Orders extends RestController
{
    function __construct()
    {
        parent::__construct();
        logrequest();
    }

    public function getorders_post()
    {
        $token = $this->post('token');
        $firm_id = $this->post('firm_id');
        $year = $this->post('year');
        $status = $this->post('status'); // Optional: filter by status

        if (!empty($token)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $where = array('t1.user_id' => $user['id']);
                if (!empty($firm_id)) {
                    $where['t1.firm_id'] = $firm_id;
                }
                if (!empty($year)) {
                    $where['t1.year'] = $year;
                }
                if ($status !== null && $status !== '') {
                    $where['t1.status'] = $status;
                }
                $orders = $this->service->getpurchases($where);
                if (!empty($orders)) {
                    foreach ($orders as $key => $order) {
                        // Add status text
                        $status_text = '';
                        switch ($order['status']) {
                            case 0:
                                $status_text = 'Pending';
                                break;
                            case 1:
                                $status_text = 'Completed';
                                break;
                            case 2:
                                $status_text = 'Cancelled';
                                break;
                            case 3:
                                $status_text = 'In Progress';
                                break;
                            case 4:
                                $status_text = 'Assessment Done';
                                break;
                            default:
                                $status_text = 'Unknown';
                                break;
                        }
                        $orders[$key]['status_text'] = $status_text;
                    }
                    $this->response([
                        'status' => true,
                        'orders' => $orders
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "No Orders Found!"
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

    public function getorderdetails_post()
    {
        $token = $this->post('token');
        $order_id = $this->post('order_id');

        if (!empty($token) && !empty($order_id)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user) && $user['role'] == 'customer') {
                $where = array('t1.id' => $order_id, 't1.user_id' => $user['id']);
                $order = $this->service->getpurchases($where, 'single');
                if (!empty($order)) {
                    // Get documents
                    $documents = $this->service->getuploadeddocuments(['a.order_id' => $order['id']]);
                    if (!empty($documents)) {
                        foreach ($documents as $key => $value) {
                            if (strpos($value['formvalue'], '/assets/') === 0) {
                                $documents[$key]['formvalue'] = file_url($value['formvalue']);
                            }
                        }
                    } else {
                        // Get service documents template if no documents uploaded
                        $documents = $this->master->getservicedocuments(['t1.service_id' => $order['service_id']]);
                    }

                    // Get assessment
                    $getassessment = $this->db->get_where('assessments', ['order_id' => $order['id'], 'status' => 1]);
                    $assessment = array();
                    if ($getassessment->num_rows() > 0) {
                        $assessment = $getassessment->unbuffered_row('array');
                        if (!empty($assessment['file'])) {
                            $assessment['file'] = file_url($assessment['file']);
                        }
                    } else {
                        // Fallback to status=0 for backward compatibility
                        $getassessment = $this->db->get_where('assessments', ['order_id' => $order['id'], 'status' => 0]);
                        if ($getassessment->num_rows() > 0) {
                            $assessment = $getassessment->unbuffered_row('array');
                            if (!empty($assessment['file'])) {
                                $assessment['file'] = file_url($assessment['file']);
                            }
                        }
                    }

                    // Get assigned employee
                    $getassigned = $this->db->get_where('order_assign', ['order_id' => $order['id'], 'status' => 0]);
                    $assigned = array();
                    if ($getassigned->num_rows() > 0) {
                        $assigned = $getassigned->unbuffered_row('array');
                    }

                    // Add status text
                    $status_text = '';
                    switch ($order['status']) {
                        case 0:
                            $status_text = 'Pending';
                            break;
                        case 1:
                            $status_text = 'Completed';
                            break;
                        case 2:
                            $status_text = 'Cancelled';
                            break;
                        case 3:
                            $status_text = 'In Progress';
                            break;
                        case 4:
                            $status_text = 'Assessment Done';
                            break;
                        default:
                            $status_text = 'Unknown';
                            break;
                    }
                    $order['status_text'] = $status_text;

                    $this->response([
                        'status' => true,
                        'order' => $order,
                        'documents' => $documents,
                        'assessment' => $assessment,
                        'assigned' => $assigned
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Order Not Found!"
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

    public function downloaddocument_post()
    {
        $token = $this->post('token');
        $document_id = $this->post('document_id');
        $type = $this->post('type'); // 'formdata', 'assessment', 'certificate', 'olddata'

        if (!empty($token) && !empty($document_id)) {
            $user = $this->account->verify_token($token);
            if (!empty($user) && is_array($user)) {
                $file_path = '';
                $file_name = '';

                if ($type == 'formdata') {
                    $document = $this->service->getuploadeddocuments(['a.id' => $document_id]);
                    if (!empty($document) && $document[0]['user_id'] == $user['id']) {
                        $file_path = $document[0]['formvalue'];
                        $file_name = $document[0]['display_name'] . '.' . pathinfo($file_path, PATHINFO_EXTENSION);
                    }
                } elseif ($type == 'assessment') {
                    $assessment = $this->db->get_where('assessments', ['id' => $document_id])->row_array();
                    if (!empty($assessment)) {
                        $order = $this->service->getpurchases(['t1.id' => $assessment['order_id']], 'single');
                        if (!empty($order) && $order['user_id'] == $user['id']) {
                            $file_path = $assessment['file'];
                            $file_name = 'Assessment-' . $order['service_name'] . '.' . pathinfo($file_path, PATHINFO_EXTENSION);
                        }
                    }
                } elseif ($type == 'certificate') {
                    $allowed_types = array('tds_certificate', 'gst_certificate', 'audit_report', 'income_tax_certificate');
                    if (in_array($document_id, $allowed_types)) {
                        $kyc = $this->db->select($document_id)->where('user_id', $user['id'])->get('kyc')->row_array();
                        if (!empty($kyc) && !empty($kyc[$document_id])) {
                            $file_path = $kyc[$document_id];
                            $file_name = ucfirst(str_replace('_', ' ', $document_id)) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);
                        }
                    }
                } elseif ($type == 'olddata') {
                    $old_data = $this->customer->getoldclientdata(['md5(t1.id)' => $document_id, 't1.user_id' => $user['id']], 'single');
                    if (!empty($old_data)) {
                        $file_path = $old_data['file_path'];
                        $file_name = $old_data['file_name'];
                    }
                }

                if (!empty($file_path)) {
                    $full_path = FCPATH . $file_path;
                    if (file_exists($full_path)) {
                        $this->response([
                            'status' => true,
                            'download_url' => file_url($file_path),
                            'file_name' => $file_name
                        ], RestController::HTTP_OK);
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => "File Not Found!"
                        ], RestController::HTTP_OK);
                    }
                } else {
                    $this->response([
                        'status' => false,
                        'message' => "Document Not Found or Access Denied!"
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
}
