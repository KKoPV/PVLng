<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-23-g2e4cde1 2013-05-05 22:15:44 +0200 Knut Kohl $
 */
class Description_Controller extends Controller {

	/**
	 *
	 */
	public function Index_Action() {

		$this->view->SubTitle = I18N::_('Description');

		require_once LIB_DIR . DS . 'markdown.php';

		$md = new Markdown_Parser;
		$file = ROOT_DIR . DS . 'description.md';

		$this->view->Description = $md->transform(file_get_contents($file));
	}

}
