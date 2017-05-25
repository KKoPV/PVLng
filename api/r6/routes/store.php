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
$api->put(
    '/store/:key',
    $APIkeyRequired,
    function ($key) use ($api) {
        $request = json_decode($api->request->getBody(), true);
        if ($err = JSON::check()) {
            $api->stopAPI($err, 400);
        }
        if (!count($request)) {
            $api->stopAPI('Invalid JSON data', 400);
        }

        $api->db->set('API-'.strtolower($key), $request[0]);
        // Set HTTP code 201 for "created"
        $api->response->setStatus(201);
    }
)
->name('PUT /store/:key')
->help = array(
    'since'       => 'r6',
    'description' => 'Save a value for a key',
    'apikey'      => true,
    'payload'     => '["<data>"]'
);

/**
 *
 */
$api->get(
    '/store/:key',
    $APIkeyRequired,
    function ($key) use ($api) {
        $api->render(array($key => $api->db->get('API-'.strtolower($key))));
    }
)
->name('GET /store/:key')
->help = array(
    'since'       => 'r6',
    'description' => 'Retrieve a value for a key',
    'apikey'      => true
);
