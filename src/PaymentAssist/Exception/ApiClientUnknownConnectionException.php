<?php

namespace PaymentAssist\Exception;

/**
 * Class ApiClientUnknownConnectionException
 *
 * @package PaymentAssist\ApiClient
 */
class ApiClientUnknownConnectionException extends ApiClientException
{
    public function __construct()
    {
        parent::__construct('Unknown connection, check calling method and/or config file');
    }
}
