<?php

namespace TestCase\Traits;

use Exception;

trait ApiClientTestHelpersTrait
{
    /**
     * @return array
     */
    private function getTestConfig(): array
    {
        return [
            'debug'                  => true,
            'timeout'                => 120,
            'log'                    => [
                'debug'                       => true,
                'log_file_name'               => 'apiclient.log',
                'log_file_path'               => __DIR__ . '/../../logs',
                'log_request_before_response' => true,
                'log_app_name'                => 'ApiClient',
                'log_format'                  => null,
            ],
            'verify_ssl_certificate' => true,
            'default'                => 'partner_api_v1',
            'connections'            => [
                'partner_api_v1' => [
                    'base_uri'               => 'http://api.pa.local',
                    'manifest_path'          => 'default',
                    'api_key'                => '7gx3c8el33tfhxio',
                    'secret'                 => 'dev_4dakjdrpxp1pw4kgrrhq0c6k1jq',
                    'additional_query_param' => [],
                ],
            ],
        ];
    }

    /**
     * @param int|null $min
     * @param int|null $max
     *
     * @return int
     * @throws Exception
     */
    private function getRandomInt(int $min = null, int $max = null): int
    {
        $min = $min ?? PHP_INT_MIN;
        $max = $max ?? PHP_INT_MAX;

        return abs(random_int($min, $max));
    }

}
