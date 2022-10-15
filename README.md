<hr>
<div align="center">
:loudspeaker: <strong><a href="https://github.com/paymentassist/paymentassist-php/tree/v2">paymentassist-php v2</a> is now available in preview</strong>.<br>We recommend you use this version of the SDK for all new integrations.
</div>
<hr>

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
