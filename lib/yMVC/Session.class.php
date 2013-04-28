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
abstract class Session {

	/**
	 *
	 */
	protected static $name;

	/**
	 *
	 */
	protected static $table = 'yMVC_session';

	/**
	 *
	 */
	protected static $hash;

	/**
	 *
	 */
	protected static $db;

	/**
	 *
	 */
	public static function setName( $name ) {
		self::$name = $name;
	}

	/**
	 *
	 */
	public static function setTable( $table ) {
		self::$table = $table;
	}

	/**
	 *
	 */
	public static function start( MySQLi $db ) {

		if (session_id()) return;

		self::dbg('Start');

		self::$db = $db;

		self::$db->query(self::$CREATETABLE, self::$table);

		session_set_save_handler(
			array('\yMVC\Session', 'open'),		array('\yMVC\Session', 'close'),
			array('\yMVC\Session', 'read'),		array('\yMVC\Session', 'write'),
			array('\yMVC\Session', 'destroy'), array('\yMVC\Session', 'gc')
		);

		if (self::$name != '') session_name(self::$name);

		session_start();

		// to overcome/fix a bug in IE 6.x
		Header('Cache-control: private');

		// Security hash
		self::$hash = md5($_SERVER['HTTP_USER_AGENT']);
	}

	/**
	 *
	 */
	public static function open() {
		self::dbg('Open');
		return TRUE;
	}

	/**
	 *
	 */
	public static function close() {
		self::dbg('Close');
		session_write_close();
		return TRUE;
	}

	/**
	 *
	 */
	public static function read( $id ) {
		self::dbg('Read: '.$id);
		$data = self::$db->queryOne(self::$READ, self::$table, $id,	self::$hash);
		if ($data != '') $data = unserialize($data);
		return $data;
	}

	/**
	 *
	 */
	public static function write( $id, $data ) {
		self::dbg('Write: '.$id);
		return self::$db->query(self::$WRITE, self::$table, $id, self::$hash, serialize($data));
	}

	/**
	 *
	 */
	public static function regenerate_id() {
		self::dbg('Regenerate');
		$old = session_id();
		session_regenerate_id();
		$new = session_id();
		self::$db->query(self::$REGENERATE, self::$table, $old, $new);
	}

	/**
	 *
	 */
	public static function destroy( $id ) {
		self::dbg('Destroy');
		self::$db->query(self::$DESTROY, self::$table, $id);
	}

	/**
	 *
	 */
	public static function gc( $maxlifetime ) {
		self::dbg('gc');
		return self::$db->query(self::$GC, self::$table, time()-$maxlifetime);
	}

	/**
	 *
	 */
	public static function dbg( $msg ) {
		self::$msgs[] = $msg;
	}

	private static $msgs = array();

	/**
	 *
	 */
	private static $READ = '
		SELECT `data` FROM `{1}` WHERE `id` = "{2}" AND `hash` = "{3}"
	';

	/**
	 *
	 */
	private static $WRITE = '
		REPLACE INTO `{1}` (`id`, `hash`, `data`) VALUES ("{2}", "{3}", "{4}")
	';

	/**
	 *
	 */
	private static $REGENERATE = '
		UPDATE `{1}` SET `id` = "{3}" WHERE `id` = "{2}"
	';

	/**
	 *
	 */
	private static $DESTROY = '
		DELETE FROM `{1}` WHERE `id` = "{2}"
	';

	/**
	 *
	 */
	private static $GC = '
		DELETE FROM `{1}` WHERE `modified` < "{2}"
	';

	/**
	 *
	 */
	private static $CREATETABLE = '
		CREATE TABLE IF NOT EXISTS `{1}` (
			`id` char(32) NOT NULL,
			`hash` char(32) NOT NULL,
			`data` varchar(20000) NOT NULL,
			`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`, `hash`)
		) ENGINE=MEMORY DEFAULT CHARSET=utf8
	';

}