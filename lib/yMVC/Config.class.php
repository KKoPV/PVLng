<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Config {

	/**
	 *
	 */
	public static $NamespaceSeparator = '.';

	/**
	 *
	 */
	public static function getInstance() {
		if (!self::$Instance) {
			self::$Instance = new Config;
		}
		return self::$Instance;
	}

	/**
	 *
	 */
	public function load( $file, $required=TRUE ) {
		if (isset($file) AND (file_exists($file) OR $required)) {
			$data = include $file;
			$data = $this->array_change_key_case_deep($data);
			$this->data = array_merge($this->data, $data);
		}
		return $this;
	}

	/**
	 *
	 */
	public function loadNamespace( $namespace, $file, $required=TRUE ) {
		if (isset($file) AND (file_exists($file) OR $required)) {
			$data = include $file;
			$data = $this->array_change_key_case_deep($data);
			$this->set($namespace, array_merge((array)$this->get($namespace), $data));
		}
		return $this;
	}

	/**
	 *
	 */
	public function set( $key, $value ) {
		$key = explode(self::$NamespaceSeparator, mb_strtolower($key));
		$current =& $this->data;
		while ($k = array_shift($key)) $current =& $current[$k];
		$current = $value;
		return $this;
	}

	/**
	 *
	 */
	public function __set( $key, $value ) {
		return $this->set(str_replace('_', self::$NamespaceSeparator, $key), $value);
	}

	/**
	 *
	 */
	public function get( $key, $default=NULL ) {
		$key = explode(self::$NamespaceSeparator, mb_strtolower($key));
		$current =& $this->data;
		while ($k = array_shift($key)) {
			if (!isset($current[$k])) return $default;
			$current =& $current[$k];
		}
		return $current;
	}

	/**
	 *
	 */
	public function __get( $key ) {
		return $this->get(str_replace('_', self::$NamespaceSeparator, $key));
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $data = array();

	/**
	 *
	 */
	protected static function array_change_key_case_deep( $data ) {
		if (!is_array($data)) return $data;

		$arr = array();
		foreach ($data as $key=>$value) {
			$arr[mb_strtolower($key)] = self::array_change_key_case_deep($value);
		}
		return $arr;
	}

	// -------------------------------------------------------------------------
	// PRIVATE
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	private static $Instance = array();

	/**
	 *
	 */
	private function __construct() {}

	/**
	 *
	 */
	private function __clone() {}

}