<?php
/**
 * Delivers the daily production in Wh/kWh
 *
 * URL format:
 *   .../<GUID>.json?m=            for Wh data
 *   .../<GUID>.json?u=kWh&m=      for kWh data
 *
 * kWh format is hightly suggested!
 *
 * http://wiki.sonnenertrag.eu/datenimport:json
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-28-g4d7f5c3 2013-05-10 14:29:24 +0200 Knut Kohl $
 */
namespace Channel\Sonnenertrag;

/**
 *
 */
class JSON extends \Channel {

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
		$new   = self::byGUID($guid);
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

		$this->year  = date('Y');
		$this->month = (array_key_exists('m', $request) AND $request['m'])
		             ? $request['m']
		             : date('n');

		$this->factor = (array_key_exists('u', $request) AND $request['u'] == 'kWh')
		              ? 1000
		              : 1;

		if ($this->month > date('n')) $this->year--;

		$request['start']  = $this->year . '-' . $this->month . '-01';
		$request['end']    = $this->year . '-' . $this->month . '-01+1month';
		$request['period'] = '1day';
		$request['full']   = TRUE;
		$request['format'] = 'json';

		$this->before_read($request);

		$childs = $this->getChilds();

		// no childs, return empty file
		if (count($childs) == 0) {
			return $this->finish();
		}

		$buffer = $childs[0]->read($request);

		// only one child, use as is
		if (count($childs) == 1) {
			$result = $buffer;
		}

		// combine all data for same timestamp
		for ($i=1; $i<count($childs); $i++) {

			$buffer->read($row1, $id1, TRUE);

			$next = $childs[$i]->read($request);
			$next->read($row2, $id2, TRUE);

			$result = new \Buffer;

			while ($row1 != '' OR $row2 != '') {

				if ($id1 == $id2) {

					// same timestamp, combine
					$row1['consumption'] += $row2['consumption'];
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

					// missing row 1, save row 2 as is
					$result->write($row2, $id2);

					// read only row 2
					$next->read($row2, $id2);

				}
			}

			$buffer = $result;
		}

		$result->rewind();

		$data = array();

		while ($result->read($row, $id)) {
			$data[] = round($row['consumption'] / $this->factor, 3);
		}

		return $this->finish($data);
	}

	/**
	 * r2
	 */
	public function GET ( &$request ) {
		$request['format'] = 'json';
		return $this->read($request);
	}

	// -----------------------------------------------------------------------
	// PROTECTED
	// -----------------------------------------------------------------------

	protected function finish( $data=array() ) {
		// Provide full information...
		return array(
			'un'  => $this->factor == 1 ? 'Wh' : 'kWh',
			'tm'  => sprintf('%04d-%02d-01T00:00:00', $this->year, $this->month),
			'dt'  => 86400,
			'val' => $data
		);
	}
}
