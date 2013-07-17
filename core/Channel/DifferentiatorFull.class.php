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
			return $this->after_read(\Buffer::create(), $attributes);
		}

		$child1 = $childs[0]->read($request);

		// only one child, return as is
		if (count($childs) == 1) {
			return $this->after_read($child1, $attributes);
		}

		// combine all data for same timestamp
		for ($i=1; $i<count($childs); $i++) {

			\Buffer::rewind($child1);
			\Buffer::read($child1, $row1, $id1);

			$child2 = $childs[$i]->read($request);

			\Buffer::rewind($child2);
			\Buffer::read($child2, $row2, $id2);

			$result = \Buffer::create();

			while ($row1 != '' OR $row2 != '') {

				if ($id1 == $id2) {

					// same timestamp, combine
					$row1['data']        -= $row2['data'];
					$row1['min']         -= $row2['min'];
					$row1['max']         -= $row2['max'];
					$row1['consumption'] -= $row2['consumption'];
					\Buffer::write($result, $row1, $id1);

					// read both next rows
					\Buffer::read($child1, $row1, $id1);
					\Buffer::read($child2, $row2, $id2);

				} elseif ($id1 AND $id1 < $id2 OR $id2 == '') {

					// missing row 2, skip
					\Buffer::write($result, $row1, $id1);

					// read only row 1
					\Buffer::read($child1, $row1, $id1);

				} else /* $id1 > $id2 */ {

					// missing row 1, save row 2 as is
					$row2['data']        = -$row2['data'];
					$row2['min']         = -$row2['min'];
					$row2['max']         = -$row2['max'];
					$row2['consumption'] = -$row2['consumption'];
					\Buffer::write($result, $row2, $id2);

					// read only row 2
					\Buffer::read($child2, $row2, $id2);

				}
			}
			\Buffer::close($child2);

			$child1 = $result;
		}

		return $this->after_read($result, $attributes);
	}

}
