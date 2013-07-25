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
			return $this->after_read(new \Buffer, $attributes);
		}

		$buffer = $childs[0]->read($request);

		// only one child, return as is
		if (count($childs) == 1) {
			return $this->after_read($buffer, $attributes);
		}

		// combine all data for same timestamp
		for ($i=1; $i<count($childs); $i++) {

			$next = $childs[$i]->read($request);

			$buffer->read($row1, $id1, TRUE);
			$next->read($row2, $id2, TRUE);

			$result = new \Buffer;

			while ($row1 != '' OR $row2 != '') {

				if ($id1 == $id2) {

					// same timestamp, combine
					$row1['data']        -= $row2['data'];
					$row1['min']         -= $row2['min'];
					$row1['max']         -= $row2['max'];
					$row1['consumption'] -= $row2['consumption'];
					$result->write($row1, $id1);

					// read both next rows
					$buffer->read($row1, $id1);
					$next->read($row2, $id2);

				} elseif ($id1 AND $id1 < $id2 OR $id2 == '') {

					// missing row 2, save row 1 as is
					$result->write($row1, $id1);

					// read only row 1
					$buffer->read($row1, $id1);

				} else /* $id1 > $id2 */ {

					// missing row 1
					$row2['data']        = -$row2['data'];
					$row2['min']         = -$row2['min'];
					$row2['max']         = -$row2['max'];
					$row2['consumption'] = -$row2['consumption'];
					$result->write($row2, $id2);

					// read only row 2
					$next->read($row2, $id2);

				}
			}
			$next->close();

			// Set result to buffer for next loop
			$buffer = $result;
		}

		return $this->after_read($result, $attributes);
	}

}
