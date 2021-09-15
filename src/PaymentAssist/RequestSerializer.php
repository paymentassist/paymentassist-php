<?php

namespace PaymentAssist;

use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Command\Guzzle\Serializer;

/**
 * Class RequestSerializer
 *
 * @package PaymentAssist\ApiClient
 */
final class RequestSerializer extends Serializer
{
    /**
     * @param DescriptionInterface $description
     * @param array                $requestLocations
     */
    public function __construct(DescriptionInterface $description, array $requestLocations = [])
    {
        parent::__construct($description, $requestLocations);
    }
}
