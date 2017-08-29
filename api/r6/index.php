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

// @codingStandardsIgnoreFile

/**
 * Initialize
 */
set_time_limit(0);

require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'bootstrap.php']);

/**
 *
 */
use Api\Api;
use Api\View;
use Channel\Channel;
use Core\NestedSet;
use Core\Hook;
use Core\PVLng;
use Slim\Log;
use Slim\Route;

/**
 * Bootstrap
 */
$loader = PVLng::bootstrap(__DIR__);
Loader::register($loader, PVLng::$TempDir);

// May not exist
@include PVLng::pathRoot('hook', 'hook.php');

Hook::run('api.load');

$api = new Api(array(
    'mode'      => PVLng::$DEBUG ? 'development' : 'production',
    'log.level' => PVLng::$DEBUG ? Log::INFO : Log::ALERT,
    'debug'     => false, // No debug mode at all
    'view'      => new View
));

Hook::run('api.init', $api);

/**
 * Environment
 */
$api->Language = 'en';
$p = explode(DIRECTORY_SEPARATOR, __DIR__);
$api->version = array_pop($p);
unset($p);

if (extension_loaded('newrelic')) {
    newrelic_set_appname('PVLng-API-'.$api->version);
}

/**
 * Configuration
 */
$api->container->singleton('config', function () {
    return PVLng::getConfig();
});

/**
 * Database
 */
$api->container->singleton('db', function () use ($api) {
    return PVLng::getDatabase();
});

/**
 * Cache
 */
$api->container->singleton('cache', function () use ($api) {
    return PVLng::getCache();
});

// ---------------------------------------------------------------------------
// Hooks
// ---------------------------------------------------------------------------
$api->hook('slim.before', function () use ($api) {

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
            case '.csv':
                $type = 'application/csv';
                break;
            case '.tsv':
                $type = 'application/tsv';
                break;
            case '.txt':
                $type = 'text/plain';
                break;
            case '.xml':
                $type = 'application/xml';
                break;
            case '.json':
                $type = 'application/json';
                break;
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

// ---------------------------------------------------------------------------
// The helper functions
// ---------------------------------------------------------------------------

/**
 *
 */
$APIkeyRequired = function () use ($api) {
    if (!$api->APIKeyValid) {
        $api->stopAPI('Access only with valid API key!', 403);
    }
};

/**
 *
 */
$accessibleChannel = function (Route $route) use ($api) {
    // API key correct, access all channels
    if ($api->APIKeyValid) {
        return;
    }

    // No API key given, check channel is public
    if (!Channel::byGUID($route->getParam('guid'))->public) {
        $api->stopAPI('Access to private channel only with valid API key!', 403);
    }
};

/**
 *
 */
$checkLocation = function () use ($api) {
    $api->Latitude  = $api->config->get('Core.Latitude');
    $api->Longitude = $api->config->get('Core.Longitude');

    if ($api->Latitude == '' || $api->Longitude == '') {
        $api->stopAPI('No valid location defined in settings', 404);
    }
};

/**
 *
 */
$api->error(function ($e) use ($api) {
    if ($api->Request()->Headers()->get('X-PVLng-Trace')) {
        echo $e;
    } else {
        $api->stopAPI($e->getMessage(), $e->getCode());
    }
});

// ---------------------------------------------------------------------------
// Declare default conditions before routes
// ---------------------------------------------------------------------------
Route::setDefaultConditions(array(
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

/**
 * The routes, compile into one cached file
 */
$routesCache = PVLng::pathTemp('routes.api.'.$api->version.'.php');

if (PVLng::$DEBUG || !file_exists($routesCache)) {
    $content = '';
    foreach (glob(PVLng::path(__DIR__, 'routes', '*.php')) as $file) {
        $content .= file_get_contents($file) . PHP_EOL;
    }
    $content = str_replace('<?php', '', $content);
    $content = preg_replace('~\s*/\*.*?\*/\s*~s', PHP_EOL, $content);
    file_put_contents($routesCache, '<?php ' . $content);
}

include $routesCache;

/**
 * Route not found, redirect to help instead
 */
$api->notFound(function () use ($api) {
    // Catch also /
    $api->redirect($api->request()->getRootUri() . '/help');
});

/**
 * Run application
 */
Hook::run('api.run', $api);

$api->add(new CorsSlim\CorsSlim);

$api->run();

Hook::run('api.teardown', $api);

// Send statistics each 6 hours if activated
if ($api->config->SendStatistics) {
    PVLng::SendStatistics();
}
