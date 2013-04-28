<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Model extends yMVC\Model\Database {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		$this->db->load(dirname(__FILE__) . DS . 'sql.xml');
	}

	/**
	 *
	 */
	public function getAPIkey() {
		return $this->db->queryOne('APIkey');
	}

	/**
	 *
	 */
	public function resetAPIkey() {
		return $this->db->query('resetAPIkey');
	}


	/**
	 *
	 */
	public function getTree() {
		return $this->db->queryRows($this->db->SQL->Tree);
	}

	/**
	 *
	 */
	public function getTreeById( $id ) {
		return $this->db->queryRow($this->db->SQL->TreeById, $id);
	}

	/**
	 *
	 */
	public function getTreeByGUID( $guid ) {
		return $this->db->queryRow($this->db->SQL->TreeByGUID, $guid);
	}

	/**
	 *
	 */
	public function getEntity( $entity ) {
		return $this->db->queryRow($this->db->SQL->Entity, $entity);
	}

	/**
	 *
	 */
	public function getEntities() {
		return $this->db->queryRows($this->db->SQL->Entities);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

}
