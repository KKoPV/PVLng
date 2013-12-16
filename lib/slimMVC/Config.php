<?php
/**
 *
 * @author	  Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license	 GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version	 $Id$
 */
namespace slimMVC;

/**
 *
 *
 * @author	  Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license	 GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version	 $Id$
 */
class Config extends \Slim\Helper\Set {

	/**
	 *
	 */
	public static $NamespaceSeparator = '.';

	/**
	 *
	 */
	public static function getInstance() {
		if (!self::$Instance) {
			self::$Instance = new self;
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
			$this->data = $this->array_replace_deep($this->data, $data);
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
			$this->set($namespace, $this->array_replace_deep($this->get($namespace), $data));
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
	protected function array_change_key_case_deep( $data ) {
		if (!is_array($data)) return $data;

		$arr = array();
		foreach ($data as $key=>$value) {
			$arr[mb_strtolower($key)] = $this->array_change_key_case_deep($value);
		}
		return $arr;
	}

	/**
	 *
	 */
	protected function array_replace_deep($base, $replace) {
		// Loop through array key/value pairs
		foreach ($replace as $key=>$value) {
			if (is_array($value)) {
				// Value is an array
				// Traverse the array; replace or add result to original array
				$base[$key] = $this->array_replace_deep(isset($base[$key])?$base[$key]:array(), $value);
			} else {
				// Value is not an array
				// Replace or add current value to original array
				$base[$key] = $value;
			}
		}
		// Return the joined array
		return $base;
	}

	// -------------------------------------------------------------------------
	// PRIVATE
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	private static $Instance = array();

}
