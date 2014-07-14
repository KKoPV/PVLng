<?php
/**
 * Main API file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

file_exists('prepend.php') && include 'prepend.php';

setlocale(LC_NUMERIC, 'C');

/**
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('TEMP_DIR', ROOT_DIR . DS . 'tmp');

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
     *
     */
    public function strParam( $name, $default ) {
        $value = trim($this->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     *
     */
    public function intParam( $name, $default ) {
        $value = trim($this->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     *
     */
    public function boolParam( $name, $default ) {
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
    'debug'     => FALSE,
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
        'Token'     => 'PVLng',
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
// The helper functions and routes
// ---------------------------------------------------------------------------
include 'functions.php';
foreach (glob('routes'.DS.'*.php') as $routes) include $routes;
file_exists('route.custom.php') && include 'route.custom.php';

/**
 * Let's go
 */
$api->run();

file_exists('append.php') && include 'append.php';
