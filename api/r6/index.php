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
 * Initialize
 */
set_time_limit(0);
setlocale(LC_NUMERIC, 'C');

require __DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', '..', 'core', 'PVLng.php']);

$file = PVLng::path(__DIR__, 'prepend.php');
if (file_exists($file)) {
    include $file;
}

if (!defined('DEVELOP')) {
    define('DEVELOP', (isset($_SERVER['HTTP_X_DEBUG']) && $_SERVER['HTTP_X_DEBUG']));
}

if (DEVELOP) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
} else {
    ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
    error_reporting(0);
}

/**
 * Bootstrap
 */
$loader = PVLng::bootstrap(__DIR__);
Loader::register($loader, TEMP_DIR);

$api = new Api(array(
    'mode'      => DEVELOP ? 'development' : 'production',
    'log.level' => DEVELOP ? Slim\Log::INFO : Slim\Log::ALERT,
    'debug'     => false, // No debug mode at all
    'view'      => new View
));

/**
 * If API run over different (sub)domain, allow CORS
 */
$CORS = false;

if ($api->environment['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']) {
    $api->response['Access-Control-Allow-Headers'] = $api->environment['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'];
    $CORS = true;
}

if ($api->environment['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) {
    $api->response['Access-Control-Allow-Method'] = 'POST, GET, OPTIONS, PUT, DELETE';
    $CORS = true;
}

if ($CORS) {
    $api->response['Access-Control-Allow-Origin'] = '*';
}

$api->Language = 'en';
$api->version  = substr($api->request()->getRootUri(), 5);

/**
 * Configuration
 */
$api->container->singleton('config', function() {
    return PVLng::getConfig();
});

/**
 * Database
 */
$api->container->singleton('db', function() use ($api) {
    return PVLng::getDatabase();
});

/**
 * Cache
 */
$api->container->singleton('cache', function() use ($api) {
    return PVLng::getCache();
});

// ---------------------------------------------------------------------------
// Hooks
// ---------------------------------------------------------------------------
$api->hook('slim.before', function() use ($api) {

    $headers = $api->Request()->Headers();

    /**
     * Detect requested content type by file extension, correct PATH_INFO value
     * without extension and set Response content header
     */
    preg_match('~^(.*?)(\..*?)?(\?.*)?$~', $api->environment['PATH_INFO'], $args);

    // Correct PATH_INFO, remove parameters
    $api->environment['PATH_INFO'] = $args[1];

    if (!empty($args[2])) {
        // All supported content types
        switch (/* Extension */ $args[2]) {
            case '.csv':   $type = 'application/csv';   break;
            case '.tsv':   $type = 'application/tsv';   break;
            case '.txt':   $type = 'text/plain';        break;
            case '.xml':   $type = 'application/xml';   break;
            case '.json':  $type = 'application/json';  break;
            default:
                $api->contentType('text/plain');
                $api->halt(400, 'Unknown Accept content type: '.$args[2]);
        }
    } else {
        // Defaults to JSON
        $type = 'application/json';
    }
    // Set the response header, used also by View to build proper response body
    $api->contentType($type);

    // Analyse Authorization header
    if ($key = $headers->get('Authorization')) {
        // Silently accept more than 1 space ...
        if (preg_match('~^Bearer +(.+)~', $key, $args)) {
            $key = $args[1];
        }
    } else {
        // Analyse X-PVLng-Key header (downward compatible)
        $key = $headers->get('X-PVLng-Key');
    }

    if ($key == '') {
        // Key was not given
        $api->APIKeyValid = false;
    } elseif (PVLng::checkApiKey($key)) {
        // Key is given and valid
        $api->APIKeyValid = true;
    } else {
        // Key is invalid
        $api->stopAPI('Invalid authorization', 403);
    }

    // Analyse X-PVLng-DryRun header
    if ($api->dryrun = $headers->get('X-PVLng-DryRun', false)) {
        $api->contentType('text/plain');
        echo 'Dry run, no data will be saved.', PHP_EOL;
    }
});

/**
 * Debugging middleware
 */
if (DEVELOP) {
    include PVLng::path(__DIR__, 'develop.php');
}

// ---------------------------------------------------------------------------
// The helper functions
// ---------------------------------------------------------------------------

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
    $api->Latitude  = $api->config->get('Core.Latitude');
    $api->Longitude = $api->config->get('Core.Longitude');

    if ($api->Latitude == '' OR $api->Longitude == '') {
        $api->stopAPI('No valid location defined in settings', 404);
    }
};

/**
 *
 */
$api->error(function($e) use ($api) {
    if ($api->Request()->Headers()->get('X-PVLng-Trace')) {
        echo $e;
    } else {
        $api->stopAPI($e->getMessage(), $e->getCode());
    }
});

// ---------------------------------------------------------------------------
// Declare default conditions before routes
// ---------------------------------------------------------------------------
Slim\Route::setDefaultConditions(array(
    'date'      => '\d{4}-\d{2}-\d{2}',
                   // At least the 1st and 2nd terms of a GUID are required
    'guid'      => '[0-9a-f]{4}-[0-9a-f]{4}(?:-[0-9a-f]{4}){0,6}',
    'period'    => '(?:monthly|last|readlast|all)',
    'timestamp' => '\d+',
    'id'        => '\d+',
    'latitude'  => '[\d.-]+',
    'longitude' => '[\d.-]+',
    'offset'    => '\d+',
    'slug'      => '[@\w\d-]+',
));

// ---------------------------------------------------------------------------
// The routes
// ---------------------------------------------------------------------------
$filemask = PVLng::path(__DIR__, 'routes', '*.php');
foreach (glob($filemask) as $routes) {
    include_once $routes;
}

/**
 * Route not found, redirect to help instead
 */
$api->notFound(function() use ($api) {
    // Catch also /
    $api->redirect($api->request()->getRootUri() . '/help');
});

/**
 * Run application
 */
$api->run();

$file = PVLng::path(__DIR__, 'append.php');
if (file_exists($file)) {
    include $file;
}

// Send statistics each 6 hours if activated
if ($api->config->SendStatistics) PVLng::SendStatistics();
