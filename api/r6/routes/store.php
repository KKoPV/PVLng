<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->put(
    '/store/:key',
    $APIkeyRequired,
    function ($key) use ($api) {
        $body = $api->request->getBody();

        try {
            $request = Core\JSON::decode($body, true);
        } catch (Exception $e) {
            $api->stopAPI($e->getMessage() . ': ' . $body, 400);
        }

        if (!count($request)) {
            $api->stopAPI('Invalid JSON data', 400);
        }

        $api->db->set('API-'.$key, $request[0]);
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
        $api->render(array($key => $api->db->get('API-'.$key)));
    }
)
->name('GET /store/:key')
->help = array(
    'since'       => 'r6',
    'description' => 'Retrieve a value for a key',
    'apikey'      => true
);
