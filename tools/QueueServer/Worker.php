<?php
/**
 * Worker daemon
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

// No Timeout
set_time_limit(0);

// Force technical numeric (with dot)
setlocale(LC_NUMERIC, 'C');

// Command line parameters
$options  = getopt('m:q:v');
$memcache = array_key_exists('m', $options) ? $options['m'] : 'localhost:11211';
$queue    = array_key_exists('q', $options) ? $options['q'] : 'SMQ';
$verbose  = array_key_exists('v', $options) ? (is_array($options['v']) ? count($options['v']) : 1) : 0;

if ($verbose == 2) {
    ini_set('display_errors', 1);
    error_reporting(-1);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

/**
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(dirname(__DIR__)));
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('TEMP_DIR', ROOT_DIR . DS . 'tmp'); // Outside document root!

// Echo helper function
function e( $level, $msg ) {
    global $verbose;
    if ($level < 0) $msg = 'ERR: ' . $msg;
    if ($level <= $verbose) printf('[%s] %s'.PHP_EOL, date('Y-m-d H:i:s'), $msg);
}

// Gracefull exit
function shutdown() {
    echo PHP_EOL;  // after ^C
    e(1, 'Shut down ...');
    e(1, 'Done');
    exit;
}

/**
 * Initialize Auto-Loader
 */
include LIB_DIR . DS . 'Loader.php';

Loader::register(
    array(
        'path'    => array(CORE_DIR, LIB_DIR, __DIR__),
        'pattern' => array('%s.php'),
        'exclude' => array('contrib/')
    ),
    TEMP_DIR
);

Loader::registerCallback( function($file) {
    e(3, 'Load ' . str_replace(ROOT_DIR.DS, '', $file));
    return $file;
});

e(1, 'Listen Memcache on '.$memcache);
e(1, 'Listen on Memcache queue "'.$queue.'"');
e(1, 'Verbosity level: '.$verbose);
e(1, 'Waiting for data ...');
e(1, 'Press Ctrl+C to exit');

include __DIR__.'/SMQ/SMQ.php';

$mc = new SMQ\SMQ($queue);

$classes = array();

// Prepare react to Ctrl+C
declare(ticks = 1);

pcntl_signal(SIGTERM, 'shutdown');
pcntl_signal(SIGINT,  'shutdown');

// Worker loop
while (TRUE) {

    try {

        if (($data = $mc->pull()) == '') {
          // Wait 500ms
            usleep(500 * 1000);
            continue;
        }

        $data = preg_replace('~ *\| *~', '|', $data);
        e(2, '> '.$data);

        $data = explode('|', $data);
        $class = 'QueueWorker\\'.array_shift($data);

        if (!class_exists($class)) {
            throw new Exception('Missing class: '.$class);
        }

        if (!array_key_exists($class, $classes)) {
            $classes[$class] = new $class;
        }

        $classes[$class]->process($data);

    } catch (Exception $e) {
        e(-1, $e->getMessage());
    }
}
