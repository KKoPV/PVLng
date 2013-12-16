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
class Model {

	/**
	 *
	 */
	public function __construct() {
		$this->app = App::getInstance();
		$this->db = $this->app->db;
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
	protected $app;

	/**
	 *
	 */
	protected $db;

}
