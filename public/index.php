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
 * Initialize
 */
require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'bootstrap.php']);

$loader = PVLng\PVLng::bootstrap();
Loader::register($loader, PVLng\PVLng::$TempDir);

if (!file_exists(PVLng\PVLng::path(PVLng\PVLng::$RootDir, 'config', 'config.php'))) {
    die(Header('Location: /public/setup.php'));
}

$file = PVLng\PVLng::path(PVLng\PVLng::$RootDir, 'prepend.php');
if (file_exists($file)) {
    include $file;
}

if (PVLng\PVLng::$DEVELOP) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
} else {
    ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
    error_reporting(0);
}

if (PVLng\PVLng::$DEVELOP == 2) {
    include PVLng\PVLng::path(PVLng\PVLng::$RootDir, 'lib', 'AOP.php');
}

// ---------------------------------------------------------------------------
// Let's go
// ---------------------------------------------------------------------------
Core\Session::$tokenName = md5($_SERVER['HTTP_USER_AGENT']);
Core\Session::$debug     = PVLng\PVLng::$DEVELOP;
Core\Session::start();

if (!Core\Session::valid()) {
    Core\Session::destroy();
    Core\Session::start();
}

/**
 * Run in /public - fake SCRIPT_NAME for correct Slim routing
 */
$_SERVER['SCRIPT_NAME'] = '/';

$app = new slimMVC\App(array(
    'mode'        => PVLng\PVLng::$DEVELOP ? 'development' : 'production',
    'log.level'   => PVLng\PVLng::$DEVELOP ? Slim\Log::INFO : Slim\Log::ALERT,
    'debug'       => PVLng\PVLng::$DEVELOP
));

$app->container->singleton('JavaScriptPacker', function () {
    return new JavaScriptPacker(0);
});

$app->view->setCacheDirectory(PVLng\PVLng::$TempDir);

$app->view->Helper = new slimMVC\ViewHelper;
$app->view->Helper->numf = function ($number, $decimals = 0) {
    return number_format($number, $decimals, I18N::translate('DSEP'), \I18N::translate('TSEP'));
};

$app->view->Helper->raw = function ($value) {
    return $value;
};

// If installed from GitHub, find branch and actual commit
$file = PVLng\PVLng::path(PVLng\PVLng::$RootDir, '.git', 'HEAD');
if (file_exists($file) &&
    ($head = file($file, FILE_IGNORE_NEW_LINES)) &&
    preg_match('~: (.*?)([^/]+)$~', $head[0], $args)
) {
    $app->view->PVLng_Branch = trim($args[2]);
    $file = PVLng\PVLng::path(PVLng\PVLng::$RootDir, '.git', $args[1].$args[2]);
    $app->view->PVLng_Commit = trim(file_get_contents($file));
}

/**
 * Configuration
 */
$app->container->singleton('config', function () {
    return PVLng\PVLng::getConfig();
});

/**
 * Database
 */
$app->container->singleton('db', function () use ($app) {
    try {
        $db = PVLng\PVLng::getDatabase();
    } catch (Exception $e) {
        $app->redirect('/public/setup.php');
    }
    return $db;
});

/**
 * Cache
 */
$app->container->singleton('cache', function () use ($app) {
    return PVLng\PVLng::getCache();
});

/**
 * Nested set for channel tree
 */
$app->container->singleton('tree', function () use ($app) {
    return Nestedset::getInstance();
});

$app->container->singleton('menu', function () {
    return new \PVLng\Menu;
});

$app->container->singleton('languages', function () {
    return new \PVLng\Language;
});

// ---------------------------------------------------------------------------
// Hooks
// ---------------------------------------------------------------------------
$app->hook(
    'slim.before.dispatch',
    function () use ($app) {
        Yryie\Yryie::Debug('slim.before.dispatch');
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
        if (substr($pattern, 0, 2) != '/m' &&
            substr($pattern, 0, 10) != '/infoframe' &&
            $useragent = $app->request()->getUserAgent()) {
            // Remember User Agent and make not for every call the preg_match()...
            while ($app->cache->save('isMobileBrowser.'.substr(md5($useragent), -7), $isMobile)) {
                $isMobile = array(
                    // http://detectmobilebrowsers.com/download/php - 2013/10/04
                    // @codingStandardsIgnoreLine
                    preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))
                    ,$useragent
                );
            }

            if ($isMobile[0]) {
                $app->redirect('/m');
            }
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

        Core\Localizer::setThousandSeparator($app->config->get('Locale.ThousandSeparator'));
        Core\Localizer::setDecimalPoint($app->config->get('Locale.DecimalPoint'));

        // Transform posted data from local format
        if ($app->Request()->isPost()) {
            // Force creation of environment['slim.request.form_hash']
            $app->request->post();

            if ($post = $app->environment['slim.request.form_hash']) {
                if (!isset($post['$raw'])) {
                    $post['$raw'] = $post;
                }
                foreach ($post['$raw'] as $key => $value) {
                    $post[$key] = Core\Localizer::fromLocale($value);
                }
                $app->environment['slim.request.form_hash'] = $post;
            }
        }
    }
);

// ---------------------------------------------------------------------------
// Authenticate user if required
// ---------------------------------------------------------------------------
$checkAuth = function (Slim\Route $route) use ($app) {
    // Check logged in user
    if (!$app->user) {
        Core\Messages::Info(I18N::translate('LoginRequired'));
        Core\Session::set('returnto', $_SERVER['REQUEST_URI']);
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
$filemask = PVLng\PVLng::path(PVLng\PVLng::$RootDir, 'core', 'Frontend', 'Application', '*.php');
foreach (glob($filemask) as $file) {
    include_once Loader::applyCallback($file);
}

/**
 * Route not found, redirect to index instead
 */
$app->notFound(function () use ($app) {
    $app->redirect('/');
});

// Register AOP as outer-most middleware
if (PVLng\PVLng::$DEVELOP == 2) {
    $app->add(new YryieMiddleware);
}

/**
 * Check for upgrade and delete user cache if required
 */
if ($app->cache->AppVersion != PVLNG_VERSION) {
    $app->cache->flush();
    $app->cache->AppVersion = PVLNG_VERSION;
}

// Transform data for view into local format
$app->view->setRenderValueCallback(function ($value) {
    return Core\Localizer::toLocale($value);
});

$app->user = Core\Session::loggedIn($app->config->get('Core.Password'));

$app->showStats = true;

/**
 * Run application
 */
$app->run();

Core\Session::close();

PVLng\PVLng::sendStatistics();

/**
 * Some statistics
 */
if ($app->showStats) {
    printf(
        PHP_EOL.'<!-- Build time: %.0f ms / Queries: %d (%.0f ms) / Memory: %.0f kByte -->',
        (microtime(true)-$_SERVER['REQUEST_TIME'])*1000,
        $app->db->getQueryCount(), $app->db->getQueryTime(), memory_get_peak_usage(true)/1024
    );
}

$file = PVLng\PVLng::path(PVLng\PVLng::$RootDir, 'append.php');
if (file_exists($file)) {
    include $file;
}
