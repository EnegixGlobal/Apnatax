<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
    use PhonePe\Env;
    use PhonePe\payments\v1\PhonePePaymentClient;
    use PhonePe\payments\v1\models\request\builders\PgPayRequestBuilder;
    use PhonePe\payments\v1\models\request\builders\InstrumentBuilder;
    
    define('MERCHANTID','PGTESTPAYUAT105');
    define('SALTKEY',"c45b52fe-f2c5-4ef6-a6b5-131aa89ed133");
    define('SALTINDEX',"1");
    define('PHONEPEENV',Env::UAT);
    define('SHOULDPUBLISHEVENTS',true);

    if(!function_exists('createTransaction')){
        function createTransaction($data){
            $amount=$data['amount']; // In Rupees
            $amount*=100; // In Paise
            $phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, PHONEPEENV, SHOULDPUBLISHEVENTS);

            $request = PgPayRequestBuilder::builder()
                ->mobileNumber($data['mobile'])
                ->callbackUrl($data['callbackurl'])
                ->merchantId(MERCHANTID)
                ->merchantUserId($data['user_id'])
                ->amount($amount)
                ->merchantTransactionId($data['transaction_id'])
                ->redirectUrl($data['redirecturl'])
                ->redirectMode("REDIRECT")
                ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
                ->build();
            
            $response = $phonePePaymentsClient->pay($request);
            $url=$response->getInstrumentResponse()->getRedirectInfo()->getUrl();
            return $url;
        }
    }

    if(!function_exists('checkPaymentStatus')){
        function checkPaymentStatus($transaction_id){
            $phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, PHONEPEENV, SHOULDPUBLISHEVENTS);

            $checkStatus = $phonePePaymentsClient->statusCheck($transaction_id);
            $state=$checkStatus->getState();
            $paymentInstrument = $checkStatus->getPaymentInstrument();
            echo $bankId = $paymentInstrument->getBankId();
            echo $bankTransactionId = $paymentInstrument->getBankTransactionId();
            //print_pre($checkStatus);
        }
    }