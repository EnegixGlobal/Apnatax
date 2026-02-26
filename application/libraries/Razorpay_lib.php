<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Razorpay_lib
{
    protected $key_id;
    protected $key_secret;
    protected $api_base = 'https://api.razorpay.com/v1';

    public function __construct()
    {
        $this->key_id     = RAZORPAY_KEY_ID;
        $this->key_secret = RAZORPAY_KEY_SECRET;
    }

    /**
     * Create Razorpay order
     *
     * @param float  $amount_rupees Amount in rupees
     * @param string $receipt       Unique receipt / merchant transaction id
     * @param array  $notes         Optional notes
     * @return array [success => bool, data|error => mixed]
     */
    public function create_order($amount_rupees, $receipt, $notes = [])
    {
        $amount_paise = (int)round($amount_rupees * 100);

        $payload = [
            'amount'          => $amount_paise,
            'currency'        => RAZORPAY_CURRENCY,
            'receipt'         => $receipt,
            'payment_capture' => 1,
            'notes'           => $notes,
        ];

        $url = $this->api_base . '/orders';

        $response = $this->request('POST', $url, $payload);

        return $response;
    }

    /**
     * Fetch Razorpay order
     */
    public function fetch_order($order_id)
    {
        $url = $this->api_base . '/orders/' . $order_id;
        return $this->request('GET', $url);
    }

    /**
     * Verify Razorpay payment signature
     */
    public function verify_signature($order_id, $payment_id, $signature)
    {
        $generated_signature = hash_hmac('sha256', $order_id . '|' . $payment_id, $this->key_secret);
        return hash_equals($generated_signature, $signature);
    }

    /**
     * Low-level HTTP request helper
     */
    protected function request($method, $url, $payload = null)
    {
        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $this->key_id . ':' . $this->key_secret,
            CURLOPT_HTTPHEADER     => $headers,
        ];

        if (strtoupper($method) === 'POST') {
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }

        curl_setopt_array($ch, $options);

        $response_body = curl_exec($ch);
        $http_code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error    = curl_error($ch);

        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'error'   => $curl_error,
            ];
        }

        $decoded = json_decode($response_body, true);

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success' => true,
                'data'    => $decoded,
            ];
        }

        return [
            'success' => false,
            'error'   => !empty($decoded['error']) ? $decoded['error'] : $response_body,
        ];
    }
}


