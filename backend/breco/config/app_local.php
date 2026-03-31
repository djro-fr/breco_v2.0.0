<?php
// backend/breco/config/app_local.php
use function Cake\Core\env;
return [
    'debug' => (bool) env('DEBUG', false),
    'Error' => [
        'errorLevel' => E_ALL & ~E_USER_DEPRECATED,
    ],
    'Security' => [
        'salt' => env('SECURITY_SALT', ''),
    ],
    'Datasources' => [
        'default' => [
            'host' => env('MYSQL_HOST', 'mysql'),
            'port' => env('MYSQL_PORT', '3306'),
            'username' => env('MYSQL_USER', ''),
            'password' => env('MYSQL_PASSWORD', ''),
            'database' => env('MYSQL_DB', 'breco_db'),
            'url' => env('DATABASE_URL', null),
        ],
        'test' => [
            'url' => env('DATABASE_TEST_URL', 'sqlite://127.0.0.1/tmp/tests.sqlite'),
        ],
    ],
    'EmailTransport' => [
        'default' => [
            'host' => env('EMAIL_HOST', 'mailhog'),
            'port' => (int) env('EMAIL_PORT', 1025),
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];
