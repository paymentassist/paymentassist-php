<?php

declare(strict_types=1);

namespace TestCase;

use Exception;
use GuzzleHttp\{Handler\MockHandler, HandlerStack, Psr7\Response, Utils};
use PaymentAssist\{ApiClient, Content};
use PaymentAssist\Exception\{ApiClientMissingConfigurationException,
    ApiClientMissingManifestFileException,
    ApiClientUnknownConnectionException
};
use PHPUnit\Framework\TestCase;
use TestCase\Traits\ApiClientTestHelpersTrait;


/**
 * Class ApiClientTest
 *
 * @package PaymentAssist\ApiClient
 */
final class ApiClientTest extends TestCase
{
    const UUID_REGEX = '/[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}/';
    const TOKEN      = 'bee615a8-9fbd-4da5-848e-951d6dc404e5';

    use ApiClientTestHelpersTrait;

    /**
     * Test subject
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->clearApiClientHandlerStack();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->clearApiClientHandlerStack();
        parent::tearDown();
    }

    private function clearApiClientHandlerStack(): void
    {
        if (isset($this->apiClient) && $this->apiClient instanceof ApiClient) {
            $this->apiClient->clearHandlerStack();
        }
        unset($this->apiClient);
    }

    /**
     * @return bool
     */
    private function mockApi(): bool
    {
        return !$this->realApi();
    }

    /**
     * @return bool
     */
    private function realApi(): bool
    {
        global $argv;

        return in_array('realapi', $argv);
    }

    public function testApiClientInstance(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());

