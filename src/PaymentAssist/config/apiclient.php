<?php

return [
    'ApiClient' => [
        'debug'                  => env('apiclient_debug', false),
        'timeout'                => env('apiclient_timeout', 30),
        'log'                    => [
            'debug'                       => env('apiclient_log_debug', false),
            'log_file_name'               => env('apiclient_log_log_file_name', 'apiclient.log'),
            'log_file_path'               => env('apiclient_log_log_file_path', 'PATH-TO-YOUR-LOGS-FOLDER'),
            'log_request_before_response' => env('apiclient_log_log_request_before_response', false),
            'log_app_name'                => env('apiclient_log_log_app_name', 'pa_partner_api'),
        ],
        'verify_ssl_certificate' => env('apiclient_verify_ssl_certificate', true),
        'default'                => 'partner_api_v1',
        'connections'            => [
            'partner_api_v1' => [
                'base_uri'               => env(
                    'apiclient_connections_partner_api_v1_base_uri',
                    'https://api.v1.payment-assist.co.uk'
                ),
                'manifest_path'          => env(
                    'apiclient_connections_partner_api_v1_manifest_path',
                    'default'
                ),
                'api_key'                => env(
                    'apiclient_connections_partner_api_v1_api_key',
                    'YOUR-KEY'
                ),
                'secret'                 => env(
                    'apiclient_connections_partner_api_v1_secret',
                    'YOUR-SECRET'
                ),
                'additional_query_param' => [],
            ],
        ],
    ],
];
