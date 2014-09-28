<?php
/**
 * Main API file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

setlocale(LC_NUMERIC, 'C');

/**
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('API_DIR',  dirname(__FILE__));
define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('TEMP_DIR', ROOT_DIR . DS . 'tmp');

file_exists(API_DIR.DS.'prepend.php') && include API_DIR.DS.'prepend.php';

/**
 * Initialize Loader
 */
include LIB_DIR . DS . 'Loader.php';

Loader::register(
    array(
        'path'    => array(LIB_DIR, CORE_DIR),
        'pattern' => array('%s.php'),
        'exclude' => array('contrib/')
    ),
    TEMP_DIR
);

class API extends Slim\Slim {

    /**
     * Get named parameter as string
     */
    public function strParam( $name, $default='' ) {
        $value = trim($this->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     * Get named parameter as integer
     */
    public function intParam( $name, $default=0 ) {
        $value = trim($this->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     * Get named parameter as boolean, all of (true|on|yes|1) interpreted as TRUE
     */
    public function boolParam( $name, $default=FALSE ) {
        $value = strtolower(trim($this->request->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }

    /**
     *
     */
    public function stopAPI( $message, $code=400 ) {
        $this->status($code);
        $this->response()->header('X-Status-Reason', $message);
        $this->render(array( 'status'=>$code<400?'success':'error', 'message'=>$message ));
        $this->stop();
    }
}

$config = slimMVC\Config::getInstance()
        ->load(ROOT_DIR . DS . 'config' . DS . 'config.default.php')
        ->load(ROOT_DIR . DS . 'config' . DS . 'config.php')
        ->load('config.php', FALSE);

$config->set('develop', file_exists(ROOT_DIR . DS . '.develop'));

if ($config->get('develop')) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

require 'View.php';

$api = new API(array(
    'mode'      => 'production',
    'log.level' => Slim\Log::ALERT,
    'debug'     => FALSE, // No debug mode at all
    'view'      => new View
));

// ---------------------------------------------------------------------------
// Detect language to use
// ---------------------------------------------------------------------------
// 1st the default
$lang = $config->get('Language', 'en');

// 2nd Check accepted languages
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $l) {
        $l = explode('-', $l);
        if ($l[0] == 'en' OR $l[0] == 'de') {
            $lang = $l[0];
            break;
        }
    }
}

$api->Language = $lang;

/**
 * Setup database connection
 */
$c = $config->get('Database');
slimMVC\MySQLi::setCredentials(
    $c['host'], $c['username'], $c['password'], $c['database'], $c['port'], $c['socket']
);
slimMVC\MySQLi::$SETTINGS_TABLE = 'pvlng_config';

$api->version = substr($api->request()->getRootUri(), 5);

if ($config->get('develop')) {
    $api->config('mode', 'development');
    $api->config('log.level', Slim\Log::INFO);
}

$api->db = slimMVC\MySQLi::getInstance();

$api->cache = Cache::factory(
    array(
        'Directory' => TEMP_DIR,
        'TTL'       => 86400
    ),
    $config->get('Cache')
);

/**
 * Nested set for channel tree
 */
include_once LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

NestedSet::Init(array (
    'db'    => $api->db,
    'debug' => true,
    'lang'  => 'en',
    'path'  => LIB_DIR . DS . 'contrib' . DS . 'messages',
    'db_table' => array (
        'tbl'  => 'pvlng_tree',
        'nid'  => 'id',
        'l'    => 'lft',
        'r'    => 'rgt',
        'mov'  => 'moved',
        'pay'  => 'entity'
    )
));

BabelKitMySQLi::setParams(array( 'table' => 'pvlng_babelkit' ));
BabelKitMySQLi::setDB($api->db);
BabelKitMySQLi::setCache($api->cache);

try {
    I18N::setBabelKit(BabelKitMySQLi::getInstance());
} catch (Exception $e) {
    die('<p>Missing translations!</p><p>Did you loaded '
       .'<tt><strong>sql/pvlng.sql</strong></tt> '
       .'into your database?!</p>');
}

I18N::setLanguage($api->Language);
I18N::setCodeSet('app');
I18N::setAddMissing($config->get('I18N.Add'));
if ($config->get('I18N.Mark')) {
    I18N::setMarkMissing();
}

/**
 * Some defines
 */
$version = file(ROOT_DIR . DS . '.version', FILE_IGNORE_NEW_LINES);
define('PVLNG',              'PhotoVoltaic Logger new generation');
define('PVLNG_VERSION',      $version[0]);
define('PVLNG_VERSION_DATE', $version[1]);

$api->response->headers->set('X-Version', PVLNG_VERSION);
$api->response->headers->set('X-API', $api->version);

// ---------------------------------------------------------------------------
// The helper functions
// ---------------------------------------------------------------------------
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

    // Analyse X-PVLng-DryRun header
    if ($api->dryrun = $api->request->headers->get('X-PVLng-DryRun', FALSE)) {
        $api->contentType('text/plain');
        echo 'Dry run, no data will be saved.', PHP_EOL;
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
$checkLocation = function() use ($api, $config) {
    $api->Latitude  = $config->get('Location.Latitude');
    $api->Longitude = $config->get('Location.Longitude');

    if ($api->Latitude == '' OR $api->Longitude == '') {
        $api->stopAPI('No valid location defined in configuration', 404);
    }
};

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
// The routes
// ---------------------------------------------------------------------------
foreach (glob(API_DIR.DS.'routes'.DS.'*.php') as $routes) include $routes;

/**
 * Let's go
 */
$api->run();

file_exists(API_DIR.DS.'append.php') && include API_DIR.DS.'append.php';
