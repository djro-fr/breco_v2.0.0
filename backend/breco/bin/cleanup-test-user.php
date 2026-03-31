<?php
// backend/breco/bin/cleanup-test-user.php

// Removes the E2E test user after tests complete.
// Usage: docker exec breco_backend php /app/bin/cleanup-test-user.php

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

const TEST_EMAIL = 'test@test.com';

/** @var Connection $db */
$db = ConnectionManager::get('default');

$db->execute('DELETE FROM users WHERE email = ?', [TEST_EMAIL]);

echo '[cleanup] User ' . TEST_EMAIL . ' removed.' . PHP_EOL;
exit(0);