        $this->assertInstanceOf(ApiClient::class, $this->apiClient);
    }

    /**
     * @throws ApiClientMissingConfigurationException
     */
    public function testApiClientInit(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());

        $this->assertIsArray($this->apiClient->getConfig());

        $this->assertArrayHasKey('log', $this->apiClient->getConfig());
        $this->assertArrayHasKey('verify_ssl_certificate', $this->apiClient->getConfig());
        $this->assertArrayHasKey('default', $this->apiClient->getConfig());
        $this->assertArrayHasKey('connections', $this->apiClient->getConfig());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     */
    public function testApiClientSetConfig(): void
    {
        $this->apiClient = ApiClient::instance();
        $this->assertIsArray($this->apiClient->getConfig());
        $this->apiClient->setConfig($this->getTestConfig());
        $this->assertIsArray($this->apiClient->getConfig());

        $this->assertArrayHasKey('log', $this->apiClient->getConfig());
        $this->assertArrayHasKey('verify_ssl_certificate', $this->apiClient->getConfig());
        $this->assertArrayHasKey('default', $this->apiClient->getConfig());
        $this->assertArrayHasKey('connections', $this->apiClient->getConfig());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function testApiClientSetConnectionError(): void
    {
        $this->expectException(ApiClientUnknownConnectionException::class);

        $this->apiClient = ApiClient::instance($this->getTestConfig());
        $this->apiClient->setConnection('none existent connection string');
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function testApiClientSetConnection(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());
        $this->apiClient->setConnection(ApiClient::PARTNER_API_V1);

        $this->assertEquals('7gx3c8el33tfhxio', $this->apiClient->getApiKey());
        $this->assertEquals('dev_4dakjdrpxp1pw4kgrrhq0c6k1jq', $this->apiClient->getSecret());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientUnknownConnectionException
     * @throws ApiClientMissingManifestFileException
     */
    public function testApiClientDefaultConnection(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());

        $this->assertEquals(ApiClient::PARTNER_API_V1, $this->apiClient->getConnection());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function testApiClientAccessEndpoint(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());
        $this->apiClient->setConnection(ApiClient::PARTNER_API_V1);

        if ($this->mockApi()) {
            $this->apiClient->setHandlerStack(
                HandlerStack::create(
                    new MockHandler([new Response(200, [], '{}')])
                )
            );
        }

        $response = $this->apiClient->GetAccountConfigurationDetails();

        $this->assertTrue($response->isOK());
        $this->assertFalse($response->isError());
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('OK', $response->getReason());
        $this->assertEquals('GET', $response->getRequestMethod());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function testApiClientGetAccountConfigurationDetailsSuccess(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());
        $this->apiClient->setConnection(ApiClient::PARTNER_API_V1);

        if ($this->mockApi()) {
            $this->apiClient->setHandlerStack(
                HandlerStack::create(
                    new MockHandler(
                        [
                            new Response(200, [], $this->getGetAccountConfigurationDetailsSuccessfulResponseJson()),
                        ]
                    )
                )
            );
        }

        $response = $this->apiClient->GetAccountConfigurationDetails();

        $this->assertTrue($response->isOK());
        $this->assertFalse($response->isError());
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('OK', $response->getReason());
        $this->assertEquals('GET', $response->getRequestMethod());

        $this->assertInstanceOf(Content::class, $response->getContents());
        $this->assertIsString((string)$response);
        $this->assertJson((string)$response);
        $this->assertJson($response->getContents()->toJson());
        $this->assertJson($response->getContentsJson());
        $this->assertIsArray($response->getContentsArray());

        $this->assertJsonStringEqualsJsonString(
            $this->getGetAccountConfigurationDetailsSuccessfulResponseJson(),
            $response->getContents()->getContent()->toJson()
        );

        $this->assertEquals('ACME Auto Services Ltd', $response->getContent()->get('data.legal_name'));
        $this->assertEquals('12-Payment (modified)', $response->getContent()->get('data.plans.0.name'));
        $this->assertEquals('ok', $response->getContent()->getStatus());
        $this->assertNull($response->getContent()->getMsg());
        $this->assertEquals('ACME Auto Services Ltd', $response->getContent()->getData()->getLegalName());
        $this->assertEquals(
            'ACME Auto Services Ltd',
            $response->getContent()->get('data')->getDisplayName()
        );
        $this->assertEquals(
            '8.50',
            $response->getContent()->get('data.plans')->get('0')->getCommissionRate()
        );

        $this->assertJson($response->toJson());
        $this->assertIsArray($response->toArray());
        $this->assertIsObject($response->getData());
        $this->assertIsArray($response->getHeaders());

        $this->assertEquals(
            '/account?api_key=7gx3c8el33tfhxio'
            . '&signature=cb98f44062e12efa59f71e01acb942059e6e2c54bd2009fac926db80784ded84',
            $response->getRequestTarget()
        );

        $this->assertEquals(
            '/account?api_key=7gx3c8el33tfhxio'
            . '&signature=cb98f44062e12efa59f71e01acb942059e6e2c54bd2009fac926db80784ded84',
            (string)$response->getRequestUri()
        );

        $this->assertJson($response->getOriginalData());
        $this->assertIsString($response->getCommandName());
        $this->assertEquals('GetAccountConfigurationDetails', $response->getCommandName());
        $this->assertEquals('1.1', $response->getProtocol());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     */
    public function testApiClientGetAccountConfigurationDetailsFailure(): void
    {
        $config = $this->getTestConfig();

        $config['connections'][ApiClient::PARTNER_API_V1]['base_uri'] = 'non_existent_uri';

        $this->apiClient = ApiClient::instance($config);
        $this->apiClient->setConnection(ApiClient::PARTNER_API_V1);

        $response = $this->apiClient->GetAccountConfigurationDetails();

        $this->assertFalse($response->isOK());
        $this->assertTrue($response->isError());
        $this->assertEquals(0, $response->getStatus());
        $this->assertEmpty($response->getReason());
        $this->assertEquals('GET', $response->getRequestMethod());
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientUnknownConnectionException
     * @throws ApiClientMissingManifestFileException
     * @throws Exception
     */
    public function testApiClientGetAccountConfigurationWrongCredentials(): void
    {
        $config = $this->getTestConfig();

        $config['connections'][ApiClient::PARTNER_API_V1]['api_key'] = 'wrong_api_key';

        $response = ApiClient::instance($config)
            ->setConnection(ApiClient::PARTNER_API_V1)
            ->GetAccountConfigurationDetails();

        $this->assertFalse($response->isOK());
        $this->assertTrue($response->isError());
        $this->assertEquals(401, $response->getStatus());
        $this->assertEquals('Unauthorized', $response->getReason());
        $this->assertEquals('GET', $response->getRequestMethod());
    }

    /**
     * @return string
     */
    private function getGetAccountConfigurationDetailsSuccessfulResponseJson(): string
    {
        return '{
    "status": "ok",
    "msg": null,
    "data": {
        "legal_name": "ACME Auto Services Ltd",
        "display_name": "ACME Auto Services Ltd",
        "plans": [
            {
                "plan_id": 7,
                "name": "12-Payment (modified)",
                "instalments": 1,
                "deposit": true,
                "apr": "5.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "8.50"
            },
            {
                "plan_id": 9,
                "name": "Single Payment",
                "instalments": 1,
                "deposit": true,
                "apr": "0.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "8.50"
            },
            {
                "plan_id": 6,
                "name": "3-Payment",
                "instalments": 3,
                "deposit": true,
                "apr": "0.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "8.50"
            },
            {
                "plan_id": 1,
                "name": "4-Payment",
                "instalments": 4,
                "deposit": true,
                "apr": "0.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "5.00"
            },
            {
                "plan_id": 11,
                "name": "6-Payment",
                "instalments": 6,
                "deposit": true,
                "apr": "0.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "6.50"
            },
            {
                "plan_id": 10,
                "name": "9-Payment",
                "instalments": 9,
                "deposit": true,
                "apr": "0.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "7.00"
            },
            {
                "plan_id": 20,
                "name": "9-Payment \/ 25% Deposit",
                "instalments": 9,
                "deposit": true,
                "apr": "0.000",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "12.00"
            },
            {
                "plan_id": 13,
                "name": "Interest Bearing",
                "instalments": 36,
                "deposit": false,
                "apr": "12.900",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 300000,
                "commission_rate": "5.00"
            },
            {
                "plan_id": 14,
                "name": "Interest Bearing",
                "instalments": 48,
                "deposit": false,
                "apr": "14.900",
                "frequency": "monthly",
                "min_amount": null,
                "max_amount": 500000,
                "commission_rate": "8.50"
            }
        ]
    }
}';
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     * @throws Exception
     */
    public function testApiClientBeginApplicationSuccess(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());
        $this->apiClient->setConnection(ApiClient::PARTNER_API_V1);

        if ($this->mockApi()) {
            $this->apiClient->setHandlerStack(
                HandlerStack::create(
                    new MockHandler(
                        [
                            new Response(200, [], $this->getBeginApplicationSuccessfulResponseJson()),
                        ]
                    )
                )
            );
        }

        $response = $this->apiClient->BeginApplication(
            [
                'order_id'    => $this->getRandomInt(),
                'amount'      => $this->getRandomInt(50000, 250000),
                'email'       => 'api_test@payment-assist.co.uk',
                'f_name'      => 'John',
                's_name'      => 'Smith',
                'addr1'       => '42 Some Hidden Road',
                'postcode'    => 'SL5 3SE',
                'failure_url' => 'http://api.pa.local',
                'success_url' => 'http://api.pa.local',
                'webhook_url' => 'http://api.pa.local',
                'plan_id'     => 1,
                'reg_no'      => 'RE66GNO',
                'description' => 'Some example description',
                'expiry'      => 86400,
                'addr2'       => '13 Mysterious Gardens',
                'addr3'       => '',
                'town'        => 'Windsor',
                'county'      => 'Berkshire',
                'telephone'   => '07521347822',
                'send_email'  => false,
                'send_sms'    => false,
                'multi_plan'  => false,
                'qr_code'     => false,
            ]
        );

        $this->assertTrue($response->isOK());
        $this->assertFalse($response->isError());
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('OK', $response->getReason());
        $this->assertEquals('POST', $response->getRequestMethod());

        $this->assertInstanceOf(Content::class, $response->getContents());
        $this->assertIsString((string)$response);
        $this->assertJson((string)$response);
        $this->assertJson($response->getContents()->toJson());
        $this->assertJson($response->getContentsJson());
        $this->assertIsArray($response->getContentsArray());

        $token = $this->realApi() ? $response->getContent()->getData()->getToken() : self::TOKEN;

        $this->assertMatchesRegularExpression(self::UUID_REGEX, $token);

        $this->assertJsonStringEqualsJsonString(
            $this->getBeginApplicationSuccessfulResponseJson($token),
            $response->getContent()->toJson()
        );

        $this->assertEquals('ok', $response->getContent()->getStatus());
        $this->assertNull($response->getContent()->getMsg());

        $this->assertJson($response->toJson());
        $this->assertIsArray($response->toArray());
        $this->assertIsObject($response->getData());
        $this->assertIsArray($response->getHeaders());

        $this->assertEquals('/begin', $response->getRequestTarget());
        $this->assertEquals('/begin', (string)$response->getRequestUri());

        $this->assertJson($response->getOriginalData());
        $this->assertIsString($response->getCommandName());
        $this->assertEquals('BeginApplication', $response->getCommandName());
        $this->assertEquals('1.1', $response->getProtocol());
    }

    /**
     * @param string|null $token
     *
     * @return string
     */
    private function getBeginApplicationSuccessfulResponseJson(?string $token = null): string
    {
        $token = $token ?? self::TOKEN;

        return Utils::jsonEncode(
            [
                'status' => 'ok',
                'msg'    => null,
                'data'   => [
                    'token' => $token,
                    'url'   => 'http://app.pa.local/loans/apply/' . $token,
                ],
            ]
        );
    }

    /**
     * @throws ApiClientMissingConfigurationException
     * @throws ApiClientMissingManifestFileException
     * @throws ApiClientUnknownConnectionException
     * @throws Exception
     */
    public function testApiClientGetPlanBreakdownSuccess(): void
    {
        $this->apiClient = ApiClient::instance($this->getTestConfig());
        $this->apiClient->setConnection(ApiClient::PARTNER_API_V1);

        if ($this->mockApi()) {
            $this->apiClient->setHandlerStack(
                HandlerStack::create(
                    new MockHandler(
                        [
                            new Response(200, [], $this->getGetPlanBreakdownSuccessfulResponseJson()),
                        ]
                    )
                )
            );
        }

        $response = $this->apiClient->GetPlanBreakdown(
            [
                'amount'      => 185460,
                'plan_id'     => 1,
                'plan_length' => 4,
            ]
        );

        $this->assertTrue($response->isOK());
        $this->assertFalse($response->isError());
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('OK', $response->getReason());
        $this->assertEquals('POST', $response->getRequestMethod());

        $this->assertInstanceOf(Content::class, $response->getContents());
        $this->assertIsString((string)$response);
        $this->assertJson((string)$response);
        $this->assertJson($response->getContents()->toJson());
        $this->assertJson($response->getContentsJson());
        $this->assertIsArray($response->getContentsArray());

        $this->assertJsonStringEqualsJsonString(
            $this->getGetPlanBreakdownSuccessfulResponseJson(),
            $response->getContent()->toJson()
        );

        $this->assertEquals('ok', $response->getContent()->getStatus());
        $this->assertNull($response->getContent()->getMsg());

        $this->assertJson($response->toJson());
        $this->assertIsArray($response->toArray());
        $this->assertIsObject($response->getData());
        $this->assertIsArray($response->getHeaders());

        $this->assertEquals('/plan', $response->getRequestTarget());
        $this->assertEquals('/plan', (string)$response->getRequestUri());

        $this->assertJson($response->getOriginalData());
        $this->assertIsString($response->getCommandName());
        $this->assertEquals('GetPlanBreakdown', $response->getCommandName());
        $this->assertEquals('1.1', $response->getProtocol());
    }

    /**
     * @return string
     */
    private function getGetPlanBreakdownSuccessfulResponseJson(): string
    {
        return '{
    "status": "ok",
    "msg": null,
    "data": {
        "plan": "4-Payment",
        "amount": 185460,
        "interest": 0,
        "repayable": 185460,
        "summary": "This loan comprises of a deposit today of \u00a3463.65, followed by 3 monthly payment(s) of \u00a3463.65. The final payment will be on 15\/12\/2021.",
        "schedule": [
            {
                "amount": 46365,
                "date": "2021-09-15"
            },
            {
                "amount": 46365,
                "date": "2021-10-15"
            },
            {
                "amount": 46365,
                "date": "2021-11-15"
            },
            {
                "amount": 46365,
                "date": "2021-12-15"
            }
        ]
    }
}';
    }
}
