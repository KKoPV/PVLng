<?php
/**
 * Main program file
 *
 * @author			Knut Kohl <github@knutkohl.de>
 * @copyright	 2012-2013 Knut Kohl
 * @license		 GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version		 $Id$
 */

ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
error_reporting(0);

/**
 * Directories
 */
define('DS', DIRECTORY_SEPARATOR);

define('BASE_DIR', dirname(__FILE__));
define('ROOT_DIR', realpath('..'));

define('CORE_DIR', ROOT_DIR . DS . 'core');
define('APP_DIR',  ROOT_DIR . DS . 'frontend');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');

// Outside document root!
define('TEMP_DIR', realpath(ROOT_DIR . DS . 'tmp'));

define('CLI', isset($_SERVER['REQUEST_URI']));

/**
 * Initialize Loader
 */
include LIB_DIR . DS . 'Loader.class.php';

Loader::register(array(
	'path'    => array(LIB_DIR, CORE_DIR, APP_DIR),
	'pattern' => array(
		'%s.class.php',
		'(BabelKit).php',
		'(PasswordHash).php',
	)
));
Loader::cache(TEMP_DIR);

/**
 * Initialize application
 */
include APP_DIR . DS . 'bootstrap.php';

/**
 * Run application
 */
yMVC\App::run();
