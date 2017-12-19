<?php
/**
 * Payment Assist PHP-SDK
 * v1.0 (initial release)
 */

namespace PaymentAssist;

class ApiClient {

    const VERSION = 'v1.0.0';

    public static $apiUrl = 'https://api.v1.payment-assist.co.uk';
    public static $userAgent = 'Payment Assist PHP Client';
    public static $serializer = array('json_encode', 'json_decode');

    protected $_http_status = null;
    protected $_credentials = array(
        'api_key' => null,
        'secret' => null
        );


    /**
     * Sets up a new PAapi instance
     *
     * @param array $credentials
     * @return void
     */
    public function __construct($credentials = array()) {
        $this->_credentials = $credentials;
    }


    /**
     * Generic request function
     *
     * @param string $peth
     * @param array $params
     * @return mixed
     */
    public function request($path, $method = 'POST', $params = null) {
        $options = array('path'=>$path, 'method'=>$method);
        if (!empty($params)) $options['params'] = $params;
        $res = self::rest($options);
        return $res;
    }


    /**
     * Generates authentication signature
     *
     * @param array $credentials
     * @return array
     */
    protected static function _generateSignature($params, $credentials) {
        ksort($params);
        $str = '';
        foreach($params as $k=>$v) {
          $k = strtoupper($k);
          if ($k !== 'SIGNATURE' && $k !== 'API_KEY') {
            $str .= $k . '=' . $v . '&';
          }
        }
        $signature = hash_hmac('sha256', $str, $credentials['secret'], false);
        return $signature;
    }


    /**
     * Generates a REST request to the PA API
     *
     * @param array $options
     * @param array $credentials
     * @return mixed
     */
    public function rest($options, $credentials = array()) {
        $defaults = array(
            'method' => 'POST',
            'url' => self::$apiUrl,
            'path' => '/',
            'params' => array()
            );

        $options = $options + $defaults;

        if (empty($credentials)) {
            $credentials = $this->_credentials;
        }

        $signature = self::_generateSignature($options['params'], $credentials);

        $options['params'] = $options['params'] + array(
            'api_key' => $credentials['api_key'],
            'signature' => $signature,
            );

        $curl_opts = array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => self::$userAgent .' '. self::VERSION
        );

        if ($options['method'] == 'POST') {
            $curl_opts[CURLOPT_POST] = true;
            $curl_opts[CURLOPT_POSTFIELDS] = $options['params'];
        } else {
            $options['path'] .= '?' . http_build_query($options['params']);
        }

        $curl = curl_init($options['url'] . $options['path']);
        curl_setopt_array($curl, $curl_opts);

        $r = curl_exec($curl);
        $this->_http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($error = curl_error($curl)) {
            trigger_error('Payment Assist: curl error: ' . curl_error($curl), E_USER_WARNING);
        }

        return call_user_func_array(self::$serializer[1], array($r));
    }

    /**
     * Return the last HTTP status code received. Useful for debugging purposes.
     *
     * @return integer
     */
    public function httpStatus() {
        return $this->_http_status;
    }

}
