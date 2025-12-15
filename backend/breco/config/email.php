<?php
// backend\breco\config\email.php
// Email configuration for the Breco backend application

return [
    'EmailTransport' => [
        'default' => [
            'className' => 'Smtp',
            'host' => env('EMAIL_HOST', 'mailhog'),
            'port' => env('EMAIL_PORT', 1025),
            'timeout' => 30,
            'username' => env('EMAIL_USERNAME', ''),
            'password' => env('EMAIL_PASSWORD', ''),
            'tls' => env('EMAIL_TLS', false),
        ],
    ],

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => [env('EMAIL_FROM', 'noreply@breco.fr') => 'Breco'],
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
        ],
    ],
];
