<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Phonepe {
    
    var $ci;
    var $env=PHONEPE_ENV;
    var $base_url;
    var $redirect_url;
    var $callback_url;
      
    function __construct() {
        $this->ci =& get_instance();
        if($this->env=='sandbox'){
            $this->base_url='https://api-preprod.phonepe.com/apis/hermes';
        }
        else{
            $this->base_url='https://api-preprod.phonepe.com/apis/hermes';
        }
    }
	
    function initiatePayment($data){
        $merchantUserId = $data['user_id'];
        $mobile = $data['mobile'];
        $amount = $data['amount']*100;
        $transactionId =  $data['transactionId'];// Unique transaction ID
        $merchantId = PHONEPE_MERCHANT_ID;
        $saltKey = PHONEPE_SALT_KEY;
        $saltIndex = PHONEPE_SALT_INDEX;
        
        $this->redirect_url=!empty($data['redirect_url'])?$data['redirect_url']:base_url('home/phoneperedirect');
        $this->callback_url=!empty($data['callback_url'])?$data['callback_url']:base_url('home/phonepewebhook');
        
        $payload = [
            "merchantId"            => $merchantId,
            "merchantTransactionId" => $transactionId,
            "merchantUserId"        => $merchantUserId,
            "amount"                => $amount,
            "redirectUrl"           => $this->redirect_url,
            "redirectMode"          => "POST",
            "callbackUrl"           => $this->callback_url,
            "mobileNumber"          => $mobile,
            "paymentInstrument"     => ["type" => "PAY_PAGE"] // Use "PAY_PAGE" for UPI/QR-based payment
        ];
        
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $base64Payload = base64_encode($payloadJson);
        
        // Generate SHA256 HMAC checksum
        $xVerify = hash('sha256', $base64Payload . "/pg/v1/pay" . $saltKey) . "###$saltIndex";
        
        // Set API URL
        $apiUrl = $this->base_url . "/pg/v1/pay";
        
        // Send request
        $headers = [
            "Content-Type: application/json",
            "X-VERIFY: $xVerify"
        ];
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["request" => $base64Payload]));
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success']) {
            // Redirect to PhonePe Payment URL
            header("Location: " . $responseData['data']['instrumentResponse']['redirectInfo']['url']);
            exit();
        } else {
            echo "Error: " . ($responseData['message']??'Please try Again!');
        }

    }

    function redirectResponse(){
        
        header("Content-Type: application/json");

        $log_file = './phonepe_redirect.log';

        // Get Raw Input
        $input_data = file_get_contents("php://input");

        // Debugging: Log the received raw data
        //file_put_contents($log_file, "Raw Data: " . print_r($input_data, true) . PHP_EOL, FILE_APPEND);

        if (!$input_data) {
                echo json_encode(["error" => "No data received"]);
                exit();
        }

        // Parse URL-encoded response
        parse_str($input_data, $data);
        //print_pre($data);

        // Debugging: Log the parsed data
        //file_put_contents($log_file, "Parsed Data: " . print_r($data, true) . PHP_EOL, FILE_APPEND);

        // Extract transaction details
        $transactionId = $data['transactionId'] ?? 'MISSING_TXN_ID';
        $referenceId = $data['providerReferenceId'] ?? 'MISSING_REF_ID';
        $paymentStatus = $data['code'] ?? 'UNKNOWN_STATUS';
        $amount = $data['amount'] ?? '0';
        $amount /= 100;

        // Check Payment Status
        if ($paymentStatus === 'PAYMENT_SUCCESS') {
                $result =["status" => "success", "transactionId" => $transactionId, "referenceId" => $referenceId, "amount" => $amount, "details" => json_encode($data)];
        } 
        elseif($paymentStatus === 'PAYMENT_PENDING') {
                $result =["status" => "pending", "transactionId" => $transactionId, "referenceId" => $referenceId, "amount" => $amount, "details" => json_encode($data)];
        }
        else {
                $result =["status" => "failed", "transactionId" => $transactionId, "referenceId" => $referenceId, "amount" => $amount, "details" => json_encode($data)];
        }

        return $result;
    }

}