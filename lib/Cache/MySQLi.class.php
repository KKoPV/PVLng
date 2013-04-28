<?php
/**
 *
 */
namespace Cache;

/**
 * Class Cache
 *
 * The following settings are supported:
 * - db    : Instance of \MySQLi (required)
 * - table : table name, default "cache" (optional)
 * - token : used to build unique cache ids (optional)
 *
 * @ingroup     Cache
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class MySQLi extends Base {

	// -------------------------------------------------------------------------
	// PUBLIC
	// -------------------------------------------------------------------------

	/**
	 * @name Implemented abstract functions
	 * @{
	 */
	public function isAvailable() {
		return class_exists('MySQLi');
	}

	/**
	 *
	 */
	public function set( $id, $data, $ttl=0 ) {
		// Cleanout out timed out data
		$this->gc();

		// optimized for probability Set -> Delete -> Flush
		if ($data !== NULL) {
			$sql = sprintf(self::$SET, $this->token, $this->id($id),
										 $this->db->real_escape_string($this->serialize($data)),
										 time(), $ttl);
			return $this->query($sql);
		} elseif ($id !== NULL) { // AND $data === NULL
			return $this->delete($id);
		} else { // $id === NULL AND $data === NULL
			return $this->flush();
		}
	} // function set()

	public function get( $id ) {
		// Cleanout out timed out data
		$this->gc();

		$sql = sprintf(self::$GET, $this->token, $this->id($id));
		$result = $this->query($sql);

		return $result ? $this->unserialize($result->value) : NULL;
	} // function get()

	public function delete( $id ) {
		$this->query(sprintf(self::$DELETE, $this->token, $this->id($id)));
	} // function delete()

	public function flush() {
		$sql = sprintf(self::$FLUSH, $this->token);
		$this->query($sql);
	} // function flush()

	public function gc() {
		if (rand(0, 100) >= $this->gc) return;
		// Remove deleted marked entries
		$this->query(sprintf(self::$GC, $this->token, time()));
	} // function gc()
	/** @} */

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $table;

	/**
	 *
	 */
	protected $db;

	/**
	 *
	 */
	protected $CreateTable = '
		CREATE TABLE IF NOT EXISTS `{TABLE}` (
			`token` varchar(20) NOT NULL,
			`key` varchar(60) NOT NULL,
			`timestamp` bigint(20) NOT NULL,
			`ttl` bigint(20) NOT NULL DEFAULT "7200",
			`is_deleted` tinyint(1) unsigned NOT NULL DEFAULT "0",
			`value` varchar(21757) DEFAULT NULL,
			PRIMARY KEY (`token`, `key`)
		) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT="Data cache in memory"
	';

	/**
	 *
	 */
	protected static $SET = '
		REPLACE `{TABLE}` (`token`, `key`, `value`, `timestamp`, `ttl`, `is_deleted`)
		 VALUES ("%s", LOWER("%s"), "%s", %d, %d, 0)
	';

	/**
	 *
	 */
	protected static $GET = '
		SELECT `value`, `timestamp`, `ttl`
			FROM `{TABLE}`
		 WHERE `token` = "%s" AND `key` = LOWER("%s") AND `is_deleted` = 0
		 LIMIT 1
	';

	/**
	 *
	 */
	protected static $DELETE = '
		UPDATE `{TABLE}`
			 SET `is_deleted` = 1
		 WHERE `token` = "%s"
			 AND `key` = LOWER("%s")
		 LIMIT 1
	';

	/**
	 *
	 */
	protected static $REMOVE = '
		DELETE FROM `{TABLE}`
		 WHERE `token` = "%s"
			 AND `key` = LOWER("%s")
		 LIMIT 1
	';

	/**
	 *
	 */
	protected static $FLUSH = '
		DELETE FROM `{TABLE}`
		 WHERE `token` = "%s"
	';

	/**
	 *
	 */
	protected static $GC = '
		DELETE FROM `{TABLE}`
		 WHERE `token` = "%1$s"
			 AND `is_deleted`
				OR (`ttl` > 0 AND `timestamp`+`ttl` < %2$d)
				OR (`ttl` < 0 AND `ttl` < -%2$d)
	';

	/**
	 * Bootstrap will be called AFTER IsAvailable()
	 *
	 * The following settings are supported:
	 * - @c token : Used to build unique cache ids (general)
	 * - @c packer : Instance of Cache_PackerI (general)
	 *
	 * @throws Cache\Exception
	 * @param array $settings
	 * @return void
	 */
	protected function bootstrap( $settings=array() ) {
		parent::bootstrap($settings);

		$this->db = isset($settings['db']) ? $settings['db'] : NULL;

		if (!isset($this->db) OR !($this->db instanceof \MySQLi))
			throw new Exception('Not valid "db" parameter for '.__CLASS__);

		$this->token = isset($settings['token'])
								 ? $this->db->real_escape_string($settings['token'])
								 : substr($this->token, 0, self::ID_LENGTH);
		$this->table = isset($settings['table']) ? $settings['table'] : 'cache';
		// Make sure in memory table exists
		$this->query($this->CreateTable);
	} // function bootstrap()

	/**
	 * Build internal Id from external Id and the cache token
	 *
	 * @param string $id Unique cache Id
	 * @return string
	 */
	protected function id( $id ) {
		return $this->db->real_escape_string($id);
	} // function id()

	/**
	 * Execute query
	 *
	 * @param string $query
	 * @return mixed
	 */
	protected function query( $query, $single=TRUE ) {
		$query = str_replace('{TABLE}', $this->table, $query);

#		dbg($query);

		$result = $this->db->query($query);

		if ($single AND $result instanceof \MySQLi_Result)
			$result = $result->fetch_object();

#		dbg($result);

		return $result;
	}

}