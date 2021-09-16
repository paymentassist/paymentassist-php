# paymentassist-php

PHP SDK for the [Payment Assist][1] Partner API.

## Dependencies

* PHP >= 7.2
* PHP JSON extension
* PHP cURL extension

## Workflow

![Payment Assist API Workflow](api-workflow.png?raw=true "API Workflow")

## Installation

Install with Composer:

`composer require paymentassist/paymentassist-php`

## Publish configuration files

Use composer to publish configuration files:

`composer --working-dir=vendor/paymentassist/paymentassist-php/ run-script publish-config`

## Usage

```php

use PaymentAssist\ApiClient;

$config = [
    'debug'                  => true,
    'timeout'                => 120,
    'log'                    => [
        'debug'                       => true,
        'log_file_name'               => 'apiclient.log',
        'log_file_path'               => 'PATH-TO-YOUR-LOGS-FOLDER',
        'log_request_before_response' => true,
        'log_app_name'                => 'pa_partner_api',
    ],
    'verify_ssl_certificate' => true,
    'default'                => 'partner_api_v1',
    'connections'            => [
        'partner_api_v1' => [
            'base_uri'               => 'https://api.v1.payment-assist.co.uk',
            'manifest_path'          => 'default', // use manifest files stored within the package
            'api_key'                => 'YOUR-KEY',
            'secret'                 => 'YOUR-SECRET',
            'additional_query_param' => [],
        ],
    ],
];
        
$client   = ApiClient::instance($config)->setConnection(ApiClient::PARTNER_API_V1);
$response = $client->GetAccountConfigurationDetails();

if ($response->isOK()) {
    $plans = collection(
        $response
            ->getContent()
            ->getData()
            ->getPlans()
            ->toArray()
    )->map(function ($plan) {
        return $plan['name'];
    })->toList();
} else {
    echo($response->getStatus() . ' ' . $response->getReason());
    echo('There was an error fetching plans from the API: ' . $response->getContents()->getMessage());
}
```
In the above example `$config` variable contains a config structure which needs to be provided as an argument to `ApiClient::instance()` static method.

This structure can be stored in the config section of your application. After installing the package you can publish an example config file. Composer script will copy the file `apiclient.php` to the `config` folder in the root folder of your app if it exists, otherwise it will copy the file to the root folder of your app.

Config file contains the structure and the default config values which can be overridden by the values stored in the file `.apiclient.env` which will be copied to the root folder of your app.

Refer to [API documentation][2] for details of valid endpoints and required params.

[1]: https://www.payment-assist.co.uk
[2]: https://api-docs.payment-assist.co.uk
