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

file_exists('..'.DIRECTORY_SEPARATOR.'prepend.php') && include '..'.DIRECTORY_SEPARATOR.'prepend.php';

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

if (!file_exists(CONF_DIR . DS . 'config.php')) _redirect('public/setup.php');

/**
 * Check mobile client
 */
if (isset($_SERVER['PATH_INFO']) AND substr($_SERVER['PATH_INFO'],0,2) != '/m' AND
    isset($_SERVER['HTTP_USER_AGENT']) AND $useragent = $_SERVER['HTTP_USER_AGENT']) {
    /**
     * http://detectmobilebrowsers.com/download/php
     * 2013/10/04
     */
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
        _redirect('m');
    }
}

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
        ->load(CONF_DIR . DS . 'config.app.php')
        ->load(CONF_DIR . DS . 'config.php');

if ($config->get('develop')) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

if ($config->get('Admin.User') == '' AND
    (!isset($_SERVER['REQUEST_URI']) OR strpos($_SERVER['REQUEST_URI'], '/adminpass') === FALSE)) {
    _redirect('adminpass');
}

Session::start($config->get('Cookie.Name', 'PVLng'));

// ---------------------------------------------------------------------------
// Let's go
// ---------------------------------------------------------------------------

/**
 * Run in /public - fake SCRIPT_NAME for correct Slim routing
 */
$_SERVER['SCRIPT_NAME'] = '/';

$app = new slimMVC\App();

$app->config = $config;

/**
 * Database
 */
slimMVC\MySQLi::setHost($config->get('Database.Host'));
slimMVC\MySQLi::setPort($config->get('Database.Port'));
slimMVC\MySQLi::setSocket($config->get('Database.Socket'));
slimMVC\MySQLi::setUser($config->get('Database.Username'));
slimMVC\MySQLi::setPassword($config->get('Database.Password'));
slimMVC\MySQLi::setDatabase($config->get('Database.Database'));
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
Session::checkRequest('lang', $lang);

define('LANGUAGE', Session::get('lang'));

$app->cache = Cache::factory(
    array(
        'Token'     => 'PVLng',
        'Directory' => TEMP_DIR,
    ),
    $config->get('Cache')
);

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

BabelKitMySQLi::setParams(array(
    'table' => 'pvlng_babelkit'
));
BabelKitMySQLi::setDB($app->db);
BabelKitMySQLi::setCache($app->cache);

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
define('PVLNG', 'PhotoVoltaic Logger new generation');
$version = file(ROOT_DIR . DS . '.version', FILE_IGNORE_NEW_LINES);
define('PVLNG_VERSION',      $version[0]);
define('PVLNG_VERSION_DATE', $version[1]);

/**
 * Check for upgrade and delete user cache if required
 */
if ($app->cache->AppVersion != PVLNG_VERSION) {
    $app->cache->flush();
    $app->cache->AppVersion = PVLNG_VERSION;
}

if (isset($_COOKIE[Session::token()])) {
    // Ok, remembered user
    Session::set('user', $app->config->get('Admin.User'));
    Controller\Admin::RememberLogin();
}

$app->showStats = TRUE;

// ---------------------------------------------------------------------------
// Authenticate user if required
// ---------------------------------------------------------------------------
$checkAuth = function( Slim\Route $route ) use ($app) {
    // Check valid logged in user
    if (Session::get('user') !== $app->config->get('Admin.User')) {
        Session::set('returnto', $route->getPattern());
        $app->redirect('/login');
    }
};

// ---------------------------------------------------------------------------
// Route not found, redirect to index instead
// ---------------------------------------------------------------------------
$app->notFound(function() use ($app) {
    $app->redirect('/');
});

// ---------------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------------
$app->map('/login', function() use ($app) {
    $app->process('Admin', 'Login');
})->via('GET', 'POST');

$app->any('/logout', function() use ($app) {
    $app->process('Admin', 'Logout');
});

$app->map('/adminpass', function() use ($app) {
    $app->process('Admin', 'AdminPassword');
})->via('GET', 'POST');

$app->map('/_config', $checkAuth, function() use ($app) {
    $app->process('Admin', 'Config');
})->via('GET', 'POST');

