<?php
// backend/breco/bin/cleanup-jmeter-user.php

// Removes the JMeter test user after load tests.
// Usage: docker exec breco_backend php /app/bin/cleanup-jmeter-user.php
declare(strict_types=1);
require_once '/app/vendor/autoload.php';
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;

defined('DS')                     || define('DS', DIRECTORY_SEPARATOR);
defined('ROOT')                   || define('ROOT', '/app');
defined('APP')                    || define('APP', '/app/src/');
defined('CONFIG')                 || define('CONFIG', '/app/config/');
defined('WWW_ROOT')               || define('WWW_ROOT', '/app/webroot/');
defined('TMP')                    || define('TMP', '/app/tmp/');
defined('LOGS')                   || define('LOGS', '/app/logs/');
defined('CACHE')                  || define('CACHE', '/app/tmp/cache/');
defined('CAKE_CORE_INCLUDE_PATH') || define('CAKE_CORE_INCLUDE_PATH', '/app/vendor/cakephp/cakephp');
defined('CORE_PATH')              || define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . '/');
defined('CAKE')                   || define('CAKE', CORE_PATH . 'src/');
require_once CORE_PATH . 'config/bootstrap.php';
require_once CONFIG . 'bootstrap.php';

// ─── Cleanup ───────────────────────────────────────────────────────────────
/** @var Connection $db */
$db = ConnectionManager::get('default');

$db->execute(
    'DELETE FROM users WHERE email = ?',
    ['jmeter@breco.test']
);

echo '[cleanup-jmeter] User jmeter@breco.test deleted.' . PHP_EOL;
exit(0);
