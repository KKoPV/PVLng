<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace slimMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class MySQLi extends \MySQLi {

	/**
	 *
	 */
	const DEFAULT_CONNECTION = 'default';

	/**
	 *
	 */
	public static $DIE_ON_ERROR = FALSE;

	/**
	 *
	 */
	public static $SETTINGS_TABLE = 'settings';

	/**
	 *
	 */
	public static $SETTINGS_KEY_FIELD = 'key';

	/**
	 *
	 */
	public static $SETTINGS_VALUE_FIELD = 'value';

	/**
	 *
	 */
	public static $DEBUG = FALSE;

	/**
	 *
	 */
	public static $QueryCount = 0;

	/**
	 *
	 */
	public static $QueryTime = 0;

	/**
	 *
	 */
	public static $Queries = array();

	/**
	 *
	 */
	public $SQL = FALSE;

	/**
	 * Call this as 1st!
	 */
	public static function setCredentials( $user, $password=NULL, $db=NULL, $host='localhost', $connection=self::DEFAULT_CONNECTION ) {
		self::$credentials[$connection] = array(
			'user'     => $user,
			'password' => $password,
			'database' => $db,
			'host'     => $host
		);
	}

	/**
	 *
	 */
	public static function setUser( $user, $connection=self::DEFAULT_CONNECTION ) {
		self::$credentials[$connection]['user'] = $user;
	}

	/**
	 *
	 */
	public static function getUser( $connection=self::DEFAULT_CONNECTION ) {
		return self::$credentials[$connection]['user'];
	}

	/**
	 *
	 */
	public static function setPassword( $password, $connection=self::DEFAULT_CONNECTION ) {
		self::$credentials[$connection]['password'] = $password;
	}

	/**
	 *
	 */
	public static function getPassword( $connection=self::DEFAULT_CONNECTION ) {
		return self::$credentials[$connection]['password'];
	}

	/**
	 *
	 */
	public static function setDatabase( $database, $connection=self::DEFAULT_CONNECTION ) {
		self::$credentials[$connection]['database'] = $database;
	}

	/**
	 *
	 */
	public static function getDatabase( $connection=self::DEFAULT_CONNECTION ) {
		return self::$credentials[$connection]['database'];
	}

	/**
	 *
	 */
	public static function setHost( $host, $connection=self::DEFAULT_CONNECTION ) {
		self::$credentials[$connection]['host'] = $host;
	}

	/**
	 *
	 */
	public static function getHost( $connection=self::DEFAULT_CONNECTION ) {
		return self::$credentials[$connection]['host'];
	}

	/**
	 *
	 */
	public static function getInstance( $connection=self::DEFAULT_CONNECTION ) {
		if (!isset(self::$Instance[$connection])) {
			self::$Instance[$connection] = new self($connection);
			if (self::$Instance[$connection]->connect_errno) {
			    throw new \Exception (self::$Instance[$connection]->connect_error,
				                      self::$Instance[$connection]->connect_errno);
			}
			self::$Instance[$connection]->bootstrap();
		}
		return self::$Instance[$connection];
	}

	/**
	 *
	 */
	public static function setDebug( $debug=TRUE ) {
		self::$DEBUG = (bool) $debug;
	}

	/**
	 *
	 */
	public function debug( $debug=TRUE ) {
		self::setDebug($debug);
		return $this;
	}

	/**
	 *
	 */
	public function load( $file ) {
		foreach (simplexml_load_file($file) as $key=>$value) {
			$this->SQL->$key = $value;
		}
		return $this;
	}

	/**
	 *
	 */
	public function sql( $query ) {
		$args = func_get_args();
		$query = array_shift($args);

		if ($this->SQL->$query != '') $query = $this->SQL->$query;

		// mask any % before replacing...
		$query = str_replace('%', '%%', trim($query));

		// Replaceplaceholder {1} ... with %1$s ...
		$query = preg_replace('~\{(\d+)\}~', '%$1$s', $query);

		if (isset($args[0])) {
			if (is_array($args[0])) $args = $args[0];
			foreach ($args as &$value) {
				$value = $this->real_escape_string($value);
			}
			$query = vsprintf($query, $args);
		}

		return $query;
	}

	/**
	 *
	 */
	public function query( $query ) {
		$args = func_get_args();
		$query = array_shift($args);

		if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

		$query = $this->sql($query, $args);

		if (self::$DEBUG) {
			echo $this->Cli ? "\n" : '<pre>';
			echo '[' . date('H:i:s'), '] ', $query;
			echo $this->Cli ? "\n" : '</pre>';
		}

		self::$QueryCount++;
		self::$Queries[] = preg_replace('~\s+~', ' ', $query);

		$t = microtime(TRUE);

		$result = parent::query($query);
		$this->error();

		self::$QueryTime += (microtime(TRUE) - $t) * 1000;

		return $result;
	}

	/**
	 *
	 */
	public function queryRows( $query ) {
		$args = func_get_args();
		$query = array_shift($args);

		if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

		$rows = array();
		if ($result = $this->query($query, $args)) {
			/// $t = microtime(TRUE);
			while ($row = $result->fetch_object()) $rows[] = $row;
			/// self::$QueryTime += (microtime(TRUE) - $t) * 1000;
		}
		return $rows;
	}

	/**
	 *
	 */
	public function queryRow( $query ) {
		$args = func_get_args();
		$query = array_shift($args);

		if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

		if ($result = $this->query($query, $args)) {
			/// $t = microtime(TRUE);
			$row = $result->fetch_object();
			/// self::$QueryTime += (microtime(TRUE) - $t) * 1000;
			return $row;
		}
	}

	/**
	 *
	 */
	public function queryOne( $query ) {
		$args = func_get_args();
		$query = array_shift($args);

		if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

		$rc = '';
		if ($result = $this->query($query, $args)) {
			/// $t = microtime(TRUE);
			if (is_object($result)) {
				$a = $result->fetch_row();
				$rc = $a[0];
			} else {
				$rc = $result;
			}
			/// self::$QueryTime += (microtime(TRUE) - $t) * 1000;
		}
		return $rc;
	}

	/**
	 *
	 */
	public function queryCol( $query ) {
		$args = func_get_args();
		$query = array_shift($args);

		if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

		$rows = array();
		if ($result = $this->query($query, $args)) {
			/// $t = microtime(TRUE);
			while ($row = $result->fetch_array()) $rows[] = $row[0];
			/// self::$QueryTime += (microtime(TRUE) - $t) * 1000;
		 }
		return $rows;
	}

	/**
	 *
	 */
	public function __set( $key, $value ) {
		$this->set($key, $value);
	}

	/**
	 *
	 */
	public function set( $key, $value ) {
		$replace = sprintf('REPLACE `%s` (`%s`, `%s`) VALUES (LOWER(\'{1}\'), \'{2}\')',
		                   self::$SETTINGS_TABLE, self::$SETTINGS_KEY_FIELD,
		                   self::$SETTINGS_VALUE_FIELD);

		$key = $this->real_escape_string($key);
		$value = $this->real_escape_string($value);

		$this->query($replace, $key, $value);
	}

	/**
	 *
	 */
	public function __get( $key ) {
		return $this->get($key);
	}

	/**
	 *
	 */
	public function get( $key ) {
		$query = sprintf('SELECT `%s` FROM `%s` WHERE `%s` = LOWER(\'{1}\')',
		                 self::$SETTINGS_VALUE_FIELD, self::$SETTINGS_TABLE,
		                 self::$SETTINGS_KEY_FIELD);

		$key = $this->real_escape_string($key);

		if ($result = $this->query($query, $key) AND
		    $obj = $result->fetch_object()) {
			return $obj->value;
		}
	}

	/**
	 *
	 */
	public function __destruct() {
		#$this->close();
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected static $credentials = array();

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
	private $Cli;

	/**
	 *
	 */
	private function __construct($connection) {
	    $c = self::$credentials[$connection];
		if (!isset($c['host'])) $c['host'] = 'localhost';
		if (!isset($c['database'])) $c['database'] = $c['user'];
		@parent::__construct($c['host'], $c['user'], $c['password'], $c['database']);
	}

	/**
	 *
	 */
	private function bootstrap() {
		$this->SQL = new SQLs;
		$this->Cli = !isset($_SERVER['REQUEST_METHOD']);
		$this->query('SET NAMES "utf8"');
		$this->query('SET CHARACTER SET utf8');
		mysqli_report(MYSQLI_REPORT_STRICT);
	}

	/**
	 *
	 */
	private function error() {
		if (!$this->errno OR !self::$DIE_ON_ERROR) return;

		echo $this->error, PHP_EOL, PHP_EOL;
		exit(1);
	}

	/**
	 * Don't clone a singleton ;-)
	 */
	private function __clone() {}

}

/**
 * Magic class for SQL statements
 */
class SQLs {

	/**
	 *
	 */
	public function __set( $key, $sql ) {
		$this->sql[strtolower($key)] = $sql;
	}

	/**
	 *
	 */
	public function __get( $key ) {
		$key = strtolower($key);
		return isset($this->sql[$key]) ? $this->sql[$key] : '';
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $sql = array();

}
