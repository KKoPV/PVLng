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

		// Prepare a simple TOC
		$links = $toc = array();

		if (preg_match_all('~^#+ +(.*?) *#*$~m', $content, $headers, PREG_SET_ORDER)) {
			foreach ($headers as $header) {
				$hash = substr(md5($header[0]), 0, 7);
				$links[] = '<a href="#'.$hash.'">'.$header[1].'</a>';
				$toc[$hash] = '<a name="'.$hash.'"></a>';
				// Prepend the hash before the header
				$content = str_replace($header[0], $hash."\n\n".$header[0], $content);
				}
		}

		// Transform MarkDown
		$content = $md->transform($content);

		// Replace inserted hashes aginst the named link tags
		$content = str_replace(array_keys($toc), array_values($toc), $content);

		// Prepend TOC
		$content = '<p>'.implode(' &nbsp; | &nbsp; ', $links).'</p>' . $content;

		$this->view->Content = $content;
	}

}
