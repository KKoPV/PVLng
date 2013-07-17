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
class SensorToMeter extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		$childs = $this->getChilds();

		$child = $childs[0]->read($request);

		$result = \Buffer::create();

		$last = $consumption = $sum = 0;

		\Buffer::rewind($child);

		while (\Buffer::read($child, $row, $id)) {

			if ($last) {
				$consumption = ($row['timestamp'] - $last) / 3600 * $row['data'];
				$sum += $consumption;
			}

			$row['data']        = $sum;
			$row['consumption'] = $consumption * $this->resolution;
			\Buffer::write($result, $row, $id);

			$last = $row['timestamp'];
		}
		\Buffer::close($child);

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
		try {
			$this->resolution = 1 / $this->resolution;
		} catch (\Exception $e) {
		    // Division by zero...
		    $this->resolution = 1;
		}
	}

}
