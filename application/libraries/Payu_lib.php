<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payu_lib
{
    protected $merchant_key;
    protected $salt;
    protected $payment_url;
    protected $success_url;
    protected $failure_url;

    public function __construct()
    {
        $this->merchant_key = PAYU_MERCHANT_KEY;
        $this->salt = PAYU_SALT;
        
        // Use full URLs for callbacks - PayU needs absolute URLs
        $base_url = base_url();
        $this->success_url = $base_url . 'home/payu_success';
        $this->failure_url = $base_url . 'home/payu_failure';
        
        // Set payment URL based on environment
        if (PAYU_ENV === 'production') {
            $this->payment_url = PAYU_PRODUCTION_URL;
        } else {
            $this->payment_url = PAYU_TEST_URL;
        }
    }

    /**
     * Generate PayU payment hash
     *
     * @param array $params Payment parameters
     * @return string Hash string
     */
    public function generate_hash($params)
    {
        $hash_string = '';
        $hash_string .= $params['key'];
        $hash_string .= '|';
        $hash_string .= $params['txnid'];
        $hash_string .= '|';
        $hash_string .= $params['amount'];
        $hash_string .= '|';
        $hash_string .= $params['productinfo'];
        $hash_string .= '|';
        $hash_string .= $params['firstname'];
        $hash_string .= '|';
        $hash_string .= $params['email'];
        $hash_string .= '|';
        $hash_string .= $params['udf1'];
        $hash_string .= '|';
        $hash_string .= $params['udf2'];
        $hash_string .= '|';
        $hash_string .= $params['udf3'];
        $hash_string .= '|';
        $hash_string .= $params['udf4'];
        $hash_string .= '|';
        $hash_string .= $params['udf5'];
        $hash_string .= '|';
        $hash_string .= $params['udf6'];
        $hash_string .= '|';
        $hash_string .= $params['udf7'];
        $hash_string .= '|';
        $hash_string .= $params['udf8'];
        $hash_string .= '|';
        $hash_string .= $params['udf9'];
        $hash_string .= '|';
        $hash_string .= $params['udf10'];
        $hash_string .= '|';
        $hash_string .= $this->salt;

        $hash = strtolower(hash('sha512', $hash_string));
        return $hash;
    }

    /**
     * Verify PayU payment response hash
     *
     * @param array $params Response parameters
     * @return bool True if hash is valid
     */
    public function verify_hash($params)
    {
        $hash_string = '';
        $hash_string .= $this->salt;
        $hash_string .= '|';
        $hash_string .= $params['status'];
        $hash_string .= '|';
        $hash_string .= $params['udf10'];
        $hash_string .= '|';
        $hash_string .= $params['udf9'];
        $hash_string .= '|';
        $hash_string .= $params['udf8'];
        $hash_string .= '|';
        $hash_string .= $params['udf7'];
        $hash_string .= '|';
        $hash_string .= $params['udf6'];
        $hash_string .= '|';
        $hash_string .= $params['udf5'];
        $hash_string .= '|';
        $hash_string .= $params['udf4'];
        $hash_string .= '|';
        $hash_string .= $params['udf3'];
        $hash_string .= '|';
        $hash_string .= $params['udf2'];
        $hash_string .= '|';
        $hash_string .= $params['udf1'];
        $hash_string .= '|';
        $hash_string .= $params['email'];
        $hash_string .= '|';
        $hash_string .= $params['firstname'];
        $hash_string .= '|';
        $hash_string .= $params['productinfo'];
        $hash_string .= '|';
        $hash_string .= $params['amount'];
        $hash_string .= '|';
        $hash_string .= $params['txnid'];
        $hash_string .= '|';
        $hash_string .= $this->merchant_key;

        $calculated_hash = strtolower(hash('sha512', $hash_string));
        $received_hash = strtolower($params['hash']);

        return hash_equals($calculated_hash, $received_hash);
    }

    /**
     * Prepare payment parameters
     *
     * @param float $amount Amount in rupees
     * @param string $txnid Transaction ID
     * @param string $productinfo Product information
     * @param string $firstname Customer first name
     * @param string $email Customer email
     * @param string $phone Customer phone
     * @param array $udf Additional parameters (udf1-udf10)
     * @return array Payment parameters
     */
    public function prepare_payment_params($amount, $txnid, $productinfo, $firstname, $email, $phone = '', $udf = [])
    {
        $params = [
            'key' => $this->merchant_key,
            'txnid' => $txnid,
            'amount' => number_format($amount, 2, '.', ''),
            'productinfo' => $productinfo,
            'firstname' => $firstname,
            'email' => $email,
            'phone' => $phone,
            'surl' => $this->success_url,
            'furl' => $this->failure_url,
            'udf1' => isset($udf['udf1']) ? $udf['udf1'] : '',
            'udf2' => isset($udf['udf2']) ? $udf['udf2'] : '',
            'udf3' => isset($udf['udf3']) ? $udf['udf3'] : '',
            'udf4' => isset($udf['udf4']) ? $udf['udf4'] : '',
            'udf5' => isset($udf['udf5']) ? $udf['udf5'] : '',
            'udf6' => isset($udf['udf6']) ? $udf['udf6'] : '',
            'udf7' => isset($udf['udf7']) ? $udf['udf7'] : '',
            'udf8' => isset($udf['udf8']) ? $udf['udf8'] : '',
            'udf9' => isset($udf['udf9']) ? $udf['udf9'] : '',
            'udf10' => isset($udf['udf10']) ? $udf['udf10'] : '',
        ];

        // Generate hash
        $params['hash'] = $this->generate_hash($params);

        return $params;
    }

    /**
     * Get payment URL
     *
     * @return string Payment URL
     */
    public function get_payment_url()
    {
        return $this->payment_url;
    }

    /**
     * Check payment status via PayU API
     *
     * @param string $txnid Transaction ID
     * @return array Status check response
     */
    public function check_payment_status($txnid)
    {
        $command = "verify_payment";
        $hash_string = $this->merchant_key . "|" . $command . "|" . $txnid . "|" . $this->salt;
        $hash = strtolower(hash('sha512', $hash_string));

        $post_data = [
            'key' => $this->merchant_key,
            'command' => $command,
            'hash' => $hash,
            'var1' => $txnid,
        ];

        // Set status check URL based on environment
        $status_url = (PAYU_ENV === 'production') ? PAYU_PRODUCTION_STATUS_URL : PAYU_TEST_STATUS_URL;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'error' => $curl_error,
            ];
        }

        $decoded = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success' => true,
                'data' => $decoded,
            ];
        }

        return [
            'success' => false,
            'error' => !empty($decoded['error']) ? $decoded['error'] : $response,
        ];
    }
}

