<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Loader {

	/**
	 *
	 */
	public static function register( $settings=array() ) {
		self::$settings = array_merge(self::$settings, array_change_key_case($settings));
		spl_autoload_register(array(__CLASS__, 'load'));
	}

	/**
	 *
	 */
	public static function ignore( $pattern ) {
		self::$IgnorePattern[] = $pattern;
	}

	/**
	 *
	 */
	public static function load( $className ) {
		// Handle namespaced and PEAR named classes the same way
		$className = str_replace(array('\\','_'), DIRECTORY_SEPARATOR, $className);
		$classMap = self::getClassMap();

#		dbg('%s - %s', $className, isset($classMap[$className])?$classMap[$className]:'???');

		isset($classMap[$className]) && include_once $classMap[$className];
	}

	/**
	 *
	 */
	public static function cache( $cache=TRUE ) {
		if ($cache === TRUE) $cache = sys_get_temp_dir();
		// otherwise $cache is a path
		self::$ClassMapFile = $cache
			? sprintf('%s%sclassmap.%s.php', $cache, DS,
								substr(md5(serialize(self::$settings['path'])),0,7))
			: FALSE;
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected static $IgnorePattern = array();

	/**
	 *
	 */
	protected static $ClassMap = array();

	/**
	 *
	 */
	protected static $ClassMapFile;

	/**
	 *
	 */
	protected static $settings = array(
		'path'		=> array(),
		'pattern' => array(
			'%s.class.php',
			'%s.interface.php',
			'class.%s.php',
			'%s.php',
			'%s.inc.php'
		),
	);

	/**
	 *
	 */
	protected static function getClassMap() {
		if (empty(self::$ClassMap)) {
			if (self::$ClassMapFile AND file_exists(self::$ClassMapFile)) {
				self::$ClassMap = include self::$ClassMapFile;
			} else {
				// Build class map
				foreach (self::$settings['path'] as $path) {
					// Iterator for the paths
					$files = new RecursiveIteratorIterator(
						new RecursiveDirectoryIterator($path)
					);
					// Load the list of files in the path
					foreach ($files as $name=>$file ) {
						foreach (self::$IgnorePattern as $pattern) {
							// Skip 2 foreach!
							if (preg_match('~'.preg_quote($pattern, '~').'~', $name)) continue 2;
						}
						if (!$file->isDir() AND !preg_match('~'.DS.'\.\w+~', $name) AND
								preg_match('~\.php$~', $name)) {
							$filename = str_replace($path.DS, '', $file->getPathname());
							foreach (self::$settings['pattern'] as $pattern) {
								$pattern = str_replace('%s', '([\w/]+)', $pattern);
								if (preg_match('~'.$pattern.'~', $filename, $args)) {
									self::$ClassMap[$args[1]] = $name;
									break;
								}
							}
						}
					}
				}
				// Cache class map if allowed
				if (self::$ClassMapFile) {
					ksort(self::$ClassMap);
					file_put_contents(self::$ClassMapFile,
					                  "<?php\nreturn " . var_export(self::$ClassMap, TRUE) . ';');
				}
			}
		}

		return self::$ClassMap;
	}

}
