<?php
/**
 * Main program file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

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
define('TEMP_DIR', ROOT_DIR . DS . 'tmp'); // Outside document root!

$version = file(ROOT_DIR . DS . '.version', FILE_IGNORE_NEW_LINES);
define('PVLNG',              'PhotoVoltaic Logger new generation');
define('PVLNG_VERSION',      $version[0]);
define('PVLNG_VERSION_DATE', $version[1]);

function _redirect( $route ) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http';
    die(Header('Location: '.$protocol.'://'.$_SERVER['HTTP_HOST'].$route));
}

/**
 * Initialize
 */
file_exists(ROOT_DIR . DS . 'prepend.php') && include ROOT_DIR . DS . 'prepend.php';

setlocale(LC_NUMERIC, 'C');
mb_internal_encoding('UTF-8');
clearstatcache();

defined('DEVELOP') || define('DEVELOP', false);

if (DEVELOP) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
} else {
    ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
    error_reporting(0);
}

file_exists(CONF_DIR . DS . 'config.php') || _redirect('/public/setup.php');

/**
 * Initialize Auto-Loader
 */
$loader = require_once ROOT_DIR . DS . 'vendor' . DS . 'autoload.php';
$loader->addPsr4('', array(CORE_DIR, LIB_DIR, APP_DIR));

Loader::register($loader, TEMP_DIR);

if (DEVELOP == 2) include CORE_DIR . DS . 'AOP.php';

// ---------------------------------------------------------------------------
// Let's go
// ---------------------------------------------------------------------------
Session::$tokenName = md5($_SERVER['HTTP_USER_AGENT']);
Session::$debug = DEVELOP;
Session::start();

if (!Session::valid()) {
    Session::destroy();
    Session::start();
}

/**
 * Run in /public - fake SCRIPT_NAME for correct Slim routing
 */
$_SERVER['SCRIPT_NAME'] = '/';

$app = new slimMVC\App(array(
    'mode'        => DEVELOP ? 'development' : 'production',
    'log.level'   => DEVELOP ? Slim\Log::INFO : Slim\Log::ALERT,
    'debug'       => DEVELOP
));

// If installed from GitHub, find branch and actual commit
$head = file(ROOT_DIR.DS.'.git'.DS.'HEAD', FILE_IGNORE_NEW_LINES);
if (!empty($head) && preg_match('~: (.*?)([^/]+)$~', $head[0], $args)) {
    $app->view->PVLng_Branch = trim($args[2]);
    $app->view->PVLng_Commit = trim(file_get_contents(ROOT_DIR.DS.'.git'.DS.$args[1].$args[2]));
}

include CORE_DIR . DS . 'Hooks.php';

/**
 * Configuration
 */
$app->container->singleton('config', function() {
    return (new \slimMVC\Config)
           ->load(CONF_DIR . DS . 'config.default.php')
           ->load(CONF_DIR . DS . 'config.php');
});

/**
 * Database
 */
$app->container->singleton('db', function() use ($app) {
    extract($app->config->get('Database'), EXTR_REFS);
    try {
        $db = new \slimMVC\MySQLi($host, $username, $password, $database, $port, $socket);
    } catch (Exception $e) {
        $app->redirect('/public/setup.php');
    }
    $db->setSettingsTable('pvlng_config');
    return $db;
});

/**
 * Cache
 */
$app->container->singleton('cache', function() use ($app) {
    return \Cache::factory(
        array('Directory' => TEMP_DIR, 'TTL' => 86400),
        $app->config->get('Cache', 'MemCache,APC,File')
    );
});

/**
 * Nested set for channel tree
 */
$app->container->singleton('tree', function() use ($app) {
    return Nestedset::getInstance();
});

$app->container->singleton('menu', function() {
    return new \PVLng\Menu;
});

$app->container->singleton('languages', function() {
    return new \PVLng\Language;
});

// ---------------------------------------------------------------------------
// Hooks
// ---------------------------------------------------------------------------
$app->hook('slim.before', function() use ($app) {
    Yryie::Debug('slim.before');

    slimMVC\ORM::setDatabase($app->db);

    foreach ((new ORM\SettingsKeys)->find() as $setting) {
        $app->config->set($setting->getKey(), $setting->getValue());
    }

    /**
     * Check for upgrade and delete user cache if required
     */
    if ($app->cache->AppVersion != PVLNG_VERSION) {
        $app->cache->flush();
        $app->cache->AppVersion = PVLNG_VERSION;
    }

    I18N::setCodeSet('app');
    I18N::setAddMissing($app->config->get('I18N.Add'));

    BabelKitMySQLi::setParams(array('table' => 'pvlng_babelkit'));
    BabelKitMySQLi::setDB($app->db);
    BabelKitMySQLi::setCache($app->cache);

    try {
        I18N::setBabelKit(BabelKitMySQLi::getInstance());
    } catch (Exception $e) {
        die('<p>Missing translations!</p><p>Did you loaded '
           .'<tt><strong>sql/pvlng.sql</strong></tt> '
           .'into your database?!</p>');
    }

    /**
     * BBCode parser
     */
    include_once LIB_DIR . DS . 'contrib' . DS . 'nbbc.php';

    I18N::setBBCode(new BBCode);

    /**
     * Nested set for channel tree
     */
    include_once LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

    NestedSet::Init(array(
        'db'       => $app->db,
        'debug'    => TRUE,
        'lang'     => 'en',
        'path'     => LIB_DIR.DS.'contrib'.DS.'messages',
        'db_table' => array (
            'tbl' => 'pvlng_tree',
            'nid' => 'id', 'l' => 'lft', 'r' => 'rgt', 'mov' => 'moved', 'pay' => 'entity'
        )
    ));

    // Transform data for view into local format
    $app->view->setRenderValueCallback(function($value) {
        return Localizer::toLocale($value);
    });

    $app->user = Session::loggedIn($app->config->get('Core.Password'));

    $app->showStats = true;

}, 1);

