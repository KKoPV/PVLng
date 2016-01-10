<?php
/**
 * Main API file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

define('DEVELOP', (isset($_SERVER['HTTP_X_DEBUG']) AND $_SERVER['HTTP_X_DEBUG']));

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
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('BASE_DIR', dirname(__FILE__));
define('ROOT_DIR', dirname(dirname(BASE_DIR)));
define('APP_DIR',  BASE_DIR . DS . 'app');
define('CONF_DIR', ROOT_DIR . DS . 'config');
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('TEMP_DIR', ROOT_DIR . DS . 'tmp'); // Outside document root!

$version = file(ROOT_DIR . DS . '.version', FILE_IGNORE_NEW_LINES);
define('PVLNG',              'PhotoVoltaic Logger new generation');
define('PVLNG_VERSION',      $version[0]);
define('PVLNG_VERSION_DATE', $version[1]);
define('PVLNG_VERSION_FULL', PVLNG . ' ' . PVLNG_VERSION);

/**
 * Initialize
 */
setlocale(LC_NUMERIC, 'C');

file_exists(BASE_DIR.DS.'prepend.php') && include BASE_DIR.DS.'prepend.php';

/**
 * Initialize Loader
 */
$loader = require_once ROOT_DIR . DS . 'vendor' . DS . 'autoload.php';
$loader->addPsr4('', array(CORE_DIR, LIB_DIR, APP_DIR));

Loader::register($loader, TEMP_DIR);

$api = new API(array(
    'mode'      => DEVELOP ? 'development' : 'production',
    'log.level' => DEVELOP ? Slim\Log::INFO : Slim\Log::ALERT,
    'debug'     => FALSE, // No debug mode at all
    'view'      => new View
));

$api->Language = 'en';
$api->version  = substr($api->request()->getRootUri(), 5);

include BASE_DIR . DS . 'functions.php';

/**
 * Configuration
 */
$api->container->singleton('config', function() {
    return (new \slimMVC\Config)
           ->load(ROOT_DIR . DS . 'config' . DS . 'config.default.php')
           ->load(ROOT_DIR . DS . 'config' . DS . 'config.php')
           ->load('config.php', FALSE);
});

/**
 * Database
 */
$api->container->singleton('db', function() use ($api) {
    extract($api->config->get('Database'), EXTR_REFS);
    $db = new \slimMVC\MySQLi($host, $username, $password, $database, $port, $socket);
    $db->setSettingsTable('pvlng_config');
    return $db;
});

/**
 * Cache
 */
$api->container->singleton('cache', function() use ($api) {
    return Cache::factory(
        array('Directory' => TEMP_DIR, 'TTL' => 86400),
        $api->config->get('Cache', 'MemCache,APC,File')
    );
});

// ---------------------------------------------------------------------------
// Hooks
// ---------------------------------------------------------------------------
$api->hook('slim.before', function() use ($api) {

    slimMVC\ORM::setDatabase($api->db);
    slimMVC\ORM::setCache($api->cache);
    Channel::setCache($api->cache);

    foreach ((new ORM\SettingsKeys)->find() as $setting) {
        $api->config->set($setting->getKey(), $setting->getValue());
    }

    BabelKitMySQLi::setParams(array( 'table' => 'pvlng_babelkit' ));
    BabelKitMySQLi::setDB($api->db);
    BabelKitMySQLi::setCache($api->cache);

    I18N::setLanguage($api->Language);
    I18N::setCodeSet('app');
    I18N::setBabelKit(BabelKitMySQLi::getInstance());

    /**
     * Nested set for channel tree
     */
    include_once LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

    NestedSet::Init(array (
        'db'    => $api->db,
        'debug' => FALSE,
        'lang'  => 'en',
        'path'  => LIB_DIR . DS . 'contrib' . DS . 'messages',
        'db_table' => array (
            'tbl'  => 'pvlng_tree',
            'nid'  => 'id', 'l' => 'lft', 'r' => 'rgt', 'mov'  => 'moved', 'pay' => 'entity'
        )
    ));

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

    // Analyse X-PVLng-Key header
    $key = $headers->get('X-PVLng-Key');

    if ($key == '') {
        // Key was not given
        $api->APIKeyValid = FALSE;
    } elseif ($key == $api->db->queryOne('SELECT getAPIKey()')) {
        // Key is given and valid
        $api->APIKeyValid = TRUE;
    } else {
        // Key is invalid
        $api->stopAPI('Invalid API key given.', 403);
    }

    // Analyse X-PVLng-DryRun header
    if ($api->dryrun = $headers->get('X-PVLng-DryRun', FALSE)) {
        $api->contentType('text/plain');
        echo 'Dry run, no data will be saved.', PHP_EOL;
    }
});

/**
 * Debugging middleware
 */
DEVELOP && include BASE_DIR.DS.'develop.php';

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

/**
 *
 */
function SaveCSVdata( $guid, $rows, $sep ) {

    // Ignore empty datasets
    $rows = array_values(array_filter($rows));

    if (empty($rows)) return;

    try {
        $api = API::getInstance();

        // Disable AutoCommit in case of errors
        $api->db->autocommit(FALSE);
        $saved = 0;

        $channel = Channel::byGUID($guid);

        // Ignore empty datasets, track also row Id for error messages
        foreach ($rows as $row=>$dataset) {
            $data = explode($sep, $dataset);

            switch (count($data)) {
                case 2:
                    // timestamp/datetime and data
                    list($timestamp, $value) = $data;
                    break;
                case 3:
                    // date, time and data
                    $timestamp = $data[0] . ' ' . $data[1];
                    $value     = $data[2];
                    break;
                default:
                    throw new Exception('Invalid data: '.$dataset, 400);
            } // switch

            if (!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

            if ($timestamp === FALSE) {
                throw new Exception('Invalid timestamp in row '.($row+1).': "'.$dataset.'"', 400);
            }

            if ($api->dryrun) {
                echo $timestamp, $sep, $value,
                     ' (', date('Y-m-d H:i:s', $timestamp), ' : ', $value, ')', PHP_EOL;
            } else {
                $saved += $channel->write(array('data'=>$value), $timestamp);
            }
        }
        // All fine, commit changes
        $api->db->commit();

        if ($saved) $api->status(201);

        $result = array(
            'status'  => 'succes',
            'message' => ($row+1) . ' valid row(s) sended, ' . $saved . ' row(s) inserted'
        );

        $api->render($result);

    } catch (Exception $e) {
        // Rollback all correct data
        $api->db->rollback();
        $api->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
    }
}

// ---------------------------------------------------------------------------
// Declare default conditions before routes
// ---------------------------------------------------------------------------
Slim\Route::setDefaultConditions(array(
    'date'      => '\d{4}-\d{2}-\d{2}',
#    'guid'      => '\w{4}(?:-\w{4}){7}',
    'guid'      => '\w{4}-\w{4}(?:-\w{4}){0,6}',
    'period'    => '(?:last|readlast|all)',
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
foreach (glob(BASE_DIR.DS.'routes'.DS.'*.php') as $routes) include $routes;

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

file_exists(BASE_DIR.DS.'append.php') && include BASE_DIR.DS.'append.php';

// Send statistics each 6 hours if activated
if ($api->config->SendStatistics) PVLng::SendStatistics();
