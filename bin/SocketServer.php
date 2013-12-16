<?php

// No Timeout
set_time_limit(0);

ini_set('display_errors', 0);
error_reporting(0);

ini_set('display_errors', 1);
error_reporting(-1);

$options = getopt('p:v');

$port = isset($options['p']) ? $options['p'] : 7777;
$verbose = isset($options['v']) ? (is_array($options['v']) ? count($options['v']) : 1) : 0;

// Server functions
function rLog( $level, $msg ) {
	global $verbose;
	if ($level <= $verbose) echo '[', date('Y-m-d H:i:s'), '] ', $msg, PHP_EOL;
}

$host = '127.0.0.1';


if (!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    rLog(-1, "Couldn't create socket: [$errorcode] $errormsg");
	exit(1);
}

rLog(0, 'Socket created');

if (!socket_bind($sock, $host, $port)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    rLog(-1, "Could not connect: [$errorcode] $errormsg");
	exit(1);
}

if (!socket_listen($sock)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    rLog(-1, "Could not listen: [$errorcode] $errormsg");
	exit(1);
}

rLog(0, 'Socket Server started at '.$host.':'.$port);
rLog(1, 'Verbosity level: '.$verbose);

$config = include __DIR__.'/../config/config.php';
$config = $config['Database'];

$db = new MySQLi($config['Host'], $config['Username'], $config['Password'], $config['Database']);

if ($db->connect_errno) {
    rLog(-1, 'Failed to connect to MySQL: (' . $db->connect_errno . ') ' . $db->connect_error);
	exit(1);
}

rLog(1, 'Connected to MySQL '.$db->server_info);
rLog(1, $db->host_info);

rLog(1, 'Waiting for connections');

// Max. clients
$max = 10;

$tables = array('pvlng_reading_str', 'pvlng_reading_num');
$client = array();

// Server loop
while (TRUE){
	socket_set_block($sock);
	// Setup clients listen socket for reading
	$read = array($sock);
	for ($i=0; $i<$max; $i++){
		  if (isset($client[$i])) $read[$i+1] = $client[$i]['sock'];
	}

	// Set up a blocking call to socket_select()
    $except = NULL;
	$ready = socket_select($read, $write, $except, 10);

	// If a new connection is being made add it to the clients array
	if (in_array($sock, $read)){
		for ($i=0; $i<$max; $i++){
			if (!isset($client[$i])){
				if (($client[$i]['sock'] = socket_accept($sock)) < 0){
					 rLog(-1, 'socket_accept() failed: '.socket_strerror($client[$i]['sock']));
				} else {
					socket_getpeername($client[$i]['sock'], $client[$i]['addr'], $client[$i]['port']);
					rLog(2, 'Client #'.$i.' connected from '.$client[$i]['addr'].':'.$client[$i]['port']);
				}
				break;
			} elseif ($i == $max-1){
				rLog(-1, 'Too many clients');
			}
		}
		if (--$ready <= 0) continue;
	}

	for ($i=0; $i<$max; $i++){
		if (isset($client[$i]) AND in_array($client[$i]['sock'], $read)){
			$input = trim(socket_read($client[$i]['sock'], 1024));
			if ($input) {

				rLog(2, 'Input: ' . $input);
				$input = explode(',', $input);

				try {
					// get numeric or not
					$sql = 'SELECT *'
					      .'  FROM `pvlng_tree_view`'
						  .' WHERE `guid` = "'.$input[0].'"'
						  .' LIMIT 1';

					if (!$res = $db->query($sql)) throw new Exception('NaN');
					if (!$entity = $res->fetch_object()) throw new Exception('NaN');

					if (isset($input[1])) {
						$output = $entity->{$input[1]};
					} else {
						$sql = 'SELECT `data`'
						      .'  FROM `'.$tables[$entity->numeric].'`'
							  .' WHERE `id` = '.$entity->entity
							  .' ORDER BY `timestamp` DESC'
							  .' LIMIT 1';

						if (!$res = $db->query($sql)) throw new Exception('NaN');
						if (!$data = $res->fetch_object()) throw new Exception('NaN');

						$output = $data->data;
					}
				} catch (Exception $e) {
					$output = $e->getMessage();
				}
				socket_write($client[$i]['sock'], $output, strlen($output));
			}
			socket_close($client[$i]['sock']);
			unset($client[$i]);
			rLog(2, 'Disconnected(1) client #'.$i);
		}
	}
}
