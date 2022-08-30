<?php

require __DIR__ . '/common/loader.php';

use PaymentAssist\ApiClient;
use PaymentAssist\Exception\ApiClientException;

$config = require __DIR__ . '/common/config.php';

$client = null;

try {
    $client = ApiClient::instance($config)->setConnection(ApiClient::PARTNER_API_V1);
} catch (ApiClientException $e) {
    echo 'Error while setting a connection to the API';
}

if ($client instanceof ApiClient) {
    $response = $client->ObtainPreApproval(
        [
            'f_name' => 'John',
            's_name' => 'Appleseed',
            'addr1' => 'Apple Blvd',
            'postcode' => 'AP71ES',
        ]
    );

    if ($response->isOK()) {
        echo 'Content object:' . PHP_EOL;
        print_r($response->getContent());

        echo 'Content object, data property (Content object):' . PHP_EOL;
        print_r($response->getContent()->getData());

        echo 'Data property (Content object) as array:' . PHP_EOL;
        print_r($response->getContent()->getData()->toArray());

        echo 'Data property (Content object) retrieve the field value directly:' . PHP_EOL;

        print_r($response->getContent()->getData()->getApproved());
        echo PHP_EOL;
    } else {
        echo $response->getStatus() . ' ' . $response->getReason();
        echo 'There was an error fetching plans from the API: ' . PHP_EOL . $response->getContents()->getMessage();
    }
}
