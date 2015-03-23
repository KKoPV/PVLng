<?php

// No Timeout
set_time_limit(0);

ini_set('display_errors', 0);
error_reporting(0);

ini_set('display_errors', 1);
error_reporting(-1);

$options = getopt('m:v');

$memcache = isset($options['m']) ? $options['m'] : 'localhost:11211';
$verbose = isset($options['v']) ? (is_array($options['v']) ? count($options['v']) : 1) : 0;

// Server functions
function e( $level, $msg ) {
    global $verbose;
    if ($level <= $verbose) echo '[', date('Y-m-d H:i:s'), '] ', $msg, PHP_EOL;
}

e(1, 'Verbosity level: '.$verbose);

include __DIR__.'/SMQ/SMQ.php';
include __DIR__.'/SMQ/SMQLocal.php';

$mc = new SMQ\SMQLocal;

// Server loop
while (TRUE) {

    try {
        $data = $mc->pull();

        $proc = NULL;

        if ($data != '') {
            e(2, 'Queue data: '.$data);
            $data = explode(':', $data.':::');
            $proc = array_shift($data);
        }

        switch ($proc) {
            // --------------------
            case 'saveData':


                break;

            // --------------------
            default:
                usleep(100 * 1000);
        }

    } catch (Exception $e) {
        e(-1, $e->getMessage());
    }
}
