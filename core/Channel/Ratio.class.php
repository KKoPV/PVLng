<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace Channel;

/**
 *
 */
class Ratio extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {
		$this->before_read($request);

		$childs = $this->getChilds();

		$tmpfile_1 = $childs[0]->read($request);

		rewind($tmpfile_1);
		$row1 = fgets($tmpfile_1);
		$this->decode($row1, $id1);

		$tmpfile_2 = $childs[1]->read($request);

		rewind($tmpfile_2);
		$row2 = fgets($tmpfile_2);
		$this->decode($row2, $id2);

		$result = tmpfile();

		$done = ($row1 == '' AND $row2 == '');

		while (!$done) {

			if ($id1 == $id2) {

				$row1['data'] = $row2['data'] != 0
				              ? $row1['data'] / $row2['data']
				              : 0;

				$row1['min']  = $row2['min'] != 0
				              ? $row1['min'] / $row2['min']
				              : 0;

				$row1['max']  = $row2['max'] != 0
				              ? $row1['max'] / $row2['max']
				              : 0;

				fwrite($result, $this->encode($row1, $id1));

				// read both next rows
				$row1 = fgets($tmpfile_1);
				$this->decode($row1, $id1);

				$row2 = fgets($tmpfile_2);
				$this->decode($row2, $id2);

			} elseif ($id1 < $id2) {

				// read only row 1
				$row1 = fgets($tmpfile_1);
				$this->decode($row1, $id1);

			} else /* $id1 > $id2 */ {

				// read only row 2
				$row2 = fgets($tmpfile_2);
				$this->decode($row2, $id2);

			}

			$done = ($row1 == '' AND $row2 == '');
		}

		fclose($tmpfile_1);
		fclose($tmpfile_2);

		return $this->after_read($result, $attributes);
	}

}
