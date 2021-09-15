<?php

namespace PaymentAssist;

use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResultSerializer
 *
 * @package PaymentAssist\ApiClient
 */
final class ResultSerializer
{
    /**
     * @param ResponseInterface $response
     * @param RequestInterface  $request
     * @param CommandInterface  $command
     *
     * @return object
     */
    public function __invoke(ResponseInterface $response, RequestInterface $request, CommandInterface $command): object
    {
        $body = $response->getBody();

        return (object)[
            'status'         => $response->getStatusCode(),
            'reason_phrase'  => $response->getReasonPhrase(),
            'headers'        => $response->getHeaders(),
            'protocol'       => $response->getProtocolVersion(),
            'content'        => Utils::jsonDecode($body->getContents(), true),
            'original_data'  => (string)$body,
            'request_target' => $request->getRequestTarget(),
            'request_uri'    => $request->getUri(),
            'request_method' => $request->getMethod(),
            'command'        => $command->getName(),
        ];
    }
}
