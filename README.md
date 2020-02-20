# paymentassist-php

PHP SDK for the [Payment Assist][1] Partner API.

## Dependencies

 * PHP >= 5.3
 * PHP JSON extension
 * PHP cURL extension
 
## Workflow

![Payment Assist API Workflow](api-workflow.png?raw=true "API Workflow")

## Installation

Install with Composer:

`composer require paymentassist/paymentassist-php`

## Usage

```php
$credentials = array('api_key'=>'YOUR-KEY', 'secret'=>'YOUR-SECRET');
$pa = new \PaymentAssist\ApiClient($credentials);

$result = $pa->request('/begin', 'POST', $params);
```
Refer to API documentation for details of valid endpoints and required params.

 [1]: https://www.payment-assist.co.uk/
