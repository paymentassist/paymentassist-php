<?php

namespace PaymentAssist\Traits;

use Exception;
use Guzzle\Service\Loader\JsonLoader;
use GuzzleHttp\Command\Guzzle\Operation;
use GuzzleHttp\Command\Guzzle\RequestLocation\QueryLocation;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PaymentAssist\{Helpers\Helpers, QuerySerializer, RequestSerializer, ResultSerializer};
use PaymentAssist\Exception\{ApiClientMissingConfigurationException,
    ApiClientMissingManifestFileException,
    ApiClientUnknownConnectionException
};
use Psr\Log\LogLevel;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;

/**
 * trait ApiClientUtilTrait
 *
 * @package PaymentAssist\ApiClient
 */
trait ApiClientUtilTrait
{
    /** @var RequestSerializer */
    private $requestSerializer;

    /** @var ResultSerializer */
    private $resultSerializer;

    /**
     * @param array $params
     *
     * @return array
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    private function makeParams(array $params): array
    {
        return array_merge(
            [
                'api_key'   => $this->getApiKey(),
                'signature' => Helpers::generateSignature(
                    $params,
                    [
                        'secret' => $this->getSecret(),
                    ]
                ),
            ],
            $params
        );
    }

    /**
     * @return string
     */
    private function getUserAgentName(): string
    {
        return self::USER_AGENT . ' ' . self::VERSION;
    }

    /**
     * @return array
     */
    private function buildParamsArray(): array
    {
        $values = [];
        foreach ($this->getParams() as $name => $value) {
            $values[$name] = [
                'request_value' =>
                    [
                        'type'  => get_debug_type($value),
                        'value' => var_export($value, true),
                    ],
            ];
        }

        return array_merge_recursive($this->getOperationParams(), $values);
    }

    /**
     * @return RequestSerializer
     */
    public function getRequestSerializer(): RequestSerializer
    {
        if (!$this->requestSerializer instanceof RequestSerializer) {
            $this->requestSerializer = new RequestSerializer($this->description, [
                'query' =>
                    new QueryLocation('query', new QuerySerializer()),
            ]);
        }

        return $this->requestSerializer;
    }

    /**
     * @return ResultSerializer
     */
    public function getResultSerializer(): ResultSerializer
    {
        if (!$this->resultSerializer instanceof ResultSerializer) {
            $this->resultSerializer = new ResultSerializer();
        }

        return $this->resultSerializer;
    }

    /**
     * @param string $operation
     *
     * @return Operation|Operation[]|null
     */
    /**
     * @param string $operation
     *
     * @return Operation|Operation[]|null
     */
    public function getOperations(string $operation = '')
    {
        if (!empty($this->description)) {
            if (!empty($operation)) {
                return $this->description->getOperation($operation);
            }

            return $this->description->getOperations();
        }

        return null;
    }

    /**
     * @param string|null $operation
     *
     * @return array
     */
    public function getOperationParams(string $operation = null): array
    {
        $operation = $operation ?? $this->operation;
        if ($operation !== null) {
            $op = $this->getOperations($operation);
            if ($op instanceof Operation) {
                return $op->toArray()['parameters'];
            }
        }

        return [];
    }

    /**
     * @return array
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     * @throws Exception
     */
    protected function loadDescriptionConfig(): array
    {
        $locator    = new FileLocator([$this->getManifestPath()]);
        $jsonLoader = new JsonLoader($locator);

        try {
            $descriptionConfig = $jsonLoader->load($locator->locate(self::MANIFEST_FILE));
        } catch (FileLocatorFileNotFoundException $e) {
            throw new ApiClientMissingManifestFileException();
        }

        return $descriptionConfig;
    }

    /**
     * @return array|null
     */
    private function fetchConfig(): ?array
    {
        $file = Helpers::searchFile(self::APP_ROOT, self::CONFIG_FILE);

        return $file ? (include($file))['ApiClient'] : null;
    }

    /**
     * @throws ApiClientMissingConfigurationException
     */
    private function setUpLogging(): void
    {
        if ($this->getConfig()['debug']) {
            $logger = new Logger($this->getConfig()['log']['log_app_name']);
            $logger->pushHandler(
                new StreamHandler(
                    $this->getConfig()['log']['log_file_path']
                    . DIRECTORY_SEPARATOR
                    . $this->getConfig()['log']['log_file_name']
                )
            );

            if (!$this->hasHandlerStack()) {
                $this->setHandlerStack(HandlerStack::create());
            }
            $stack = $this->getHandlerStack();
            $stack->push(
                Middleware::log(
                    $logger,
                    new MessageFormatter($this->getConfig()['log']['log_format']),
                    $this->getConfig()['log']['debug'] ? LogLevel::DEBUG : LogLevel::INFO
                )
            );
        }
    }
}
