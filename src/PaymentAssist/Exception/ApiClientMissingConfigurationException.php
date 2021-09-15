<?php

namespace PaymentAssist\Exception;

/**
 * Class ApiClientMissingConfigurationException
 *
 * @package PaymentAssist\ApiClient
 */
class ApiClientMissingConfigurationException extends ApiClientException
{
    public function __construct()
    {
        parent::__construct(
            'Missing configuration, provide the configuration in the constructor or '
            . 'use ApiClient::setConfig($configurationArray);'
        );
    }
}
