<?php
// backend/breco/bin/seed-test-user.php

// Creates the test user for E2E tests if it does not already exist.
// Usage: docker exec breco_backend php /app/bin/seed-test-user.php

declare(strict_types=1);

require_once '/app/vendor/autoload.php';

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;

// Bootstrap CakePHP config: only define constants if not already set by paths.php
defined('DS')                   || define('DS', DIRECTORY_SEPARATOR);
defined('ROOT')                 || define('ROOT', '/app');
defined('APP')                  || define('APP', '/app/src/');
defined('CONFIG')               || define('CONFIG', '/app/config/');
defined('WWW_ROOT')             || define('WWW_ROOT', '/app/webroot/');
defined('TMP')                  || define('TMP', '/app/tmp/');
defined('LOGS')                 || define('LOGS', '/app/logs/');
defined('CACHE')                || define('CACHE', '/app/tmp/cache/');
defined('CAKE_CORE_INCLUDE_PATH') || define('CAKE_CORE_INCLUDE_PATH', '/app/vendor/cakephp/cakephp');
defined('CORE_PATH')            || define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . '/');
defined('CAKE')                 || define('CAKE', CORE_PATH . 'src/');

require_once CORE_PATH . 'config/bootstrap.php';
require_once CONFIG . 'bootstrap.php';

// ─── Config ────────────────────────────────────────────────────────────────

const TEST_EMAIL     = 'test@test.com';
const TEST_PASSWORD  = 'Password123';
const TEST_FIRSTNAME = 'Test';
const TEST_LASTNAME  = 'User';
const TEST_PHONE     = '0607080910';

// ─── Seed ──────────────────────────────────────────────────────────────────

/** @var Connection $db */
$db = ConnectionManager::get('default');

// Check if user already exists
$existing = $db->execute(
    'SELECT id FROM users WHERE email = ?',
    [TEST_EMAIL]
)->fetch('assoc');

if ($existing) {
    echo '[seed] User ' . TEST_EMAIL . ' already exists (id=' . $existing['id'] . '), skipping.' . PHP_EOL;
    exit(0);
}

// Create user with verified email
$hash = password_hash(TEST_PASSWORD, PASSWORD_BCRYPT);
$now  = date('Y-m-d H:i:s');

$db->execute(
    'INSERT INTO users (email, email_verified, password, first_name, last_name, phone, created, modified)
     VALUES (?, 1, ?, ?, ?, ?, ?, ?)',
    [TEST_EMAIL, $hash, TEST_FIRSTNAME, TEST_LASTNAME, TEST_PHONE, $now, $now]
);

$id = $db->execute('SELECT LAST_INSERT_ID() as id')->fetch('assoc')['id'];
echo '[seed] User ' . TEST_EMAIL . ' created (id=' . $id . ').' . PHP_EOL;
exit(0);
