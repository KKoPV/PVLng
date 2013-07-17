<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC\View;

/**
 *
 */
use yMVC\View;

/**
 * Uses this vars:
 * - content - output as text/plain (required)
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class TEXT extends View {

	/**
	 *
	 */
	public function output() {
		if ($this->filename != '' AND !$this->Plain) {
			Header('Cache-Control: no-cache, must-revalidate');
			Header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
			Header('Content-Disposition: attachment; filename=' . $this->filename);
		}

		Header('Content-Type: text/plain; charset=UTF-8');

		if (is_resource($this->content)) {
			rewind($this->content);
			while ($row = fgets($this->content)) {
				echo implode(' ', unserialize($row)), PHP_EOL;
			}
		} else {
			echo implode(PHP_EOL, (array) $this->content);
		}
	}

}