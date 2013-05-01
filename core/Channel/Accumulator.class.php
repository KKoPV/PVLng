<?php
/**
 * An Accumulator sums channels with the same unit to retrieve them as one channel
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
class Accumulator extends \Channel {

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
			return $this->after_read(tmpfile(), $attributes);
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

			$result = tmpfile();

			$done = ($row1 == '' AND $row2 == '');

			while (!$done) {

				if ($id1 == $id2) {

					// same timestamp, combine
					$row1['data']        += $row2['data'];
					$row1['min']         += $row2['min'];
					$row1['max']         += $row2['max'];
					$row1['consumption'] += $row2['consumption'];
					fwrite($result, $this->encode($row1, $id1));

					// read both next rows
					$row1 = fgets($tmpfile_1);
					$this->decode($row1, $id1);

					$row2 = fgets($tmpfile_2);
					$this->decode($row2, $id2);

				} elseif ($id1 AND $id1 < $id2 OR $id2 == '') {

					// missing row 2, save row 1 as is
					fwrite($result, $this->encode($row1, $id1));

					// read only row 1
					$row1 = fgets($tmpfile_1);
					$this->decode($row1, $id1);

				} else /* $id1 > $id2 */ {

					// missing row 1, save row 2 as is
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
