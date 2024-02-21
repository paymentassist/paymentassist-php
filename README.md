# paymentassist-php

PHP SDK for the [Payment Assist][1] Partner API.

> [!NOTE]
> <strong>paymentassist-php v2 is now the default version</strong>. For legacy integrations, please see the [v1 branch](https://github.com/paymentassist/paymentassist-php/tree/v1).


## Dependencies

* PHP >= 7.2
* PHP JSON extension
* PHP cURL extension

## Workflow

![Payment Assist API Workflow](https://1979817456-files.gitbook.io/~/files/v0/b/gitbook-legacy-files/o/assets%2F-LB0_w9C7uFYYWDZcixr%2F-MTMLOXG5c0Wju4XPWoM%2F-MTMLRe04jbQiPBJ8ctt%2FAPI%20web-flow%20(5).png?alt=media&token=c05f973e-74ba-4abf-addd-29b7f7380edb "API Workflow")

## Installation

Install with Composer:

`composer require paymentassist/paymentassist-php:dev-v2`

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
        'log_app_name'                => 'ApiClient',
        'log_format'                  => null, // if null, a default format from GuzzleHttp\MessageFormatter class will be used
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

## Contributing

We welcome contributions. There are several ways to help out:

* Create an issue on GitHub, if you have found a bug.
* Write patches for open bugs/feature issues, preferably with test cases included. Please fork our repo, make your changes in a new branch and then open a pull request ensuring the correct target branch.
* Contribute to the [documentation][2]

## Support

For integrations support, please email [itsupport@payment-assist.co.uk](mailto:itsupport@payment-assist.co.uk).

[1]: https://www.payment-assist.co.uk
[2]: https://api-docs.payment-assist.co.uk
