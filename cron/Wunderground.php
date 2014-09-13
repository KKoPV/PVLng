<?php
/**
 * Update PVOutput.org
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

$url = sprintf('http://api.wunderground.com/api/%s/conditions/lang:%s/q/%f,%f.json',
               $section['apikey'], $section['language'],
               $config->get('Location.Latitude'), $config->get('Location.Longitude'));

out(1, 'URL       : %s', $url);

// Start curl sequence
if (!curl(array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => 1
), $data, $info)) return;

out(2, 'Received  : %s', print_r($data, TRUE));

if (TESTMODE) return;

// Anything went wrong?
if ($info['http_code'] != 200) {
    out(0, '%s', print_r($data, TRUE));
    return;
}

$cnt = Channel::byGUID($section['channel'])->write(json_decode($data, TRUE));

out(1, 'Updated   : %d channels', $cnt);
