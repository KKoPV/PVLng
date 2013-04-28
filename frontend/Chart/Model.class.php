<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Chart_Model extends Model {

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
	public function getViews() {
		$result = array();
		foreach ($this->db->queryRows($this->db->SQL->Views) as $row) {
			$row->data = json_decode($row->data);
			$result[] = $row;
		}
		return $result;
	}

	/**
	 *
	 */
	public function getView( $name ) {
		$result = $this->db->queryRow($this->db->SQL->View, $name);
		if (isset($result->data)) $result->data = json_decode($result->data);
		return $result;
	}

	/**
	 *
	 */
	public function saveView( $name, $data ) {
		return $this->db->query($this->db->SQL->SaveView, $name, json_encode($data));
	}

	/**
	 *
	 */
	public function deleteView( $name ) {
		return $this->db->query($this->db->SQL->DeleteView, $name);
	}

}
