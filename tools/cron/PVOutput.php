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
$request = array( 'period' => $section['each'] . 'i' );

// date and time parameters
$data = array( 'd' => date('Ymd'), 't' => date('H:i') );

// Loop channels
foreach ($section['channels'] as $id => $channel) {
    $channel = array_merge(array( 'guid' => '', 'factor' => 1 ), $channel);

    // Ignore channels without GUID
    if ($channel['guid'] == '') {
        continue;
    }

    $d = Channel\Channel::byGUID($channel['guid'])->read($request);

    // No data
    if (!count($d)) {
        continue;
    }

    $d = $d->asArray();
    $data[$id] = array_pop($d)['data'] * $channel['factor'];
}

okv(1, 'Data', print_r($data, true));

// Check, that at least ONE of v1 .. v4 is set
if (empty($data[1]) && empty($data[2]) && empty($data[3]) && empty($data[4])) {
    out(1, 'No valid data, skip');
    return;
}

$data = http_build_query($data, 'v');
okv(1, 'URL', $StatusURL);
okv(1, 'Data', $data);

if ($TESTMODE) {
    return;
}

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
), $response, $info)) {
    return;
}

okv(1, 'Response', $response);

// Anything went wrong?
if ($info['http_code'] != 200) {
    // Ignore "PVOutput is offline for maintenance"
    if (!strstr($response, 'maintenance')) {
        okv(0, 'Response', $response);
    }
}
