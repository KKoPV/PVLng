<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->get(
    '/json/:path+',
    function($path) use ($api)
{
    $api->render(JSONxPath($api, $path, $api->request->get('json')));
})->name('GET /json/:path+')->help = array(
    'description' => 'Extract a section/value from given JSON data from query string',
    'payload'     => array('json' => '<JSON data>')
);

/**
 *
 */
$api->post(
    '/json/:path+',
    function($path) use ($api)
{
    $api->render(JSONxPath($api, $path, $api->request->getBody()));
})->name('POST /json/:path+')->help = array(
    'description' => 'Extract a section/value from given JSON data sended in request body e.g. from a file',
);

/**
 *
 */
$api->post(
    '/jsonencode',
    function() use ($api)
{
    // Set the response header to JSON
    $api->contentType('application/json');
    $api->render($api->request->getBody());
})->name('Encode posted data to JSON');

/**
 * Helper function
 */
function JSONxPath( $api, $path, $json ) {

    $json = json_decode($json, TRUE);

    if ($err = JSON::check()) $api->stopAPI($err, 400);

    // Root pointer
    $p = &$json;

    foreach ($path as $key) {
        if (is_array($p) AND isset($p[$key])) {
            // Move pointer foreward ...
            $p = &$p[$key];
        } else {
            // ... until key not more found
            $api->halt(404);
        }
    }

    // Key found, return its value
    return $p;
};
