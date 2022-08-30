<?php

declare(strict_types=1);

namespace TestCase;

use Exception;
use PaymentAssist\Helpers\Helpers;
use PHPUnit\Framework\TestCase;

/**
 * Class HelpersTest
 *
 * @package PaymentAssist\ApiClient
 */
final class HelpersTest extends TestCase
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
    public function testGenerateSignature(): void
    {
        $credentials = [
            'api_key' => '7de165dae29140c9',
            'secret'  => 'dev_3fe45524d16a36533d92cfde86c',
        ];

        $generatedSignature = Helpers::generateSignature(
            [
                'param1' => 'value1',
                'param2' => 'value2',
                'param3' => 'value3',
            ],
            $credentials
        );

        $this->assertEquals(
            'dc5b10eec2c2f6a73a779e38fd84a39186787ff791612e8c35413772490dff2c',
            $generatedSignature
        );
    }

    public function testCamel2snake(): void
    {
        $this->assertEquals(
            'an_example_of_snake_notation',
            Helpers::camel2snake('anExampleOfSnakeNotation')
        );
        $this->assertEquals(
            'another_example_of_snake_notation',
            Helpers::camel2snake('anotherExampleOfSnakeNOTATION')
        );
        $this->assertEquals(
            'and_one_more_example',
            Helpers::camel2snake('andOneMoreExample')
        );
    }
}
