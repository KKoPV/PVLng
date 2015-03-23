<?php

// No Timeout
set_time_limit(0);

ini_set('display_errors', 0);
error_reporting(0);

ini_set('display_errors', 1);
error_reporting(-1);

$options = getopt('m:p:v');

$memcache = isset($options['m']) ? $options['m'] : 'localhost:11211';
$port = isset($options['p']) ? $options['p'] : 7777;
$verbose = isset($options['v']) ? (is_array($options['v']) ? count($options['v']) : 1) : 0;

// Server functions
function e( $level, $msg ) {
    global $verbose;
    if ($level <= $verbose) echo '[', date('Y-m-d H:i:s'), '] ', $msg, PHP_EOL;
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

e(1, 'Socket Server started: '.$host.':'.$port);
e(1, 'Waiting for connections ...');

include __DIR__.'/SMQ/SMQ.php';
include __DIR__.'/SMQ/SMQLocal.php';

$mc = new SMQ\SMQLocal;

// Max. clients
$max = 5;

$client = array();

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
    $ready  = socket_select($read, $write, $except, 10);

    // If a new connection is being made add it to the clients array
    if (in_array($sock, $read)){
        for ($i=0; $i<$max; $i++){
            if (!isset($client[$i])){
                if (($client[$i]['sock'] = socket_accept($sock)) < 0){
                     e(-1, 'socket_accept() failed: '.socket_strerror($client[$i]['sock']));
                } else {
                    socket_getpeername($client[$i]['sock'], $client[$i]['addr'], $client[$i]['port']);
                    e(2, '#'.$i.' connected: '.$client[$i]['addr'].':'.$client[$i]['port']);
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

            $input = trim(socket_read($client[$i]['sock'], 1024));

            if ($input) {

                e(2, '#'.$i.' sended: '.$input);

                try {
                    $mc->push($input);
                } catch (Exception $e) {
                    $output = $e->getMessage();
                    socket_write($client[$i]['sock'], $output, strlen($output));
                }
            }

            socket_close($client[$i]['sock']);

            e(2, '#'.$i.' disconnected(1)');

            unset($client[$i]);
        }
    }
}

// Closing sockets gracefully
socket_shutdown($sock);

// Close
socket_close($sock);
