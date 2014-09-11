<?php
/**
 * Main program file
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

function _redirect( $route ) {
    $protocol = (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']) ? 'https' : 'http';
    die(Header('Location: '.$protocol.'://'.$_SERVER['HTTP_HOST'].'/'.$route));
}

/**
 * Initialize
 */
ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
error_reporting(0);

setlocale(LC_NUMERIC, 'C');
iconv_set_encoding('internal_encoding', 'UTF-8');
mb_internal_encoding('UTF-8');
clearstatcache();

/**
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('BASE_DIR', dirname(__FILE__));
define('ROOT_DIR', dirname(BASE_DIR));
define('CONF_DIR', ROOT_DIR . DS . 'config');
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('APP_DIR',  ROOT_DIR . DS . 'frontend');

// Outside document root!
define('TEMP_DIR', ROOT_DIR . DS . 'tmp');

file_exists(CONF_DIR . DS . 'config.php') || _redirect('public/setup.php');

file_exists(ROOT_DIR . DS . 'prepend.php') && include ROOT_DIR . DS . 'prepend.php';

/**
 * Initialize Auto-Loader
 */
include LIB_DIR . DS . 'Loader.php';

Loader::register(
    array(
        'path'    => array(CORE_DIR, LIB_DIR, APP_DIR),
        'pattern' => array('%s.php'),
        'exclude' => array('contrib/')
    ),
    TEMP_DIR
);

$config = slimMVC\Config::getInstance()
        ->load(CONF_DIR . DS . 'config.default.php')
        ->load(CONF_DIR . DS . 'config.php');

$config->set('develop', file_exists(ROOT_DIR . DS . '.develop'));

/**
 * Check Admin config
 */
if ($config->get('Admin.User') == '' AND
    isset($_SERVER['REQUEST_URI']) AND strpos($_SERVER['REQUEST_URI'], '/adminpass') === FALSE) {
    _redirect('adminpass');
}

/**
 * Initialize cache
 */
$cache = Cache::factory(
    array(
        'Directory' => TEMP_DIR,
        'TTL'       => 86400
    ),
    $config->get('Cache')
);

/**
 * Check mobile client
 */
if (isset($_SERVER['REQUEST_URI']) AND
    substr($_SERVER['REQUEST_URI'], 0,  2) != '/m' AND
    substr($_SERVER['REQUEST_URI'], 0, 10) != '/infoframe' AND
    isset($_SERVER['HTTP_USER_AGENT']) AND $useragent = $_SERVER['HTTP_USER_AGENT']) {

    // Remember User Agent and make not for every call the preg_match()...
    while ($cache->save('isMobileBrowser.'.substr(md5($useragent),-7), $isMobile)) {
        $isMobile = array(
            (
            // http://detectmobilebrowsers.com/download/php - 2013/10/04
            preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))
            ),
            $useragent
        );
    }

    if ($isMobile[0]) _redirect('m');
}

if ($config->get('develop')) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

// ---------------------------------------------------------------------------
// Let's go
// ---------------------------------------------------------------------------
Session::start($config->get('Cookie.Name', 'PVLng'));

/**
 * Run in /public - fake SCRIPT_NAME for correct Slim routing
 */
$_SERVER['SCRIPT_NAME'] = '/';

$app = new slimMVC\App();

$app->config = $config;
$app->cache  = $cache;

/**
 * Database
 */
$c = $config->get('Database');
slimMVC\MySQLi::setCredentials(
    $c['host'], $c['username'], $c['password'], $c['database'], $c['port'], $c['socket']
);
slimMVC\MySQLi::$SETTINGS_TABLE = 'pvlng_config';

try {
    // Try connect to database
    $app->db = slimMVC\MySQLi::getInstance();
} catch (Exception $e) {
    _redirect('public/setup.php');
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

// 3rd check the request parameters
$app->Language = Session::checkRequest('lang', $lang) ?: 'en';

/**
 * BBCode parser
 */
include LIB_DIR . DS . 'contrib' . DS . 'nbbc.php';

I18N::setBBCode(new BBCode);

/**
 * Nested set for channel tree
 */
include LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

NestedSet::Init(array(
    'db'       => $app->db,
    'debug'    => true,
    'lang'     => 'en',
    'path'     => LIB_DIR . DS . 'contrib' . DS . 'messages',
    'db_table' => array (
        'tbl' => 'pvlng_tree',
        'nid' => 'id',
        'l'   => 'lft',
        'r'   => 'rgt',
        'mov' => 'moved',
        'pay' => 'entity'
    )
));

BabelKitMySQLi::setParams(array( 'table' => 'pvlng_babelkit' ));
BabelKitMySQLi::setDB($app->db);
BabelKitMySQLi::setCache($cache);

try {
    I18N::setBabelKit(BabelKitMySQLi::getInstance());
} catch (Exception $e) {
    die('<p>Missing translations!</p><p>Did you loaded '
       .'<tt><strong>sql/pvlng.sql</strong></tt> '
       .'into your database?!</p>');
}

I18N::setLanguage($app->Language);
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

/**
 * Check for upgrade and delete user cache if required
 */
if ($cache->AppVersion != PVLNG_VERSION) {
    $cache->flush();
    $cache->AppVersion = PVLNG_VERSION;
}

if (isset($_COOKIE[Session::token()])) {
    // Ok, remembered user
    Session::set('user', $config->get('Admin.User'));
    Controller\Admin::RememberLogin();
}

$app->showStats = TRUE;

// ---------------------------------------------------------------------------
// Authenticate user if required
// ---------------------------------------------------------------------------
$checkAuth = function( Slim\Route $route ) use ($app) {
    // Check logged in user
    if (Session::get('user') !== $app->config->get('Admin.User')) {
        $app->redirect('/');
    }
};

// ---------------------------------------------------------------------------
// Route not found, redirect to index instead
// ---------------------------------------------------------------------------
$app->notFound(function() use ($app) {
    $app->redirect('/');
});

/**
 *
 */
Slim\Route::setDefaultConditions(array(
    'guid' => '(\w{4}-){7}\w{4}',
    'id'   => '\d+',
    'slug' => '[@\w\d-]+'
));

// ---------------------------------------------------------------------------
// Modules: Menu and route definitions
// ---------------------------------------------------------------------------
foreach (glob(APP_DIR.DS.'Application'.DS.'*.php') as $file) include $file;

/**
 * Run application
 */
$app->run();

if ($app->showStats) {
    /**
     * Some statistics
     */
    printf(PHP_EOL.PHP_EOL
          .'<!-- Build time: %.0f ms / Queries: %d (%.0f ms) / Memory: %.0f kByte -->'.PHP_EOL,
           (microtime(TRUE)-$_SERVER['REQUEST_TIME'])*1000,
           slimMVC\MySQLi::$QueryCount, slimMVC\MySQLi::$QueryTime, memory_get_peak_usage(TRUE)/1024);
}

file_exists(ROOT_DIR . DS . 'append.php') && include ROOT_DIR . DS . 'append.php';
