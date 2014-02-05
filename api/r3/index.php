<?php
/**
 * Main API file
 *
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2013 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

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
        ->load(ROOT_DIR . DS . 'config' . DS . 'config.app.php')
        ->load(ROOT_DIR . DS . 'config' . DS . 'config.php')
        ->load('config.php', false);

if ($config->get('develop')) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

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

define('LANGUAGE', $lang);

/**
 * Setup database connection
 */
slimMVC\MySQLi::setHost($config->get('Database.Host'));
slimMVC\MySQLi::setPort($config->get('Database.Port'));
slimMVC\MySQLi::setSocket($config->get('Database.Socket'));
slimMVC\MySQLi::setUser($config->get('Database.Username'));
slimMVC\MySQLi::setPassword($config->get('Database.Password'));
slimMVC\MySQLi::setDatabase($config->get('Database.Database'));
slimMVC\MySQLi::$SETTINGS_TABLE = 'pvlng_config';

require 'View.php';

$api = new API(array(
    'mode'      => 'production',
    'log.level' => Slim\Log::ALERT,
    'debug'     => FALSE,
    'view'      => new View
));

if ($config->get('develop')) {
    $api->config('mode', 'development');
    $api->config('log.level', Slim\Log::INFO);
}

$api->db = slimMVC\MySQLi::getInstance();

$api->cache = Cache::factory(
    array(
        'Token'     => 'PVLng',
        'Directory' => TEMP_DIR,
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

BabelKitMySQLi::setParams(array(
    'table' => 'pvlng_babelkit'
));
BabelKitMySQLi::setDB($api->db);
BabelKitMySQLi::setCache($api->cache);

try {
    I18N::setBabelKit(BabelKitMySQLi::getInstance());
} catch (Exception $e) {
    die('<p>Missing translations!</p><p>Did you loaded '
       .'<tt><strong>sql/pvlng.sql</strong></tt> '
       .'into your database?!</p>');
}

I18N::setLanguage(LANGUAGE);
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
$api->response->headers->set('X-API-Version', 'r2');

// ---------------------------------------------------------------------------
// The helper functions and routes
// ---------------------------------------------------------------------------
include 'functions.php';
include 'route.help.php';
include 'route.attributes.php';
include 'route.channel.php';
include 'route.data.php';
include 'route.batch.php';
include 'route.csv.php';
include 'route.log.php';
include 'route.daylight.php';
include 'route.json.php';
include 'route.view.php';
include 'route.hash.php';
include 'route.status.php';

if (file_exists('route.custom.php')) include 'route.custom.php';

/**
 * Let's go
 */
$api->run();
