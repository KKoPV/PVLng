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
class Differentiator extends \Channel {

	/**
	 * Accept only childs of the same entity type
	 */
	public function addChild( $guid ) {
		$childs = $this->getChilds();
		if (empty($childs)) {
			// Add 1st child
			return parent::addChild($guid);
		}

		// Check if the new child have the same type as the 1st (and any other) child
		$first = self::byID($childs[0]['entity']);
		$new	 = self::byGUID($guid);
		if ($first->type == $new->type) {
			// ok, add new child
			return parent::addChild($guid);
		}

		throw new Exception('"'.$this->name.'" accepts only childs of type "'.$first->type.'"', 400);
	}

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

					// missing row 2, skip
					// read only row 1
					$buffer->read($row1, $id1);

				} else /* $id1 > $id2 */ {

					// missing row 1, skip
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
