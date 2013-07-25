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
class Counter extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		$result = new \Buffer;

		$last = 0;

		$buffer = parent::read($request);
		$buffer->rewind();

		while ($buffer->read($row, $id)) {

			// skip 1st row for plain data
			if ($row['timediff'] OR $last) {

				if (!$row['timediff']) {
					// no period calculations
					// get time difference from row to row
					$row['timediff'] = $row['timestamp'] - $last;
				}

				// remove resolution, will be applied in after_read
				$factor = 3600 / $row['timediff'] / $this->resolution /
				          $this->resolution / $this->resolution;

				$row['data']        *= $factor;
				$row['min']         *= $factor;
				$row['max']         *= $factor;
				$row['consumption'] *= $factor;

				$result->write($row, $id);
			}

			$last = $row['timestamp'];
		}
		$buffer->close();

		return $this->after_read($result, $attributes);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $counter = 1;

}
