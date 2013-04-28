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
class SensorToMeter extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		$childs = $this->getChilds();

		$tmpfile = $childs[0]->read($request);

		$result = tmpfile();

		$last = $consumption = $sum = 0;

		rewind($tmpfile);
		while ($row = fgets($tmpfile)) {
			$this->decode($row, $id);

			if ($last) {
				$consumption = ($row['timestamp'] - $last) / 3600 *
				               $row['data'] / $this->resolution;
				$sum += $consumption;
			}

			$row['data']        = $sum;
			$row['consumption'] = $consumption;
			fwrite($result, $this->encode($row, $id));

			$last = $row['timestamp'];
		}

		fclose($tmpfile);

		return $this->after_read($result, $attributes);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected function __construct( $guid ) {
		parent::__construct($guid);
		$this->meter = TRUE;
	}

}
