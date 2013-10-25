<?php
/**
 *
 */
require_once dirname(__FILE__) . DS . 'contrib' . DS . 'BabelKit.php';

/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class BabelKitMySQLi extends BabelKit {

	/**
	 *
	 */
	public static function setDB( MySQLi $db ) {
		self::$db = $db;
	}

	/**
	 *
	 */
	public static function setParams( $params ) {
		self::$params = $params;
	}

	/**
	 *
	 */
	public static function setCache( $cache ) {
		self::$cache = $cache;
	}

	/**
	 *
	 */
	public static function getInstance() {
		if (!self::$Instance) {
			self::$Instance = new BabelKitMySQLi(self::$db, self::$params);
		}
		return self::$Instance;
	}

	/**
	 * DON't call parent::__construct() to overcome the "new" database type!
	 *
	 */
	function __construct( $dbh, $param=array()) {

		$this->dbh = $dbh;

		$this->table = isset($param['table']) ? $param['table'] : 'bk_code';

		$this->native = $this->_find_native();
		if (!$this->native)
			throw new Exception("BabelKitMySQLi(): unable to determine native language. "
			                   ."Check table '$this->table' for code_admin/code_admin record.");
	}

	/**
	 * Get a raw code_desc, *not* safe for html display.
	 *
	 * Buffer request results
	 */
	function data($code_set, $code_lang, $code_code) {
		$key = $code_set . '.' . $code_lang . '.' . $code_code;
		while (self::$cache->save($key, $data)) {
			$data = parent::data($code_set, $code_lang, $code_code);
		}
		return $data;
	}

	/**
	 * Implement only MySQLi query
	 */
	function _query($query) {
		$result = array();

		$dbh = $this->dbh;
		$dbq = $dbh->query($query);
		if (is_object($dbq)) {
			while ($row = $dbq->fetch_array(MYSQLI_NUM)) {
				$result[] = $row;
			}
			$dbq->free();
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected static $Instance;

	/**
	 *
	 */
	protected static $db;

	/**
	 *
	 */
	protected static $params;

	/**
	 *
	 */
	protected static $cache;

}
