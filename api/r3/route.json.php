<?php
/**
 * JSON xPath parser
 *
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 *
 */
$api->get('/json/:path+', function($path) use ($api) {
    $api->render(JSONxPath($api, $path, $api->request->get('json')));
})->name('json extract via get')->help = array(
    'description' => 'Extract a section/value from given JSON data from query string',
    'payload'     => '...json/path/to/node/?json=<JSON data>'
);

/**
 *
 */
$api->post('/json/:path+', function($path) use ($api) {
    $api->render(JSONxPath($api, $path, $api->request->getBody()));
})->name('json extract via post')->help = array(
    'description' => 'Extract a section/value from given JSON data sended in request body e.g. from a file',
);

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
