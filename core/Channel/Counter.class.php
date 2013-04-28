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
class Counter extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		$tmpfile = parent::read($request);

		$result = tmpfile();

		$last = 0;

		rewind($tmpfile);
		while ($row = fgets($tmpfile)) {
			$this->decode($row, $id);

			// skip 1st row for plain data
			if ($row['timediff'] OR $last) {

				if (!$row['timediff']) {
					// no period calculations
					// get time difference from row to row
					$row['timediff'] = $row['timestamp'] - $last;
				}

				// remove resolution
				$factor = 3600 / $row['timediff'] / $this->resolution / $this->resolution;

				$row['data']        *= $factor;
				$row['min']         *= $factor;
				$row['max']         *= $factor;
				$row['consumption'] *= $factor;

				fwrite($result, $this->encode($row, $id));
			}

			$last = $row['timestamp'];
		}

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
