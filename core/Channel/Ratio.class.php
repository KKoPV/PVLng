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
class Ratio extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {
		$this->before_read($request);

		$childs = $this->getChilds();

		$child1 = $childs[0]->read($request);
		$child1->read($row1, $id1, TRUE);

		$child2 = $childs[1]->read($request);
		$child2->read($row2, $id2, TRUE);

		$result = new \Buffer;

		while ($row1 != '' OR $row2 != '') {

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

				$result->write($row1, $id1);

				// read both next rows
				$child1->read($row1, $id1);
				$child2->read($row2, $id2);

			} elseif ($id1 AND $id1 < $id2 OR $id2 == '') {

				// read only row 1
				$child1->read($row1, $id1);

			} else /* $id1 > $id2 */ {

				// read only row 2
				$child2->read($row2, $id2);

			}
		}
		$child1->close();
		$child2->close();

		return $this->after_read($result, $attributes);
	}

}
