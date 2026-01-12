<?php
session_start();
header("Content-Type: application/json");
	    header("Access-Control-Allow-Origin: *");
	    header("Access-Control-Allow-headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	    header("X-Frame-Options: DENY");
	    header("X-XSS-Protection: 1;node=block");
	    header("X-XSS-Type-Options: nosniff");
	    echo $rawdata=file_get_contents("php://input");
	    $data=json_decode($rawdata,true);
	    print_r($data);
        //$this->load->helper('phonepe');
        //$result=checkPaymentStatus($this->session->transaction_id);
	   // print_r($result);
