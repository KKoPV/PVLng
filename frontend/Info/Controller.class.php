<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Info_Controller extends ControllerAuth {

	// -------------------------------------------------------------------------
	// PUBLIC
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	public function Index_POST_Action() {
		if ($this->Request('regenerate')) {
			$this->model->resetAPIkey();
			Messages::Info(I18N::_('APIkeyRegenerated'));
		}
	}

	/**
	 *
	 */
	public function Index_Action() {
		$this->view->SubTitle   = I18N::_('Information');
		$this->view->ServerName = $_SERVER['SERVER_NAME'];
		$this->view->APIkey     = $this->model->getAPIkey();

		$rows = $this->model->getReadingCounts();
		$this->view->Stats = $this->rows2view($rows);
		$this->view->readings = 0;
		foreach ($rows as $row) {
			$this->view->readings += $row->readings;
        }
	}

}