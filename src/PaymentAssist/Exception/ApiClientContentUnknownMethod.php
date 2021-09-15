<?php

namespace PaymentAssist\Exception;

use GuzzleHttp\Utils;

/**
 * Class ApiClientContentUnknownMethod
 *
 * @package PaymentAssist\ApiClient
 */
class ApiClientContentUnknownMethod extends ApiClientException
{
    /**
     * @param string     $name
     * @param array|null $args
     */
    public function __construct(string $name, ?array $args = [])
    {
        parent::__construct(
            'Unknown method: "' . $name . '()" called with args: "' . Utils::jsonEncode($args)
            . '" on object of type PaymentAssist::Content'
        );
    }
}
