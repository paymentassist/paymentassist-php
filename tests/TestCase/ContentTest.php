<?php

declare(strict_types=1);

namespace TestCase;

use Exception;
use PaymentAssist\Content;
use PaymentAssist\Exception\ApiClientContentUnknownMethod;
use PHPUnit\Framework\TestCase;

/**
 * Class ContentTest
 *
 * @package PaymentAssist\ApiClient
 */
final class ContentTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testEmptyContent(): void
    {
        $content = Content::make(null);

        $this->assertInstanceOf(Content::class, $content);
        $this->assertIsArray($content->toArray());
        $this->assertEmpty($content->toArray());
        $this->assertEmpty($content->toJson());
        $this->assertEmpty((string)$content);
    }

    /**
     * @throws Exception
     */
    public function testJsonContent(): void
    {
        $content = Content::make(
            [
                'parameter1' => 'value1',
                'parameter2' => 'value2',
                'parameter3' => 'value3',
                'nested1'    => [
                    'nestedParameter1' => 'nestedValue1',
                    'nestedParameter2' => 'nestedValue2',
                    'nestedParameter3' => 'nestedValue3',
                ],
            ]
        );

        $this->assertInstanceOf(Content::class, $content);
        $this->assertJson($content->toJson());
        $this->assertJsonStringEqualsJsonString(
            '{
    "parameter1": "value1",
    "parameter2": "value2",
    "parameter3": "value3",
    "nested1": {
        "nestedParameter1": "nestedValue1",
        "nestedParameter2": "nestedValue2",
        "nestedParameter3": "nestedValue3"
    }
}',
            $content->toJson()
        );
    }

    public function testUnknownGetMethodCall(): void
    {
        $content = Content::make();

        $this->expectException(ApiClientContentUnknownMethod::class);

        $content->{'getSomeUnknownValueAndRaiseAnException'}();
    }

    public function testUnknownMethodCall(): void
    {
        $content = Content::make();

        $this->expectException(ApiClientContentUnknownMethod::class);

        $content->{'totallyWrongMethodName'}('some_argument');
    }
}
