<?php
/**
 * Helper functions
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
Slim\Route::setDefaultConditions(array(
    'guid' => '(\w{4}-){7}\w{4}',
    'id'   => '\d+',
    'slug' => '[@\w\d-]+'
));

/**
 * - Detect requested content type by file extension, correct PATH_INFO value
 *   without extension and set Response content header
 * - Analyse X-PVLng-Key header
 */
$api->hook('slim.before', function() use ($api) {
    $PathInfo = $api->environment['PATH_INFO'];
    if ($dot = strrpos($PathInfo, '.')) {
        // File extension
        $ext = substr($PathInfo, $dot+1);
        // Correct PATH_INFO, remove extension
        $api->environment['PATH_INFO'] = substr($PathInfo, 0, $dot);
        // All supported content types
        switch ($ext) {
            case 'csv':   $type = 'application/csv';   break;
            case 'tsv':   $type = 'application/tsv';   break;
            case 'txt':   $type = 'text/plain';        break;
            case 'xml':   $type = 'application/xml';   break;
            case 'json':  $type = 'application/json';  break;
            default:
                $api->contentType('text/plain');
                $api->halt(400, 'Unknown Accept content type: '.$ext);
        }
    } else {
        // Defaults to JSON
        $type = 'application/json';
    }
    // Set the response header, used also by View to build proper response body
    $api->contentType($type);

    // Analyse X-PVLng-Key header
    $APIKey = $api->request->headers->get('X-PVLng-Key');

    if ($APIKey == '') {
        // Key was not given
        $api->APIKeyValid = FALSE;
    } elseif ($APIKey == $api->db->queryOne('SELECT getAPIKey()')) {
        // Key is given and valid
        $api->APIKeyValid = TRUE;
    } else {
        // Key is invalid
        $api->stopAPI('Invalid API key given.', 403);
    }
});

/**
 *
 */
$api->error(function($e) use ($api) {
    if ($api->request->headers->get('X-PVLng-Trace')) {
        echo $e;
    } else {
        $api->stopAPI($e->getMessage(), $e->getCode());
    }
});

/**
 *
 */
$api->notFound(function() use ($api) {
    // Catch also /
    $api->redirect($api->request()->getRootUri() . '/help');
});

/**
 *
 */
$APIkeyRequired = function() use ($api) {
    $api->APIKeyValid || $api->stopAPI('Access only with valid API key!', 403);
};

/**
 *
 */
$accessibleChannel = function(Slim\Route $route) use ($api) {
    // API key correct, access all channels
    if ($api->APIKeyValid) return;

    // No API key given, check channel is public
    if (!Channel::byGUID($route->getParam('guid'))->public) {
        $api->stopAPI('Access to private channel only with valid API key!', 403);
    }
};

/**
 *
 */
$checkLocation = function() use ($api) {
    $api->Latitude  = $api->config->get('Location.Latitude');
    $api->Longitude = $api->config->get('Location.Longitude');

    if ($api->Latitude == '' OR $api->Longitude == '') {
        $api->stopAPI('No valid location defined in configuration', 404);
    }
};
