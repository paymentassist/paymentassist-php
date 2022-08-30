<?php

namespace PaymentAssist;

use Dflydev\DotAccessData\Data;
use GuzzleHttp\Utils;
use PaymentAssist\Exception\ApiClientContentUnknownMethod;
use PaymentAssist\Helpers\Helpers;

/**
 * Class Content
 *
 * @package PaymentAssist\ApiClient
 *
 * @method get(string $string)
 * @method getStatus()
 * @method getMsg()
 * @method getData()
 * @method getMessage()
 * @method getCommand()
 * @method getParams()
 * @method getContent()
 */
final class Content
{
    /** @var array|null */
    private $data;

    private function __construct(?array $data = null)
    {
        $this->data = $data;
    }

    /**
     * @param array|null $data
     *
     * @return Content
     */
    public static function make(?array $data = []): Content
    {
        return new self($data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        return [];
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        $array = $this->toArray();
        if (!empty($array)) {
            return Utils::jsonEncode($array, JSON_PRETTY_PRINT);
        }

        return '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return Content|array|mixed|void|null
     * @throws ApiClientContentUnknownMethod
     */
    public function __call(string $name, array $args)
    {
        $arg = $args[0] ?? null;

        if ($name === 'get' && $arg !== null) {
            return $this->parseDotNotation($arg);
        }

        if (substr($name, 0, 3) === 'get') {
            return $this->parseGetterName($name);
        }

        throw new ApiClientContentUnknownMethod($name, $args);
    }

    /**
     * @param string $arg
     *
     * @return Content|mixed|array|void|null
     */
    private function parseDotNotation(string $arg)
    {
        $dotData = new Data($this->data);
        if ($dotData->has($arg)) {
            $property = $dotData->get($arg);
            if (is_array($property)) {
                return self::make($property);
            }

            return $property;
        }
    }

    /**
     * @param string $name
     *
     * @return Content|mixed|array|void|null
     * @throws ApiClientContentUnknownMethod
     */
    private function parseGetterName(string $name)
    {
        $property = Helpers::camel2snake(substr($name, -(strlen($name) - 3)));
        if (array_key_exists($property, $this->data)) {
            if (is_array($this->data[$property])) {
                return self::make($this->data[$property]);
            }

            return $this->data[$property];
        }

        throw new ApiClientContentUnknownMethod($name);
    }
}
