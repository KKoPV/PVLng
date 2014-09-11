#!/usr/bin/php
<?php
/**
 * Main cron file
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

/**
 * Initialize
 */
ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
error_reporting(0);

setlocale(LC_NUMERIC, 'C');
iconv_set_encoding('internal_encoding', 'UTF-8');
mb_internal_encoding('UTF-8');
clearstatcache();

/**
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('BASE_DIR', dirname(__FILE__));
define('ROOT_DIR', BASE_DIR);
define('CONF_DIR', ROOT_DIR . DS . 'config');
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('APP_DIR',  ROOT_DIR . DS . 'frontend');

// Outside document root!
define('TEMP_DIR', ROOT_DIR . DS . 'tmp');

file_exists(CONF_DIR . DS . 'config.php') || die('Missing: ' . CONF_DIR . DS . 'config.php');
file_exists(CONF_DIR . DS . 'config.cron.php') || die('Missing: ' . CONF_DIR . DS . 'config.cron.php');

/**
 * Initialize Auto-Loader
 */
include LIB_DIR . DS . 'Loader.php';

Loader::register(
    array(
        'path'    => array(CORE_DIR, LIB_DIR, APP_DIR),
        'pattern' => array('%s.php'),
        'exclude' => array('contrib/')
    ),
    TEMP_DIR
);

$config = slimMVC\Config::getInstance()
        ->load(CONF_DIR . DS . 'config.default.php')
        ->load(CONF_DIR . DS . 'config.php')
        ->load(CONF_DIR . DS . 'config.cron.php', TRUE, 'Export');

/**
 * Initialize cache
 */
$cache = Cache::factory(
    array(
        'Token'     => 'PVLng',
        'Directory' => TEMP_DIR,
        'TTL'       => 86400
    ),
    $config->get('Cache')
);

// ---------------------------------------------------------------------------
// Let's go
// ---------------------------------------------------------------------------

$app = new slimMVC\App();

$app->config = $config;
$app->cache  = $cache;

/**
 * Database
 */
$c = $config->get('Database');
slimMVC\MySQLi::setCredentials(
    $c['host'], $c['username'], $c['password'], $c['database'], $c['port'], $c['socket']
);
slimMVC\MySQLi::$SETTINGS_TABLE = 'pvlng_config';

try {
    // Try connect to database
    $app->db = slimMVC\MySQLi::getInstance();
} catch (Exception $e) {
    die('Unable to connect to database!');
}

/**
 * Init Nested set for channel tree
 */
include LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

NestedSet::Init(array(
    'db'       => $app->db,
    'debug'    => true,
    'lang'     => 'en',
    'path'     => LIB_DIR . DS . 'contrib' . DS . 'messages',
    'db_table' => array (
        'tbl' => 'pvlng_tree',
        'nid' => 'id',
        'l'   => 'lft',
        'r'   => 'rgt',
        'mov' => 'moved',
        'pay' => 'entity'
    )
));

/**
 *
 */
function usage() {
    echo <<<EOT

Run defined tasks from cron

Usage: cron.php [options]

Options:
    -t  Test mode, no data will be changed
        Sets verbosity level to info
    -v  Verbosity level info
    -vv Verbosity level debug

Add this line to your crontab

* * * * * /path/to/your/public_html/cron.php

EOT;

    exit;
}

/**
 *
 */
function out( $level, $msg ) {
    $args = func_get_args();
    $level = array_shift($args);

    if ($level > VERBOSE) return;

    $msg = array_shift($args);
    vprintf('['.date('d-M H:i:s').'] '.$msg.PHP_EOL, $args);
}

/**
 *
 */
function curl( $options, &$result, &$info=array() ) {
    $ch = curl_init();

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);
    $info   = curl_getinfo($ch);
    $errno  = curl_errno($ch);
    $error  = curl_error($ch);

    curl_close($ch);

    // Debug curl
    out(1, 'Curl      : %d ms', $info['total_time']*1000);
    out(2, 'Curl info : %s', print_r($info, TRUE));

    // Curl error?
    if ($errno) {
        out(0, 'Curl error [%d] : %s', $errno, $error);
        return FALSE;
    }

    return TRUE;
}

// Command line parameters
extract(getopt('vtfh'), EXTR_PREFIX_ALL, 'p');

if (isset($p_h)) usage();

define('TESTMODE', isset($p_t));

$p_v = isset($p_v) ? (is_array($p_v)?count($p_v):1) : 0;
// Increase verbosity by 1 in test mode
TESTMODE && $p_v++;
define('VERBOSE', $p_v);

if (VERBOSE) {
    ini_set('display_errors', 1);
    error_reporting(-1);
}

if (TESTMODE) {
    out( 1, 'Test mode');
} else {
    // Give the other (data saving) cron jobs some more time to finish...
    out( 1, 'Snooze 5 seconds ...');
    sleep(5);
}

$minute = +date('i');

try {
    foreach ($config->get('Export') as $section) {

        $section = array_merge(
            array(
                'enabled' => 0,
                'name'    => '<unknown>',
                'handler' => '<handler unknown>',
                'runeach' => 1
            ),
            $section
        );

        out(1, '---------------------------------------------------------------');
        out(1, '--- %s (%s)', $section['name'], $section['handler']);
        out(1, '---------------------------------------------------------------');

        if ($section['enabled'] === TRUE OR
            TESTMODE AND $section['enabled'] === 0) {
            // Run in test mode at any minute or if forced flag was set...
            if (TESTMODE OR isset($p_f) OR $minute % $section['runeach'] == 0) {
                $file = ROOT_DIR . DS . 'cron' . DS . $section['handler'] . '.php';
                // Check for file exists only during test, in live don't check anymore
                if (TESTMODE AND !file_exists($file)) {
                    throw new Exception('Missing handler script: '.$file);
                }
                require $file;
            } else {
                out( 1, 'Skip, not that minute');
            }
        } else {
            out( 1, 'Skip, disabled');
        }
    }
} catch (Exception $e) {
    out(0, 'ERROR: %s', $e->getMessage());
}