/**
 *
 */
$app->hook('slim.before.dispatch', function() use ($app) {
    Yryie::Debug('slim.before.dispatch');
    $route = $app->Router()->getCurrentRoute();
    $pattern = $route->getPattern();

    /**
     * Check Admin config
     */
    if ($app->config->get('Core.Password') == '' && strpos($pattern, '/adminpass') === false) {
        $app->redirect('/adminpass');
    }

    /**
     * Check location
     */
    if (($app->config->get('Core.Latitude') == '' || $app->config->get('Core.Longitude') == '') &&
        strpos($pattern, '/login') === false && strpos($pattern, '/adminpass') === false &&
        strpos($pattern, '/location') === false) {
        $app->redirect('/location');
    }

    /**
     * Check mobile client
     */
    if (substr($pattern, 0,  2) != '/m' &&
        substr($pattern, 0, 10) != '/infoframe' &&
        $useragent = $app->request()->getUserAgent()) {

        // Remember User Agent and make not for every call the preg_match()...
        while ($app->cache->save('isMobileBrowser.'.substr(md5($useragent),-7), $isMobile)) {
            $isMobile = array(
                // http://detectmobilebrowsers.com/download/php - 2013/10/04
                preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))
                ,$useragent
            );
        }

        if ($isMobile[0]) $app->redirect('/m');
    }

    if (isset($route->Language)) {
        $app->Language = $route->Language;
    } else {
        // -------------------------------------------------------------------
        // Detect language to use
        // -------------------------------------------------------------------
        if ($lang = $app->request()->get('lang')) {
            $app->Language = $lang;
        } elseif (array_key_exists('language', $_COOKIE)) {
            // Check cookie
            $app->Language = $_COOKIE['language'];
        } else {
            $lang = '';
            // Check accepted languages
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $l) {
                    $l = explode('-', $l);
                    if ($l[0] == 'en' || $l[0] == 'de') {
                        $lang = $l[0];
                        break;
                    }
                }
            }
            $app->Language = $lang ?: $app->config->get('Core.Language', 'en');
        }
        setcookie('language', $app->Language, time()+30*24*60*60);
    }

    I18N::setLanguage($app->Language);

    // Init locale settings
    foreach (BabelKitMySQLi::getInstance()->full_set('locale', $app->Language) as $row) {
        $app->config->set('Locale.'.$row[0], $row[1]);
    }

    Localizer::setThousandSeparator($app->config->get('Locale.ThousandSeparator'));
    Localizer::setDecimalPoint($app->config->get('Locale.DecimalPoint'));

    // Transform posted data from local format
    if ($app->Request()->isPost()) {
        // Force creation of environment['slim.request.form_hash']
        $app->request->post();

        if ($post = $app->environment['slim.request.form_hash']) {
            if (!isset($post['$raw'])) $post['$raw'] = $post;
            foreach ($post['$raw'] as $key=>$value) {
                $post[$key] = Localizer::fromLocale($value);
            }
            $app->environment['slim.request.form_hash'] = $post;
        }
    }

});

// ---------------------------------------------------------------------------
// Authenticate user if required
// ---------------------------------------------------------------------------
$checkAuth = function( Slim\Route $route ) use ($app) {
    // Check logged in user
    if (!$app->user) {
        Messages::Info(__('LoginRequired'));
        \Session::set('returnto', $_SERVER['REQUEST_URI']);
        $app->redirect('/login');
    }
};

// ---------------------------------------------------------------------------
// Declare default conditions before routes
// ---------------------------------------------------------------------------
Slim\Route::setDefaultConditions(array(
    'guid' => '(\w{4}-){7}\w{4}',
    'id'   => '\d+',
    'slug' => '[@\w\d-]+'
));

// ---------------------------------------------------------------------------
// Modules: Menus and route definitions
// ---------------------------------------------------------------------------
foreach (glob(APP_DIR.DS.'Application'.DS.'*.php') as $file) include Loader::applyCallback($file);

/**
 * Route not found, redirect to index instead
 */
$app->notFound(function() use ($app) {
    $app->redirect('/');
});

// Register AOP as outer-most middleware
if (DEVELOP == 2) $app->add(new YryieMiddleware);

/**
 * Run application
 */
$app->run();

Session::close();

PVLng::sendStatistics();

/**
 * Some statistics
 */
if ($app->showStats) {
    printf(PHP_EOL.'<!-- Build time: %.0f ms / Queries: %d (%.0f ms) / Memory: %.0f kByte -->',
           (microtime(TRUE)-$_SERVER['REQUEST_TIME'])*1000,
           $app->db->getQueryCount(), $app->db->getQueryTime(), memory_get_peak_usage(TRUE)/1024);
}

file_exists(ROOT_DIR . DS . 'append.php') && include ROOT_DIR . DS . 'append.php';
