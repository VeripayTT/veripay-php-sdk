<?php

return [

    'environment' => env('VERIPAY_ENVIRONMENT', 'production'),

    'api_key' => env('VERIPAY_API_KEY'),

    'base_urls' => [
        'production' => env('VERIPAY_BASE_URL_PRODUCTION', 'https://api.veripay.us'),
        'staging'    => env('VERIPAY_BASE_URL_STAGING', 'https://staging-api.veripay.us'),
        'sandbox'    => env('VERIPAY_BASE_URL_SANDBOX', 'https://sandbox-api.veripay.us'),
    ],

    'log_requests' => env('VERIPAY_LOG_REQUESTS', false),
    'retry_times'  => env('VERIPAY_RETRY_TIMES', 3),
    'retry_delay'  => env('VERIPAY_RETRY_DELAY', 100), // milliseconds
];
