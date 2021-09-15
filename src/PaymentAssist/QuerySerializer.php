<?php

namespace PaymentAssist;

use GuzzleHttp\Command\Guzzle\QuerySerializer\QuerySerializerInterface;

/**
 * Class QuerySerializer
 *
 * @package PaymentAssist\ApiClient
 */
class QuerySerializer implements QuerySerializerInterface
{
    /**
     * {@inheritDoc}
     */
    public function aggregate(array $queryParams): string
    {
        return http_build_query($queryParams);
    }
}
