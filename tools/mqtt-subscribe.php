<?php
/**
 * MQTT listener
 *
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2017 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */
$opts = getopt('s:p:q:vh');

$server  = array_key_exists('s', $opts) ? $opts['s'] : 'localhost';
$port    = array_key_exists('p', $opts) ? $opts['p'] : 1883;
$qos     = array_key_exists('q', $opts) ? +$opts['q'] : 0;
$verbose = +array_key_exists('v', $opts);

if (array_key_exists('h', $opts)) {
    echo PHP_EOL;
    echo 'MQTT listener for PVLng channel data', PHP_EOL;
    echo PHP_EOL;
    echo 'Send messages as {"data":"..."[,"timestamp":"..."]} to "pvlng/<API key>/data/<GUID>"', PHP_EOL;
    echo PHP_EOL;
    echo 'Usage: ', $argv[0], ' [options]', PHP_EOL;
    echo PHP_EOL;
    echo 'Options:', PHP_EOL;
    echo '    -s SERVER    MQTT server, default localhost', PHP_EOL;
    echo '    -p PORT      MQTT port, default 1883', PHP_EOL;
    echo '    -q QOS       Quality of service, default 0', PHP_EOL;
    echo '    -v           Verbose', PHP_EOL;
    echo '    -h           This help', PHP_EOL;
    echo PHP_EOL;
    exit;
}

if ($verbose) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

/**
 *
 */
require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'core', 'PVLng', 'PVLng.php']);

PVLng\PVLng::bootstrap();

$mqtt = new MQTT($server, $port);
$mqtt->qos = $qos;
$mqtt->verbose = $verbose;
$mqtt->run();
