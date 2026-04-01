<?php
// backend/breco/bin/seed-jmeter-user.php

// Creates the test user for JMeter load tests if it does not already exist.
// Usage: docker exec breco_backend php /app/bin/seed-jmeter-user.php
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

// ─── Config ────────────────────────────────────────────────────────────────
const JMETER_EMAIL     = 'jmeter@breco.test';
const JMETER_PASSWORD  = 'JMeter123!'; //NOSONAR
const JMETER_FIRSTNAME = 'JMeter';
const JMETER_LASTNAME  = 'Test';
const JMETER_PHONE     = '0600000000';

// ─── Seed ──────────────────────────────────────────────────────────────────
/** @var Connection $db */
$db = ConnectionManager::get('default');

$existing = $db->execute(
    'SELECT id FROM users WHERE email = ?',
    [JMETER_EMAIL]
)->fetch('assoc');

if ($existing) {
    echo '[seed-jmeter] User ' . JMETER_EMAIL . ' already exists (id=' . $existing['id'] . '), skipping.' . PHP_EOL;
    exit(0);
}

$hash = password_hash(JMETER_PASSWORD, PASSWORD_BCRYPT);
$now  = date('Y-m-d H:i:s');

$db->execute(
    'INSERT INTO users (email, email_verified, password, first_name, last_name, phone, created, modified)
     VALUES (?, 1, ?, ?, ?, ?, ?, ?)',
    [JMETER_EMAIL, $hash, JMETER_FIRSTNAME, JMETER_LASTNAME, JMETER_PHONE, $now, $now]
);

$id = $db->execute('SELECT LAST_INSERT_ID() as id')->fetch('assoc')['id'];
echo '[seed-jmeter] User ' . JMETER_EMAIL . ' created (id=' . $id . ').' . PHP_EOL;
exit(0);
