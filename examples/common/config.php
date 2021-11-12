<?php

return [
    'debug'                  => true,
    'timeout'                => 120,
    'log'                    => [
        'debug'                       => true,
        'log_file_name'               => 'apiclient.log',
        'log_file_path'               => 'logs',
        'log_request_before_response' => true,
        'log_app_name'                => 'ApiClient',
        'log_format'                  => null,
        // if null, a default format from GuzzleHttp\MessageFormatter class will be used
    ],
    'verify_ssl_certificate' => true,
    'default'                => 'partner_api_v1',
    'connections'            => [
        'partner_api_v1' => [
            'base_uri'               => 'https://api.demo.payassi.st',
            'manifest_path'          => 'default', // use manifest files stored within the package
            'api_key'                => '216299fc4cc950da',
            'secret'                 => 'demo_61b9b1fcd340cc461fbec3d7079',
            'additional_query_param' => [],
        ],
    ],
];
