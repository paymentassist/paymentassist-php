<?php

namespace PaymentAssist;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ResponseStruct
 *
 * @package PaymentAssist\ApiClient
 */
final class ResponseStruct
{
    /** @var int */
    public $status;

    /** @var string */
    public $reason_phrase;

    /** @var array */
    public $headers;

    /** @var string */
    public $protocol;

    /** @var string */
    public $original_data;

    /** @var string */
    public $request_target;

    /** @var UriInterface */
    public $request_uri;

    /** @var string */
    public $request_method;

    /** @var string */
    public $command;

    /** @var array */
    public $content;

    /** @var string */
    public $message;

    /** @var StreamInterface|string */
    public $body;

    /** @var array */
    public $params;

    private function __construct(object $data = null)
    {
        foreach ((array)$data as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * @param object|null $data
     *
     * @return ResponseStruct
     */
    public static function instance(object $data = null): ResponseStruct
    {
        return new ResponseStruct($data);
    }
}
