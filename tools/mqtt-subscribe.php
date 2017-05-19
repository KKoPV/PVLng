<?php
/**
 *
 */
function e($level)
{
    global $verbose;
    $args = func_get_args();
    $level = array_shift($args);
    if ($level <= $verbose) {
        echo date("[c] "), implode(' ', $args), PHP_EOL;
    }
}

/**
 *
 */
function writedata( $topic, $msg )
{
    e(1, 'Topic:', $topic);
    e(1, 'Message:', $msg);

    if ($data = json_decode($msg, true)) {
        // pvlng/<API key>/data/<GUID>
        list(,,,$guid) = explode('/', $topic);

        try {
            $rc = Channel::byGUID($guid)->write($data);
            e(1, 'Result:', $rc, 'row(s) added');
        } catch (Exception $e) {
            e(0, 'ERROR:', $e->getMessage());
        }

    } else {
        e(0, 'Message:', $msg);
    }
}

// ---------------------------------------------------------------------------
// Go
// ---------------------------------------------------------------------------

$opts = getopt('s:p:q:vh');

$server  = array_key_exists('s', $opts) ? $opts['s'] : 'localhost';
$port    = array_key_exists('p', $opts) ? $opts['p'] : 1883;
$qos     = array_key_exists('q', $opts) ? +$opts['q'] : 0;
$verbose = +array_key_exists('v', $opts);

if (array_key_exists('h', $opts)) {
    echo PHP_EOL;
    echo 'MQTT listener for PVLng channel data', PHP_EOL;
    echo PHP_EOL;
    echo 'Send messages as {"data":"..."[,"timestamp":"..."]} to "pvlng/<API key>/<GUID>"', PHP_EOL;
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
} else {
    ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
    error_reporting(0);
}

/**
 * Let's go
 */
require __DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', 'core', 'PVLng.php']);

// Add path for autoloading
PVLng::bootstrap(PVLng::path(__DIR__, 'phpMQTT'));

$mqtt = new phpMQTT($server, $port, 'PVLng');
$mqtt->debug = $verbose;

if (!$mqtt->connect(false)) exit(1);

/**
 * Listen only for messages for API key
 */
$apikey = PVLng::getDatabase()->queryOne('SELECT `pvlng_api_key`()');

$topic = 'pvlng/'.$apikey.'/data/#';

e(1, 'Listen for', $topic, '...');

$mqtt->subscribe(array($topic => array('qos' => $qos, 'function' => 'writedata')));

while ($mqtt->proc()) {
    sleep(1);
}

$mqtt->close();
