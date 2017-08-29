#!/usr/bin/env php
<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 * MQTT listener
 */
$opts = getopt('s:p:vh');

$server  =  array_key_exists('s', $opts) ? $opts['s'] : 'localhost';
$port    =  array_key_exists('p', $opts) ? $opts['p'] : 1883;
$verbose = +array_key_exists('v', $opts);

if ($verbose) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

if (array_key_exists('h', $opts)) {
    echo PHP_EOL;
    echo 'MQTT listener for PVLng channel data', PHP_EOL;
    echo PHP_EOL;
    echo 'Send your messages as {"data":"..."[,"timestamp":"..."]} to "pvlng/<API key>/data/<GUID>"', PHP_EOL;
    echo PHP_EOL;
    echo 'Usage: ', $argv[0], ' [options]', PHP_EOL;
    echo PHP_EOL;
    echo 'Options:', PHP_EOL;
    echo '    -s SERVER    MQTT server, default localhost', PHP_EOL;
    echo '    -p PORT      MQTT port, default 1883', PHP_EOL;
    echo '    -v           Verbose', PHP_EOL;
    echo '    -h           This help', PHP_EOL;
    echo PHP_EOL;
    exit;
}

/**
 *
 */
require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'core', 'Core', 'PVLng.php']);

Core\PVLng::bootstrap();

$mqtt = new MQTT($server, $port);
$mqtt->run($verbose);
