<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-14-g2a8e482 2013-05-01 20:44:21 +0200 Knut Kohl $
 */
namespace Channel;

/**
 *
 */
class DifferentiatorFull extends Differentiator {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		$childs = $this->getChilds();

		// no childs, return empty file
		if (count($childs) == 0) {
			return $this->after_read($this->tmpfile(), $attributes);
		}

		$tmpfile_1 = $childs[0]->read($request);

		// only one child, return as is
		if (count($childs) == 1) {
			return $this->after_read($tmpfile_1, $attributes);
		}

		// combine all data for same timestamp
		for ($i=1; $i<count($childs); $i++) {

			rewind($tmpfile_1);
			$row1 = fgets($tmpfile_1);
			$this->decode($row1, $id1);

			$tmpfile_2 = $childs[$i]->read($request);

			rewind($tmpfile_2);
			$row2 = fgets($tmpfile_2);
			$this->decode($row2, $id2);

			$result = $this->tmpfile();

			$done = ($row1 == '' AND $row2 == '');

			while (!$done) {

				if ($id1 == $id2) {

					// same timestamp, combine
					$row1['data']        -= $row2['data'];
					$row1['min']         -= $row2['min'];
					$row1['max']         -= $row2['max'];
					$row1['consumption'] -= $row2['consumption'];
					fwrite($result, $this->encode($row1, $id1));

					// read both next rows
					$row1 = fgets($tmpfile_1);
					$this->decode($row1, $id1);
					$row2 = fgets($tmpfile_2);
					$this->decode($row2, $id2);

				} elseif ($id1 AND $id1 < $id2 OR $id2 == '') {

					// missing row 2, skip
					fwrite($result, $this->encode($row1, $id1));

					// read only row 1
					$row1 = fgets($tmpfile_1);
					$this->decode($row1, $id1);

				} else /* $id1 > $id2 */ {

					// missing row 1, save row 2 as is
					$row2['data']        = -$row2['data'];
					$row2['min']         = -$row2['min'];
					$row2['max']         = -$row2['max'];
					$row2['consumption'] = -$row2['consumption'];
					fwrite($result, $this->encode($row2, $id2));
					// read only row 2
					$row2 = fgets($tmpfile_2);
					$this->decode($row2, $id2);

				}

				$done = ($row1 == '' AND $row2 == '');
			}

			$tmpfile_1 = $result;
		}

		return $this->after_read($result, $attributes);
	}

}
