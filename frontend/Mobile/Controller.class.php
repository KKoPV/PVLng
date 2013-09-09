<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Mobile_Controller extends Controller {

	/**
	 *
	 */
	public function Index_Action() {
		// Switch layout
		$this->view->setLayout('mobile');

		// Get views
		$q = new \DBQuery('pvlng_view');
		$q->order('name');
		$views = array();
		$view = new \PVLng\Tree;

		foreach ($this->db->queryRows($q) as $row) {
			// Accept only views starting with "@"
			if (strstr($row->name, '@') != $row->name) continue;

			$data = json_decode($row->data);
			unset($data->c, $data->p);

			$new_data = array();
			foreach ($data as $id=>$presentation) {
				// Get entity attributes
				$view->find('id', $id);
				$new_data[] = array(
					'id'           => $view->id,
					'guid'         => $view->guid,
					'unit'         => $view->unit,
					'presentation' => addslashes($presentation)
				);
			}
			$views[] = array(
				'NAME' => $row->name,
				'DATA' => json_encode($new_data)
			);

		}
		$this->view->Views = $views;
	}

}