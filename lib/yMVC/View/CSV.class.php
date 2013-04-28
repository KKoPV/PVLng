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
 * - content - convert to CSV (required)
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class CSV extends View {

	/**
	 *
	 */
	public function output() {
		if ($this->filename != '' AND !$this->Plain) {
			Header('Cache-Control: no-cache, must-revalidate');
			Header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
			Header('Content-Disposition: attachment; filename=' . $this->filename);
		}

		Header('Content-Type: application/csv; charset=UTF-8');

		if (is_resource($this->content)) {
			rewind($this->content);
			while ($row = fgets($this->content)) {
				$row = unserialize($row);
				$line = '';
				foreach ((array)$row as $value) {
					if (strstr($value, $this->sep)) $value = '"' . $value . '"';
					$line .= $value . $this->sep;
				}
				// remove last separator
				echo substr($line, 0, -1) . PHP_EOL;
			}
		} else {
			foreach ((array)$this->content as $row) {
				$line = '';
				foreach ((array)$row as $value) {
					if (strstr($value, $this->sep)) $value = '"' . $value . '"';
					$line .= $value . $this->sep;
				}
				// remove last separator
				echo substr($line, 0, -1) . PHP_EOL;
			}
		}
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $sep = ';';

}