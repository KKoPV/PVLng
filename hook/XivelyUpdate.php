#!/usr/bin/php
<?php
/**
 * $0 - Xively API endpount URL
 * $1 - Xively API key
 * $2 - Xively channel name,value (formated)
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

if (!isset($_SERVER['argc']) || $_SERVER['argc'] != 4) exit(1);

list($_self, $URL, $APIkey, $value) = $_SERVER['argv'];

// Write to memory stream for PUT request
$fh = fopen('php://memory', 'rw');
fwrite($fh, $value);
rewind($fh);

$ch = curl_init($URL);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_HEADER,         FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER,     array('X-ApiKey: '.$APIkey));
curl_setopt($ch, CURLOPT_PUT,            TRUE);
curl_setopt($ch, CURLOPT_INFILE,         $fh);
curl_setopt($ch, CURLOPT_INFILESIZE,     strlen($value));

curl_exec($ch);

curl_close($ch);
fclose($fh);
