<?php
/**
 * Update PVOutput.org
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

$StatusURL = 'http://pvoutput.org/service/r2/addstatus.jsp';

/**
 * Settings from configuration can be accessed by
 * $section['<key>'] (keys lowercase)
 */

// Fetch data aggregated over the time since last run
$request = array( 'period' => $section['runeach'] . 'i' );

// date and time parameters
$data = array( 'd' => date('Ymd'), 't' => date('H:i') );

// Loop channels
foreach ($section['channels'] as $id=>$channel) {

    $channel = array_merge(array( 'guid' => '', 'factor' => 1 ), $channel);

    // Ignore channels without GUID
    if ($channel['guid'] == '') continue;

    $d = Channel::byGUID($channel['guid'])->read($request);

    // No data
    if (!count($d)) continue;

    $d = $d->asArray();
    $data[$id] = array_pop($d)['data'] * $channel['factor'];
}

out(1, 'Data: %s', print_r($data, TRUE));

// Check, that at least ONE of v1 .. v4 is set
if (empty($data[1]) && empty($data[2]) && empty($data[3]) && empty($data[4])) {
    out(1, 'No valid data, skip');
    return;
}

$data = http_build_query($data, 'v');
out(1, 'URL       : %s', $StatusURL);
out(1, 'Data      : %s', $data);

// Start curl sequence
if (!curl(array(
    CURLOPT_URL => $StatusURL,
    // Authorization
    CURLOPT_HTTPHEADER => array(
        'X-Pvoutput-Apikey: ' . $section['apikey'],
        'X-Pvoutput-SystemId: '. $section['systemid']
    ),
    // Send POST
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_RETURNTRANSFER => 1,
), $response, $info)) return;

if (TESTMODE) return;

out(1, 'Response  : %s', $response);

// Anything went wrong?
if ($info['http_code'] != 200) out(0, 'Response  : %s', $response);
