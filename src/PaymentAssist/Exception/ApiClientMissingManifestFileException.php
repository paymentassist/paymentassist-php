<?php

namespace PaymentAssist\Exception;

/**
 * Class ApiClientMissingManifestFileException
 *
 * @package PaymentAssist\ApiClient
 */
class ApiClientMissingManifestFileException extends ApiClientException
{
    public function __construct()
    {
        parent::__construct('Manifest file not found');
    }
}
