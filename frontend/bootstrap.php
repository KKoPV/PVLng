<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0-1-g78248d6 2013-04-28 20:54:02 +0200 Knut Kohl $
 */

if (!isset($_SERVER['PATH_INFO'])) $_SERVER['PATH_INFO'] = '';

if (isset($_SERVER['HTTP_USER_AGENT']) AND
    substr($_SERVER['PATH_INFO'],0,2) != '/m' AND
    substr($_SERVER['PATH_INFO'],0,4) != '/api') {
	/**
	 * http://detectmobilebrowsers.com/download/php
	 * 2013/04/10
	 */
	$useragent=$_SERVER['HTTP_USER_AGENT'];

	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))

	{ Header('Location: /m'); exit; }
}

ErrorHandler::register();

$config = ROOT_DIR . DS . 'config' . DS . 'config.php';

if (!file_exists($config)) {
	header('Content-Type: text/plain');
	echo 'Missing config file!', PHP_EOL, PHP_EOL,
	     'Please refer to config/config.php.dist for details!';
	exit(1);
}

$config = yMVC\Config::getInstance()->load($config);

/**
 * Setup database connection
 */

yMVC\MySQLi::setCredentials($config->get('Database.Username'),
                            $config->get('Database.Password'),
                            $config->get('Database.Database'),
                            $config->get('Database.Host'));

yMVC\MySQLi::$SETTINGS_TABLE = 'pvlng_config';

Registry::$NameSpaceSeparator = '.';

try {
	Registry::set('db', yMVC\MySQLi::getInstance());
} catch (Exception $e) {
	header('Content-Type: text/plain');
	echo $e->getMessage(), PHP_EOL, PHP_EOL,
	     'Please refer to config/config.php for details!';
	exit(1);
}

if ($config->get('DEV')) {
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);
}

define('API', (strpos($_SERVER['PATH_INFO'], '/api') !== FALSE));

if (!API) {
	// Don't use session for API calls
	Session::start($config->get('Cookie.Name', 'PVLng'));

	// Detect language to use
	// 1st the default
	$lang = $config->get('Language', 'en');

	// 2nd Check accepted languages
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $l) {
			$l = explode('-', $l);
			if (in_array($l[0], array('de', 'en'))) {
				$lang = $l[0];
				break;
			}
		}
	}

	// 3rd check the request parameters
	Session::checkRequest('lang', $lang);

	define('LANGUAGE', Session::get('lang'));
} else {
	define('LANGUAGE', 'en');
}

/*
$locales = array('de_DE', 'de', 'en_EN', 'en');

foreach ($locales as $locale) {
	if (setlocale(LC_ALL, $locale . '.utf-8') OR setlocale(LC_ALL, $locale)) break;
}
*/

yMVC\ORMTable::cache(TEMP_DIR);

// iconv encoding
iconv_set_encoding('internal_encoding', 'UTF-8');

// multibyte encoding
mb_internal_encoding('UTF-8');

clearstatcache();

/**
 * BBCode parser
 */
include_once LIB_DIR . DS . 'contrib' . DS . 'nbbc.php';

I18N::setBBCode( new BBCode );

/**
 * Nested set for channel tree
 */
include_once LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

/**
 * Some defines
 */
define('PVLNG', 'PhotoVoltaic Logger new generation');
$version = file(ROOT_DIR . DS . '.version', FILE_IGNORE_NEW_LINES);
define('PVLNG_VERSION',      $version[0]);
define('PVLNG_VERSION_DATE', $version[1]);

// ---------------------------------------------------------------------------
// FUNCTIONS
// ---------------------------------------------------------------------------
register_shutdown_function(function() {
	// probability 0.1%
	if (rand(1, 1000) > 1) return;

	$iterator = new DirectoryIterator(TEMP_DIR);
	// Last accessed before more than 1 day
	$maxAge = time() - 24*60*60;
	foreach ($iterator as $fileinfo) {
		if ($fileinfo->isFile() AND $fileinfo->getFilename() != '.githold' AND
		    $fileinfo->getATime() < $maxAge)
			unlink($fileinfo->getPathname());
	}
});

/**
 *
 */
function dbg() {
	$params = func_get_args();
	$msg = print_r(array_shift($params), TRUE);
	foreach ($params as &$value) {
		$value = print_r($value, TRUE);
	}
	echo "\n";
	if (!CLI) echo '<pre>';
	$msg = trim(print_r($msg, TRUE));
	echo !empty($params) ? vsprintf(date('H:i:s').' -- '.$msg, $params) : $msg;
	if (!CLI) echo '</pre>';
	echo "\n";
}

/**
 * Translate function for templates
 */
function t( $str ) {
	return I18N::_($str);
}