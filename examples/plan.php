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
    $response = $client->GetPlanBreakdown(
        [
            'amount' => rand(100, 300) * 5 * 100,
        ]
    );

    if ($response->isOK()) {
        echo 'Content object:' . PHP_EOL;
        print_r($response->getContent());

        echo 'Content object, data property (Content object):' . PHP_EOL;
        print_r($response->getContent()->getData());

        echo 'Data property (Content object) as array:' . PHP_EOL;
        print_r($response->getContent()->getData()->toArray());

        echo 'Data property (Content object) retrieve the field value directly: ' . PHP_EOL;
        print_r($response->getContent()->getData()->getPlan());
        echo PHP_EOL;

        echo 'Data property (Content object) retrieve the field value directly: ' . PHP_EOL;
        print_r($response->getContent()->getData()->getAmount());
        echo PHP_EOL;

        echo 'Data property (Content object) retrieve the field value directly: ' . PHP_EOL;
        print_r($response->getContent()->getData()->getInterest());
        echo PHP_EOL;

        echo 'Data property (Content object) retrieve the field value directly: ' . PHP_EOL;
        print_r($response->getContent()->getData()->getRepayable());
        echo PHP_EOL;

        echo 'Data property (Content object) retrieve the field value directly: ' . PHP_EOL;
        print_r($response->getContent()->getData()->getSummary());
        echo PHP_EOL;

        echo 'Data property (Content object) retrieve the field value directly: ' . PHP_EOL;
        print_r($response->getContent()->getData()->getSchedule()->toArray());
        echo PHP_EOL;
    } else {
        echo($response->getStatus() . ' ' . $response->getReason());
        echo('There was an error while calling the API: ' . PHP_EOL . $response->getContents()->getMessage());
    }
}
