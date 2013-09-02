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
class History extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		if (isset($request['period'])) {
			if (preg_match('~^([.\d]+)(i|min|minutes?)$~', $request['period'], $args)) {
				if ($this->threshold) $args[1] *= $this->threshold;
				$request['period'] = ($args[1]/60).'h';
			} elseif ($this->threshold AND
			          preg_match('~^[.\d]+~', $request['period'], $args)) {
				$request['period'] = str_replace($args[0], $args[0]*$this->threshold, $request['period']);
			}
		} else {
			$request['period'] = '4i';
		}

		$this->before_read($request);

		$this->child = $this->getChild(1);

		if (!$this->child) return $this->after_read(new \Buffer, $attributes);

		// Inherit properties from child
		$this->meter = $this->child->meter;

		if (!$this->valid_to) {
			$result = $this->DayRange(
				$request,
				$this->start + $this->valid_from * 60*60*24,
				$this->end
			);
		} else {
			$result = $this->overall($request);
		}

		// Skip validity handling of after_read!
		$this->valid_from = NULL;
		$this->valid_to = NULL;

		return $this->after_read($result, $attributes);
	}

	// -----------------------------------------------------------------------
	// PROTECTED
	// -----------------------------------------------------------------------

	/**
	 *
	 */
	protected $child;

	/**
	 *
	 */
	protected function DayRange( $request, $start, $end ) {

		$result = new \Buffer;

		$_start = $start;
		$_end = $start + ($this->end - $this->start);

		$i = 1;

		while ($_end <= $end) {
			$request['start'] = $_start;
			$request['end']   = $_end;

			// Skip actual requested period day
			if ($_start != $this->start AND $_end != $this->end) {
				$result = $this->combine($result, $this->child->read($request), $request, $i);
			}

            $_start += $this->end - $this->start;
            $_end   += $this->end - $this->start;
            $i++;
		}

		return $result;
	}

	/**
	 *
	 */
	protected function overall( $request ) {

		$q = new \DBQuery('pvlng_reading_num');
		$q->get($q->YEAR($q->MIN($q->FROM_UNIXTIME('timestamp'))))
		  ->whereEQ('id', $this->child->entity);

		// Start year
		$year = $this->db->QueryOne($q);

		$result = new \Buffer;
		$i = 1;

		while ($year < date('Y')) {
		    // Recalc dates to fetch into year
		    list($m, $d) = explode('|', date('m|d', $this->start));
		    $start = mktime(0, 0, 0, $m, $d, $year);

		    list($m, $d) = explode('|', date('m|d', $this->end));
		    $end = mktime(0, 0, 0, $m, $d, $year);

			$buffer = $this->DayRange(
				$request,
				$start + $this->valid_from * 60*60*24,
				$end + $this->valid_to * 60*60*24
			);

			$request['start'] = $this->start;
			$request['end']   = $this->end;

			$result = $this->combine($result, $buffer, $request, $i);

			$year++;
			$i++;
		}

		return $result;
	}

	/**
	 *
	 */
	protected function combine( \Buffer $buffer, \Buffer $next, $request, $i ) {

		// Check for data to process
		if (!$next->size()) return $buffer;

		$buffer->read($row1, $id1, TRUE);
		$next->read($row2, $id2, TRUE);

		$result = new \Buffer;

		while ($row1 != '' OR $row2 != '') {

			// id1 is in correct format from previous run, build $id2
			$id2 = $row2
			     ? floor(($row2['timestamp'] - $request['start']) / 86400) . '|' .
			       substr($row2['datetime'], -8)
			     : '';

			if ($id1 == $id2) {

				// same timestamp, combine
				$row1['data']        = ($row1['data']*$i        + $row2['data'])        / ($i+1);
				$row1['min']         = ($row1['min']*$i         + $row2['min'])         / ($i+1);
				$row1['max']         = ($row1['max']*$i         + $row2['max'])         / ($i+1);
				$row1['consumption'] = ($row1['consumption']*$i + $row2['consumption']) / ($i+1);

				// Save combined data
				$result->write($row1, $id1);

				// read both next rows
				$buffer->read($row1, $id1);
				$next->read($row2, $id2);

			} elseif ( $id1 AND $id1 < $id2 OR $id2 == '') {

				// missing row 2, save row 1 as is
				$result->write($row1, $id1);

				// read only row 1
				$buffer->read($row1, $id1);

			} else {

				// missing row 1, save row 2 as is
				$result->write($row2, $id2);

				// read only row 2
				$next->read($row2, $id2);

			}
		}
		$buffer->close();
		$next->close();

		$buffer = new \Buffer;

		$result->rewind();

		// Recalc datetime & timestamp from row Ids
		while ($result->read($row, $id)) {

		    list($day_offset, $hms) = explode('|', $id);

		    $row['datetime'] =
				date('Y-m-d ', $this->start + $day_offset * 60*60*24) . $hms;
		    $row['timestamp'] = strtotime($row['datetime']);
			$buffer->write($row, $id);
		}

		return $buffer;
	}

}