// ---------------------------------------------------------------------------
// Index
// ---------------------------------------------------------------------------
// User check is done inside controller, only save and delete needs login!
$app->map('/', function() use ($app) {
    $app->process();
})->via('GET', 'POST');

$app->map('/index', function() use ($app) {
    $app->process();
})->via('GET', 'POST');

$app->get('/index(/:view)', function( $view='' ) use ($app) {
    // Put chart name at the begin
    $params = array_merge(
        array('chart' => $view),
        $app->request->get()
    );
    $app->redirect('/?' . http_build_query($params));
});

$app->get('/chart/:view', function( $view ) use ($app) {
    // Put chart name at the begin
    $params = array_merge(
        array('chart' => $view),
        $app->request->get()
    );
    $app->redirect('/?' . http_build_query($params));
});

// ---------------------------------------------------------------------------
// Dashboard
// ---------------------------------------------------------------------------
$app->map('/dashboard', $checkAuth, function() use ($app) {
    $app->process('Dashboard');
})->via('GET', 'POST');

$app->get('/dashboard/embedded', function() use ($app) {
    $app->process('Dashboard', 'IndexEmbedded');
});

$app->get('/ed', function() use ($app) {
    $app->process('Dashboard', 'IndexEmbedded');
});

$app->get('/md', function() use ($app) {
    $app->process('Dashboard', 'IndexEmbedded');
});

// ---------------------------------------------------------------------------
// List
// ---------------------------------------------------------------------------
$app->get('/list(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->params->set('id', $id);
    $app->process('Lists');
});

// ---------------------------------------------------------------------------
// Overview
// ---------------------------------------------------------------------------
$app->get('/overview', $checkAuth, function() use ($app) {
    $app->process('Overview');
});

$app->post('/overview/:action', $checkAuth, function( $action ) use ($app) {
    // Tree manipulation requests
    $app->process('Overview', $action);
});

// ---------------------------------------------------------------------------
// Channel
// ---------------------------------------------------------------------------
$app->get('/channel', $checkAuth, function() use ($app) {
    $app->process('Channel');
});

$app->map('/channel/add(/:clone)', $checkAuth, function( $clone=0 ) use ($app) {
    $app->params->set('clone', $clone);
    $app->process('Channel', 'Add');
})->via('GET', 'POST');

$app->get('/channel/edit/:id', $checkAuth, function( $id ) use ($app) {
    $app->params->set('id', $id);
    $app->process('Channel', 'Edit');
});

$app->post('/channel/alias', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Alias');
});

$app->post('/channel/edit', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Edit');
});

$app->post('/channel/delete', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Delete');
});

// ---------------------------------------------------------------------------
// Info
// ---------------------------------------------------------------------------
$app->map('/info', $checkAuth, function() use ($app) {
    $app->process('Info');
})->via('GET', 'POST');

// ---------------------------------------------------------------------------
// Description
// ---------------------------------------------------------------------------
$app->get('/description', function() use ($app) {
    $app->process('Description');
});

// ---------------------------------------------------------------------------
// Mobile
// ---------------------------------------------------------------------------
$app->get('/m', function() use ($app) {
    $app->process('Mobile');
});

// ---------------------------------------------------------------------------
// Other
// ---------------------------------------------------------------------------
$app->get('/widget.inc.js', function() use ($app) {
    $app->showStats = FALSE;
    $app->process('Widget', 'Inc');
});

$app->get('/widget.js', function() use ($app) {
    $app->showStats = FALSE;
    $app->process('Widget', 'Chart');
});

/**
 *
 */
$app->get('/clearcache', $checkAuth, function() use ($app) {
    $app->showStats = FALSE;
    shell_exec('rm '.TEMP_DIR.DS.'*');
    echo '<p>Cached templates cleared from <tt>'.TEMP_DIR.'</tt></p>';
    echo '<p>Cache stats</p><ul>';
    foreach ($app->cache->info() as $key=>$value) {
        echo '<li>', ucwords(str_replace('_', ' ', $key)), ' : <tt>', $value, '</tt></li>';
    }
    echo '</ul><p><a href="/">back</a></p>';
});

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
