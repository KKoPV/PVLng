<?php
/**
 *
 *
 * @author      Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright	2012 Knut Kohl
 * @license		GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: v1.0.0.2-24-gffc9108 2013-05-05 22:20:01 +0200 Knut Kohl $
 */
namespace yMVC\View;

/**
 *
 */
use yMVC\View;

/**
 * Uses this vars:
 * - content - convert to JSON (required)
 * - jsonp	 - JSON padded (optional)
 */
class JSON extends View {

	/**
	 *
	 */
	public function output() {
		if ($this->filename != '' AND !$this->Plain) {
			Header('Cache-Control: no-cache, must-revalidate');
			Header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
			Header('Content-Disposition: attachment; filename=' . $this->filename);
		}

		Header('Content-Type: application/x-json; charset=UTF-8');

		// With padding?
		if ($jsonp = $this->jsonp) echo $jsonp, '(';

		if ($this->content instanceof \Buffer) {
			$this->content = $this->content->ressource();
		}

		if (is_resource($this->content)) {
			echo '[';
			$first = TRUE;
			rewind($this->content);
			while ($row = fgets($this->content)) {
				$row = unserialize($row);
				if ($first) {
					$first = FALSE;
				} else {
					echo ',';
				}
				// make numeric
				if (is_array($row)) {
				    array_walk($row, function(&$d) {
				        if ((string) $d == (string) +$d) $d = +$d;
                    });
                } else {
                    if ((string) $row == (string) +$row) $row = +$row;
                }
				echo json_encode($row);
			}
			echo ']';
		} elseif ($this->content != '') {
			echo json_encode($this->content);
		}

		// With padding?
		if ($jsonp) echo ')';
	}

}
