<?php

namespace PaymentAssist\Exception;

/**
 * Class ApiClientUnknownOperationException
 *
 * @package PaymentAssist\ApiClient
 */
class ApiClientUnknownOperationException extends ApiClientException
{
    public function __construct()
    {
        parent::__construct('Unknown operation, check calling method and/or service description file');
    }
}
