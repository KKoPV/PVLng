<?php
/**
 * Fetch Inverter and Sensorbox data from SMA Webbox
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

/**
 * Settings from configuration can be accessed by
 * $section['<key>'] (keys lowercase)
 */

$ip = $section['ip'];

$rpc = array(
    'version' => '1.0',
    'proc'    => 'GetProcessData',
    'id'      => (string) time(),
    'format'  => 'JSON',
    'params' => NULL
);

if ($section['password']) $rpc['passwd'] = md5($section['password']);

/**
 *
 */
foreach ($section['equipments'] as $equipment) {

    list($serial, $guid) = array_values($equipment);

    okv(1, 'Serial / Channel', $serial . ' / ' . $guid);

    $r = $rpc;
    $r['params']['devices'][0]['key'] = $serial;
    $r = json_encode($r);

    okv(1, 'RPC', $r);

    // Start curl sequence
    if (!curl(array(
        CURLOPT_URL => 'http://'.$ip.'/rpc',
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => 'RPC='.$r,
        CURLOPT_RETURNTRANSFER => 1
    ), $data, $info)) continue;

    okv(2, 'Received', $data);

    // Anything went wrong?
    if ($info['http_code'] != 200) {
        out(0, print_r($data, TRUE));
        continue;
    }

    if (!TESTMODE) {
        $cnt = Channel::byGUID($guid)->write(json_decode($data, TRUE));
        okv(1, 'Channels updated', $cnt);
    }

    out(1);

}
