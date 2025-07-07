<?php

return [
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'user'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'https://syborg-qa9om.ondigitalocean.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'supports_credentials' => true,
];
