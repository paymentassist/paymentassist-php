<?php

/**
 * Payment Assist PHP-SDK
 * v2.0.0
 */

namespace PaymentAssist;

use GuzzleHttp\Client;
use GuzzleHttp\Command\{Exception\CommandException, Guzzle\Description, Guzzle\GuzzleClient};
use PaymentAssist\Exception\{ApiClientMissingConfigurationException,
    ApiClientMissingManifestFileException,
    ApiClientUnknownConnectionException,
    ApiClientUnknownOperationException,
};
use PaymentAssist\Traits\{ApiClientTrait, ApiClientUtilTrait};
use stdClass;
use Throwable;

/**
 * Class ApiClient
 *
 * @package PaymentAssist\ApiClient
 *
 * @method Response GetAccountConfigurationDetails(mixed[] $args = [])
 * @method Response BeginApplication(mixed[] $args = [])
 * @method Response UploadInvoiceToCompletedApplication(mixed[] $args = [])
 * @method Response GetPlanBreakdown(mixed[] $args = [])
 * @method Response ObtainPreApproval(mixed[] $args = [])
 * @method Response GetApplicationStatus(mixed[] $args = [])
 * @method Response UpdateExistingApplication(mixed[] $args = [])
 */
final class ApiClient
{
    use ApiClientUtilTrait, ApiClientTrait;

    public const USER_AGENT = 'Payment Assist PHP Client';
    public const VERSION    = 'v2.0.0';

    public const MANIFEST_FILE  = 'manifest.json';
    public const PARTNER_API_V1 = 'partner_api_v1';

    /** @var ApiClient */
    private static $instance = null;

    /** @var array|null */
    private $config;

    /** @var Description */
    private $description;

    /** @var string */
    private $operation;

    /** @var array */
    private $params;

    /**
     * @param array|null $config
     */
    private function __construct(?array $config = null)
    {
        $this->config           = $config;
        $this->resultSerializer = $this->getResultSerializer();
    }

    /**
     * @param array|null $config
     *
     * @return ApiClient
     */
    public static function instance(?array $config = null): ApiClient
    {
        if (self::$instance == null) {
            self::$instance = new self($config);
        }

        if ($config !== null) {
            self::$instance->setConfig($config);
        }

        return self::$instance;
    }

    /**
     * Creates and executes a command for an operation by name.
     *
     * @param string $name Name of the command to execute.
     * @param array  $args Arguments to pass to the getCommand method.
     *
     * @return Response
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     * @throws ApiClientUnknownOperationException
     * @throws Throwable
     */
    public function __call(string $name, array $args)
    {
        $args = $args[0] ?? [];

        return $this->execute($name, $args);
    }

    /**
     * @param string $operation
     * @param array  $args
     *
     * @return Response
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     * @throws ApiClientUnknownOperationException
     * @throws Throwable
     */
    private function execute(string $operation = '', array $args = []): Response
    {
        if (!$this->description->hasOperation($operation)) {
            throw new ApiClientUnknownOperationException();
        }

        $this->setOperation($operation);
        $this->setParams($this->makeParams($args));

        $error  = new stdClass();
        $result = null;
        try {
            $result = ResponseStruct::instance(
                (
                new GuzzleClient(
                    new Client(
                        array_merge(
                            [
                                'base_uri'        => $this->getBaseUri(),
                                'headers'         => ['User-Agent' => $this->getUserAgentName()],
                                'timeout'         => $this->getTimeout(),
                                'allow_redirects' => true,
                            ],
                            $this->hasHandlerStack() ? ['handler' => $this->getHandlerStack()] : []
                        )
                    ),
                    $this->getDescription(),
                    $this->getRequestSerializer(),
                    $this->getResultSerializer()
                ))->{$this->getOperation()}(
                        $this->getParams()
                    )
            );
        } catch (CommandException $e) {
            $error->message = $e->getMessage();

            if ($e->getRequest() !== null) {
                $error->request_target = $e->getRequest()->getRequestTarget();
                $error->request_uri    = $e->getRequest()->getUri();
                $error->request_method = $e->getRequest()->getMethod();
                $error->protocol       = $e->getRequest()->getProtocolVersion();
                $error->headers        = $e->getRequest()->getHeaders();
                $error->body           = $e->getRequest()->getBody();
            }

            if ($e->getResponse() !== null) {
                $error->status         = $e->getResponse()->getStatusCode();
                $error->reason_phrase  = $e->getResponse()->getReasonPhrase();
                $error->request_method = $this->getDescription()->getOperation($this->operation)->getHttpMethod();
                $error->protocol       = $e->getResponse()->getProtocolVersion();
                $error->headers        = $e->getResponse()->getHeaders();
                $error->body           = $e->getResponse()->getBody()->getContents();
            }

            $error->command = $e->getCommand()->getName();
            $error->params  = $this->buildParamsArray();

            $error = ResponseStruct::instance($error);
        }

        return new Response($result ?? $error);
    }
}
