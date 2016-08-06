<?php
/**
 * Server daemon
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

$options  = getopt('m:p:q:v');
$memcache = array_key_exists('m', $options) ? $options['m'] : 'localhost:11211';
$port     = array_key_exists('p', $options) ? $options['p'] : 7777;
$queue    = array_key_exists('q', $options) ? $options['q'] : 'SMQ';
$verbose  = array_key_exists('v', $options) ? (is_array($options['v']) ? count($options['v']) : 1) : 0;

if ($verbose == 2) {
    ini_set('display_errors', 1);
    error_reporting(-1);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Echo helper function
function e( $level, $msg ) {
    global $verbose;
    if ($level < 0) $msg = 'ERR: ' . $msg;
    if ($level <= $verbose) printf('[%s] %s'.PHP_EOL, date('Y-m-d H:i:s'), $msg);
}

// Gracefull exit
function shutdown() {
    global $sock;

    echo PHP_EOL;  // after ^C

    // Closing sockets gracefully
    e(1, 'Shut down ...');
    socket_shutdown($sock);

    // Close immediately
    // http://php.net/manual/de/function.socket-close.php#66810
    e(1, 'Close socket ...');
    socket_set_option($sock, SOL_SOCKET, SO_LINGER, array('l_onoff'=>1, 'l_linger'=>1));
    socket_close($sock);

    e(1, 'Done');
    exit;
}

$host = '127.0.0.1';

e(1, 'Verbosity level: '.$verbose);

// Create a new socket
if (!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    e(-1, "Couldn't create socket: [$errorcode] $errormsg");
    exit(1);
}

e(1, 'Socket created');

// Bind the source address
if (!socket_bind($sock, $host, $port)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    e(-1, "Could not connect: [$errorcode] $errormsg");
    exit(1);
}

if (!socket_listen($sock)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    e(-1, "Could not listen: [$errorcode] $errormsg");
    exit(1);
}

e(1, 'Socket Server started at '.$host.':'.$port);
e(1, 'Write to Memecache queue "'.$queue.'"');
e(1, 'Waiting for connections ...');
e(1, 'Press Ctrl+C to exit');

include __DIR__.'/SMQ/SMQ.php';

$mc = new SMQ\SMQ($queue);

// Max. clients
$max = 5;

$client = array();

// Prepare react to Ctrl+C
declare(ticks = 1);

pcntl_signal(SIGTERM, 'shutdown');
pcntl_signal(SIGINT,  'shutdown');

// Server loop
while (TRUE) {

    socket_set_block($sock);

    // Setup clients listen socket for reading
    $read = array($sock);
    for ($i=0; $i<$max; $i++){
        if (isset($client[$i])) $read[$i+1] = $client[$i]['sock'];
    }

    // Set up a blocking call to socket_select()
    $except = NULL;
    $ready  = @socket_select($read, $write, $except, 10);

    // If a new connection is being made add it to the clients array
    if (in_array($sock, $read)){
        for ($i=0; $i<$max; $i++){
            if (!isset($client[$i])){
                if (($client[$i]['sock'] = socket_accept($sock)) < 0){
                     e(-1, 'socket_accept() failed: '.socket_strerror($client[$i]['sock']));
                } else {
                    socket_getpeername($client[$i]['sock'], $client[$i]['addr'], $client[$i]['port']);
                    e(2, sprintf('#%02d + %s:%s', $i, $client[$i]['addr'], $client[$i]['port']));
                }
                break;
            } elseif ($i == $max-1){
                e(-1, 'Too many clients');
            }
        }
        if (--$ready <= 0) continue;
    }

    for ($i=0; $i<$max; $i++){

        if (isset($client[$i]) AND in_array($client[$i]['sock'], $read)) {

            $input = trim(socket_read($client[$i]['sock'], 10240));

            if ($input) {

                e(2, sprintf('#%02d < %s', $i, $input));

                try {
                    $mc->push($input);
                } catch (Exception $e) {
                    $output = $e->getMessage();
                    socket_write($client[$i]['sock'], $output, strlen($output));
                }
            }

            socket_close($client[$i]['sock']);

            e(2, sprintf('#%02d -', $i));

            unset($client[$i]);
        }
    }
}

e(1, 'Shut down ...');

// Closing sockets gracefully
socket_shutdown($sock);

e(1, 'Close socket ...');

// Close
// http://php.net/manual/de/function.socket-close.php#66810
socket_set_option($sock, SOL_SOCKET, SO_LINGER, array('l_onoff'=>1, 'l_linger'=>1));
socket_close($sock);

e(1, 'done');
