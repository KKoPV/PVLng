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
class Estimate extends \Channel {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		// make sure, only until now :-)
		$this->end = min($this->end, time());

		// Buffer $this->db->TimeStep, at least 60 seconds
		$TimeStep = max(60, $this->db->TimeStep);

		// Search for estimate, 1st for exact day
		$data  = explode("\n", $this->comment);
		$dstart = date('m-d', $this->start);
		$mstart = date('d', $this->start);
		$dend   = date('m-d', $this->end);
		$mend   = date('d', $this->end);
		foreach ($data as $line) {
			$line = explode(':', $line, 2);
			if (trim($line[0]) == $dstart) $estimate[1] = $line[1];
			elseif (trim($line[0]) == $mstart) $estimate[1] = $line[1];
			if (trim($line[0]) == $dend) $estimate[2] = $line[1];
			elseif (trim($line[0]) == $mend) $estimate[2] = $line[1];
		}

		$result = new \Buffer;

		if (!isset($estimate)) return $result; // Return empty result

		// Calc sunrise & sunset


		// Align start to full minutes
		$ts = floor($this->start / $TimeStep) * $TimeStep;

		$result->write(array(
			'datetime'    => date('Y-m-d H:i:s', $ts),
			'timestamp'   => $ts,
			'data'        => +$estimate[1],
			'min'         => +$estimate[1],
			'max'         => +$estimate[1],
			'count'       => 1,
			'timediff'    => 0,
			'consumption' => 0
		), $this->start);

		// Align end to full minutes
		$ts = floor($this->end / $TimeStep) * $TimeStep;

		$result->write(array(
			'datetime'    => date('Y-m-d H:i:s', $ts),
			'timestamp'   => $ts,
			'data'        => +$estimate[2],
			'min'         => +$estimate[2],
			'max'         => +$estimate[2],
			'count'       => 1,
			'timediff'    => $this->end-$this->start,
			'consumption' => 0
		), $this->end);

		return $this->after_read($result, $attributes);
	}

}
