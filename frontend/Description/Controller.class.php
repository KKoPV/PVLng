<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Description_Controller extends Controller {

	/**
	 *
	 */
	public function Index_Action() {

		$this->view->SubTitle = I18N::_('Description');

		require_once LIB_DIR . DS . 'markdown.php';

		$md = new Markdown_Parser;
		$content = file_get_contents(ROOT_DIR . DS . 'description.md');

		// Move all headers 2 levels deeper
		$content = preg_replace('~^#+~m', '##$0', $content);

		$this->view->Content = $md->transform($content);
	}

}
