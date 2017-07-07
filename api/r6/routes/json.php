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
$api->get(
    '/json/:path+',
    function ($path) use ($api) {
        $api->render(Core\JSON::xPath($api->request->get('json'), $path));
    }
)
->name('GET /json/:path+')
->help = array(
    'description' => 'Extract a section/value from given JSON data from query string',
    'payload'     => array('json' => '<JSON data>')
);

/**
 *
 */
$api->post(
    '/json/:path+',
    function ($path) use ($api) {
        $api->render(Core\JSON::xPath($api->request->getBody(), $path));
    }
)
->name('POST /json/:path+')
->help = array(
    'description' => 'Extract a section/value from given JSON data sended in request body e.g. from a file',
);

/**
 *
 */
$api->post(
    '/jsonencode',
    function () use ($api) {
        // Set the response header to JSON
        $api->contentType('application/json');
        $api->render(Core\JSON::encode($api->request->getBody()));
    }
)
->name('POST /jsonencode');
