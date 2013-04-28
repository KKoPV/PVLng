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
 * Session handling class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class SessionHandlerMySQLi implements \SessionHandlerI {

	/**
	 *
	 */
	public static function setTable( $table ) {
		self::$table = $table;
	}

	/**
	 *
	 */
	public function __construct( MySQLi $db ) {
		$this->dbg('Start');
		$this->db = $db;
		#$this->db->query($this->CREATETABLE, self::$table);
		// Security hash
		$this->hash = md5(isset($_SERVER['HTTP_USER_AGENT'])
		            ? $_SERVER['HTTP_USER_AGENT']
		            : '');
	}

	/**
	 *
	 */
	public function open( $path, $name ) {
		$this->dbg('Open');
		return TRUE;
	}

	/**
	 *
	 */
	public function close() {
		$this->dbg('Close');
		session_write_close();
		return TRUE;
	}

	/**
	 *
	 */
	public function read( $id ) {
		$this->dbg('Read: '.$id);
		return $this->db->queryOne($this->READ, self::$table, $id, $this->hash);
	}

	/**
	 *
	 */
	public function write( $id, $data ) {
		$this->dbg('Write: '.$id);
		return $this->db->query($this->WRITE, self::$table, $id, $this->hash, $data);
	}

	/**
	 *
	 */
	public function destroy( $id ) {
		$this->dbg('Destroy');
		$this->db->query($this->DESTROY, self::$table, $id);
	}

	/**
	 *
	 */
	public function gc( $max ) {
		$this->dbg('gc');
		return $this->db->query($this->GC, self::$table, $max);
	}

	/**
	 *
	 */
	public function regenerated( $old, $new ) {
		$this->dbg('Regenerated');
		$this->db->query($this->REGENERATE, self::$table, $old, $new);
	}

	/**
	 *
	 */
	public function dbg( $msg ) {
		#$this->msgs[] = $msg;
		#echo $msg . '<br />';
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected static $table = 'yMVC_session';

	/**
	 *
	 */
	protected $hash;

	/**
	 *
	 */
	protected $db;

	// -------------------------------------------------------------------------
	// PRIVATE
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	private $msgs = array();

	/**
	 *
	 */
	private $READ = '
		SELECT `data` FROM `{1}` WHERE `id` = "{2}" AND `hash` = "{3}"
	';

	/**
	 *
	 */
	private $WRITE = '
		REPLACE INTO `{1}` (`id`, `hash`, `data`) VALUES ("{2}", "{3}", "{4}")
	';

	/**
	 *
	 */
	private $REGENERATE = '
		UPDATE `{1}` SET `id` = "{3}" WHERE `id` = "{2}"
	';

	/**
	 *
	 */
	private $DESTROY = '
		DELETE FROM `{1}` WHERE `id` = "{2}"
	';

	/**
	 *
	 */
	private $GC = '
		DELETE FROM `{1}`
		 WHERE `data` = ""
		    OR UNIX_TIMESTAMP(`modified`) < UNIX_TIMESTAMP() - {2}
	';

	/**
	 *
	 */
	private $CREATETABLE = '
		CREATE TABLE IF NOT EXISTS `{1}` (
			`id` char(32) NOT NULL,
			`hash` char(32) NOT NULL,
			`data` varchar(20000) NOT NULL,
			`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`, `hash`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
	';

}