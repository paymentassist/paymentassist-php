paymentassist-php
=================
PHP SDK for the [Payment Assist][1] Partner API.

Dependencies
------------

This is a lightweight and minimal client designed for easy drop-in to most code-bases. As such, the only requirement beside PHP itself is cURL.

Workflow
--------

![Payment Assist API Workflow](api-workflow.png?raw=true "API Workflow")

Installation
------------

Install with Composer:

`composer require paymentassist/paymentassist-php`

Usage
-----

    $credentials = array('api_key'=>'YOUR-KEY', 'secret'=>'YOUR-SECRET');
    $pa = new \PaymentAssist\ApiClient($credentials);

    $result = $pa->request('/begin', 'POST', $params);

Refer to API documentation for details of valid endpoints and required params.

 [1]: https://www.payment-assist.co.uk/
