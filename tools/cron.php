#!/usr/bin/env php
<?php
/**
 * Main cron file
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @codingStandardsIgnoreFile
 */

##############################################################################

is_file(__DIR__.'/.paused') && exit(254);

/**
 *
 */
function usage()
{
    $f = __FILE__;
    echo <<<EOT

Run defined tasks from cron

Usage: $f [options]

Options:
    -c  Configuration file relative to config dir., defaults to config/cron.yaml
    -t  Test mode, no data will be changed; sets verbosity level to info
    -v  Verbosity level info
    -vv Verbosity level debug

Add this lines to your crontab:

# Run cron script each minute
* * * * * $f

EOT;

    exit;
}

/**
 *
 */
function out($level)
{
    $args  = func_get_args();
    $level = array_shift($args);

    global $VERBOSE;

    if ($level > $VERBOSE) return;

    $msg = count($args) ? array_shift($args) : str_repeat('-', 63);
    vprintf(date('[d-M H:i:s] ') . $msg . PHP_EOL, $args);
}

/**
 *
 */
function okv($level, $key, $value)
{
    if (is_scalar($value)) {
        out($level, '%-20s = %s', $key, $value);
    } else {
        out($level, $key . PHP_EOL . print_r($value, true));
    }
}

/**
 *
 */
function curl($options, &$result, &$info)
{
    $ch = curl_init();

    $options[CURLOPT_RETURNTRANSFER] = 1;

    curl_setopt_array($ch, $options);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);
    $info   = curl_getinfo($ch);
    $errno  = curl_errno($ch);
    $error  = curl_error($ch);

    curl_close($ch);

    // Debug curl
    okv(1, 'cUrl total time', $info['total_time'] . 's');
    okv(1, 'cUrl bytes up / down', $info['size_upload'] . ' / ' . $info['size_download']);
    okv(2, 'cUrl info', $info);

    // Curl error?
    if ($errno) {
        okv(0, 'Curl error', '['.$errno.'] '.$error);
        okv(0, 'Parameters', $options);
        return false;
    }

    return true;
}

##############################################################################

// Command line parameters
// -f is undocumentd and ignore minutes interval, run always
extract(getopt('c:fvth'), EXTR_PREFIX_ALL, 'param');

if (isset($param_h)) {
    usage();
}

$TESTMODE = isset($param_t);
$VERBOSE  = isset($param_v) ? (is_array($param_v) ? count($param_v) : 1) : 0;
$FORCE    = isset($param_f);
$CONFFILE = isset($param_c) ? $param_c : 'cron.yaml';

// Increase verbosity by 1 in test mode
$TESTMODE && $VERBOSE++;

ini_set('display_startup_errors', !$VERBOSE);
ini_set('display_errors', !$VERBOSE);
error_reporting($VERBOSE ? -1 : 0);

$TESTMODE && okv( 1, 'Mode', 'TEST');

/**
 * Initialize Auto-Loader
 */
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'bootstrap.php']);

use Core\PVLng;

$loader = PVLng::bootstrap();
Loader::register($loader, PVLng::$TempDir);

/**
 * Config file
 */
$CONFFILE = PVLng::pathRoot('config', $CONFFILE);
okv(1, 'Config file', $CONFFILE);

try {
    // Load into "Cron" namespace
    $config = PVLng::getConfig()->loadNamespace('Cron', $CONFFILE);
} catch (Exception $e) {
    die($e->getMessage());
}

/**
 * Fork here child processes for each section
 */
$minute   = +date('i');
$sections = $config->get('Cron');
$cnt      = count($sections);

$id = -1;  // Parent will NOT change the $id

for ($i=0; $i<$cnt; $i++) {

   switch ($pid = pcntl_fork()) {

      default:
         // parent
         pcntl_waitpid($pid, $status);
         break;

      case -1:
         // fail
         die('Fork failed');
         break;

      case 0:
         // child: Break out to loop and set section $id to process
         $id = $i;
         break 2;
   }
}

// parent process finished loop
if ($id == -1) exit;

// Child process, need to recreate database!
PVLng::setDatabase(true);

// Collect outputs to show at once
ob_start();

$section = array_merge(
    [
        'handler' => '<handler unknown>',
        'enabled' => false,
        'name'    => '<unknown>',
        'each'    => 1
    ],
    $sections[$id]
);

$file = PVLng::pathRoot('tools', 'cron', $section['handler'].'.php');

// Check for file exists only during test, in live don't check anymore
if ($TESTMODE && !file_exists($file)) {
    throw new Exception('Missing handler script: '.$file);
}

out(1);
out(1, '[%d] %s - %s', ($id+1), $section['handler'], $section['name']);
out(1);

try {
    if (($section['enabled'] === true) || $TESTMODE && ($section['enabled'] === 0)) {
        // Run in test mode at any minute or if forced flag was set...
        if ($TESTMODE || $FORCE || (($minute % $section['each']) === 0)) {
            unset($section['enabled'], $section['name'], $section['handler']);
            // Run handler
            require $file;
        } else {
            out(1, 'Skip, not that minute');
        }
    } else {
        out(1, 'Skip, disabled');
    }
} catch (Exception $e) {
    out(0, 'ERROR: %s', $e->getMessage());
}
