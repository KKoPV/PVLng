<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC\Model;

/**
 *
 */
use yMVC\Model;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Database extends Model {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		$this->db = \yMVC\MySQLi::getInstance();
	}

	/**
	 *
	 */
	public function getDatabaseVersion() {
		return $this->db->QueryOne('SELECT VERSION()');
	}

	/**
	 *
	 */
	public function escape( $str ) {
		return $this->db->real_escape_string($str);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $db;

}