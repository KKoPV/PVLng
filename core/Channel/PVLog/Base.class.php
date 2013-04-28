<?php
/**
 * Base class for generation PV-Log JSON response
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace Channel\PVLog;

/**
 * Classes from PVLog
 */
require_once CORE_DIR . DS . 'Yield.php';

/**
 * Base class for generation PV-Log JSON response
 *
 * Fetch data for one inverter and transform many inverter data into correct
 * PV-Log JSON
 */
class Base extends \Channel {

	/**
	 * Skip strings details until PV-Log supports it!
	 */
	public $useStrings = TRUE;

	/**
	 * Fetch data for one inverter
	 *
	 * @param $request array Holds the date to extract
	 * @return array Array(Total, Array of Pac, Array of Pdc)
	 */
	public function fetch( \YieldInverter $inverter, $request ) {

		$childs = $this->getChilds();

		if (count($childs) < 2) {
			throw new \Exception('"'.$this->name.'" needs at least 2 childs, Total and Pac!', 400);
		}

		// transform request date into start - end
		$date = array_key_exists('date', $request) ? $request['date'] : date('Y-m-d');
		$date = array_key_exists(0, $request) ? $request[0] : date('Y-m-d');
		$request['start'] = $date;
		$request['end']   = $date . '+1day';

		// 1st child: total production
		$child = array_shift($childs);
		$fh = $child->read($request, TRUE);
		// get only attributes line
		rewind($fh);
		$row = fgets($fh);
		$row = unserialize($row);
		$inverter->setCurrentTotalWattHours($row['consumption']);
		fclose($fh);

		// 2nd child: Pac power
		$child = array_shift($childs);
		$fh = $child->read($request);
		$this->calcTimesAndPowers($fh, $inverter);
		fclose($fh);

		// other childs: strings Pdc
		if ($this->useStrings) {
			foreach ($childs as $id=>$child) {
			    $string = new \YieldString;
				$fh = $child->read($request);
				$this->calcTimesAndPowers($fh, $string);
				fclose($fh);
				$inverter->addString($string);
			}
		}

		return $inverter;
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected function calcTimesAndPowers ( $fh, $obj ) {
		rewind($fh);
		$start = PHP_INT_MAX;
		$end = 0;
		while ($row = fgets($fh)) {
			$this->decode($row, $ts);
			if ($row['timestamp'] < $start) $start = $row['timestamp'];
			if ($row['timestamp'] > $end)   $end   = $row['timestamp'];
			$obj->addPowerValue($row['data']);
		}
		$obj->setTimestampStart($start);
		$obj->setTimestampEnd($end);
	}

	/**
	 *
	 */
	protected function finish ( &$yield, $request ) {
		$yield->setCreator('PVLng ' . PVLNG_VERSION);

		$yield->setUtcOffset(3600);

		$date = array_key_exists(0, $request) ? $request[0] : date('Y-m-d');
		$yield->setDeleteDayBeforeImport(($date != date('Y-m-d')));

		return $yield->asArray();
	}

}