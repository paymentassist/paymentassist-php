<?php

namespace PaymentAssist\Traits;

use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\HandlerStack;
use PaymentAssist\ApiClient;
use PaymentAssist\Exception\{ApiClientMissingConfigurationException,
    ApiClientMissingManifestFileException,
    ApiClientUnknownConnectionException
};

/**
 * trait ApiClientTrait
 *
 * @package PaymentAssist\ApiClient
 */
trait ApiClientTrait
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $secret;

    /** @var string */
    private $connection;

    /** @var string */
    private $baseUri;

    /** @var string */
    private $manifestPath;

    /** @var integer */
    private $timeout;

    /** @var HandlerStack|null */
    private $handlerStack;

    /** @var boolean */
    private $verifySSLCertificate;

    /**
     * @param array $config
     *
     * @return ApiClient
     */
    public function setConfig(array $config): ApiClient
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return string
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function getApiKey(): string
    {
        if (empty($this->apiKey)) {
            $this->apiKey = $this->getConfig()['connections'][$this->getConnection()]['api_key'];
        }

        return $this->apiKey;
    }

    /**
     * @return mixed|string
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function getSecret()
    {
        if (empty($this->secret)) {
            $this->secret = $this->getConfig()['connections'][$this->getConnection()]['secret'];
        }

        return $this->secret;
    }

    /**
     * @return string|null
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function getManifestPath(): ?string
    {
        if (empty($this->manifestPath) || $this->manifestPath === 'default') {
            $path = $this->getConfig()['connections'][$this->getConnection()]['manifest_path'];

            if ($path === 'default') {
                $path = __DIR__ . '/../manifest/' . $this->getConnection();
            }

            $this->manifestPath = $path;
        }

        return $this->manifestPath;
    }

    /**
     * @return string
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function getConnection(): string
    {
        if (empty($this->connection)) {
            if (isset($this->getConfig()['default'])) {
                $this->setConnection($this->getConfig()['default']);
            } else {
                throw new ApiClientUnknownConnectionException();
            }
        }

        return $this->connection;
    }

    /**
     * @return mixed|string
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function getBaseUri()
    {
        if (empty($this->baseUri)) {
            $this->baseUri = $this->getConfig()['connections'][$this->getConnection()]['base_uri'];
        }

        return $this->baseUri;
    }

    /**
     * @return int
     * @throws ApiClientMissingConfigurationException
     */
    public function getTimeout(): int
    {
        if (empty($this->timeout)) {
            $this->timeout = (int)($this->getConfig()['timeout']);
        }

        return $this->timeout;
    }

    /**
     * @return array|null
     * @throws ApiClientMissingConfigurationException
     */
    public function getConfig(): ?array
    {
        if (empty($this->config)) {
            throw new ApiClientMissingConfigurationException();
        }

        return $this->config;
    }

    /**
     * @param string $connection
     *
     * @return ApiClient
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function setConnection(string $connection): ApiClient
    {
        if (!isset($this->config['connections'][$connection])) {
            throw new ApiClientUnknownConnectionException();
        }

        $this->connection = $connection;

        $connection = $this->config['connections'][$this->connection];

        $this->baseUri      = $connection['base_uri'];
        $this->manifestPath = $connection['manifest_path'];
        $this->apiKey       = $connection['api_key'];
        $this->secret       = $connection['secret'];

        unset($connection);

        $this->description = new Description($this->loadDescriptionConfig());

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * @return Description
     */
    public function getDescription(): Description
    {
        return $this->description;
    }

    /**
     * @param string $operation
     *
     * @return ApiClient
     */
    public function setOperation(string $operation): ApiClient
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return ApiClient
     */
    public function setParams(array $params): ApiClient
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param HandlerStack $handlerStack
     *
     * @return ApiClient
     */
    public function setHandlerStack(HandlerStack $handlerStack): ApiClient
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    /**
     * @return HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        return $this->handlerStack;
    }

    /**
     * @return bool
     */
    private function hasHandlerStack(): bool
    {
        return $this->handlerStack instanceof HandlerStack;
    }

    /**
     * @return ApiClient
     */
    public function clearHandlerStack(): ApiClient
    {
        $this->handlerStack = null;

        return $this;
    }

    /**
     * @return bool|null
     * @throws ApiClientMissingConfigurationException
     */
    public function getVerifySSLCertificate(): ?bool
    {
        if (empty($this->verifySSLCertificate)
            && isset($this->getConfig()['verify_ssl_certificate'])) {
            $this->verifySSLCertificate = $this->getConfig()['verify_ssl_certificate'];
        }

        return $this->verifySSLCertificate;
    }

}
