<?php

namespace PaymentAssist\Exception;

use Exception;

/**
 * Class ApiClientException
 *
 * @package PaymentAssist\ApiClient
 */
class ApiClientException extends Exception
{
    const EXCEPTION_MESSAGE_PREFIX = 'PaymentAssist ApiClient: ';

    /**
     * @param string|null $message
     * @param int         $code
     */
    public function __construct(string $message = null, int $code = 0)
    {
        parent::__construct(self::EXCEPTION_MESSAGE_PREFIX . ($message ?: 'General exception'), $code);
    }
}
