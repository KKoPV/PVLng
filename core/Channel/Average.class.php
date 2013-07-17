<?php
/**
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
class Average extends \Channel {

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

			$buffer = \Buffer::create();

			while ($row1 != '' OR $row2 != '') {

				if ($id1 == $id2) {

					// same timestamp, combine
					$row1['data']        .= ';' . $row2['data'];
					$row1['min']         .= ';' . $row2['min'];
					$row1['max']         .= ';' . $row2['max'];
					$row1['consumption'] .= ';' . $row2['consumption'];
					\Buffer::write($buffer, $row1, $id1);

					// read both next rows
					\Buffer::read($child1, $row1, $id1);
					\Buffer::read($child2, $row2, $id2);

				} elseif ($id1 AND $id1 < $id2 OR $id2 == '') {

					// missing row 2, save row 1 as is
					\Buffer::write($buffer, $row1, $id1);

					// read only row 1
					\Buffer::read($child1, $row1, $id1);

				} else /* $id1 > $id2 */ {

					// missing row 1, save row 2 as is
					\Buffer::write($buffer, $row2, $id2);

					// read only row 2
					\Buffer::read($child2, $row2, $id2);

				}
			}
			\Buffer::close($child2);

			$child1 = $buffer;
		}

		$result = \Buffer::create();

		rewind($buffer);
		while (\Buffer::read($buffer, $row, $id)) {
			$data = explode(';', $row['data']);
			$row['data'] = array_sum($data) / count($data);

			$data = explode(';', $row['min']);
			$row['min'] = array_sum($data) / count($data);

			$data = explode(';', $row['max']);
			$row['max'] = array_sum($data) / count($data);

			$data = explode(';', $row['consumption']);
			$row['consumption'] = array_sum($data) / count($data);

			\Buffer::write($result, $row, $id);
		}
		\Buffer::close($buffer);

		return $this->after_read($result, $attributes);
	}

}
