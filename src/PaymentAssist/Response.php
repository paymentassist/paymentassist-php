<?php

namespace PaymentAssist;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class Response
 *
 * @package PaymentAssist\ApiClient
 */
final class Response
{
    /** @var int */
    private $status;

    /** @var string */
    private $reason;

    /** @var array */
    private $headers;

    /** @var string */
    private $protocol;

    /** @var string */
    private $original_data;

    /** @var string */
    private $request_target;

    /** @var UriInterface */
    private $request_uri;

    /** @var string */
    private $request_method;

    /** @var string */
    private $commandName;

    /** @var object */
    private $data;

    /**
     * @param ResponseStruct|null $data
     */
    public function __construct(?ResponseStruct $data = null)
    {
        $this->data = $data;
        if (!is_null($data)) {
            $this->status         = $data->status ?? 0;
            $this->reason         = $data->reason_phrase ?? '';
            $this->headers        = $data->headers ?? [];
            $this->protocol       = $data->protocol ?? '';
            $this->original_data  = $data->original_data ?? '';
            $this->request_target = $data->request_target ?? '';
            $this->request_uri    = $data->request_uri ?? Uri::fromParts([]);
            $this->request_method = $data->request_method ?? '';
            $this->commandName    = $data->command ?? 'no command';
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = get_object_vars($this);
        unset($array['data']);

        return Content::make($array)->toArray();
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        $array                = $this->toArray();
        $array['request_uri'] = (string)$this->request_uri;
        $array['command']     = $this->commandName;

        return Content::make($array)->toJson();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getOriginalData(): string
    {
        return $this->original_data;
    }

    /**
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->request_target;
    }

    /**
     * @return UriInterface
     */
    public function getRequestUri(): UriInterface
    {
        return $this->request_uri;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->request_method;
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
    }

    /**
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isOK(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return !$this->isOK();
    }

    /**
     * @return Content
     */
    public function getContents(): Content
    {
        return Content::make((array)$this->data);
    }

    /**
     * @return string
     */
    public function getContentsJson(): string
    {
        return $this->getContents()->toJson();
    }

    /**
     * @return array
     */
    public function getContentsArray(): array
    {
        return $this->getContents()->toArray();
    }

    /**
     * @return Content
     */
    public function getContent(): Content
    {
        return $this->getContents()->getContent();
    }
}
