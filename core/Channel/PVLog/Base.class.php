<?php
/**
 * Base class for generation PV-Log JSON response
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.1-6-gc61bfdd 2013-04-30 20:31:28 +0200 Knut Kohl $
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
	 *
	 */
	public $UTC_Offset = 0;

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
		$date = array_key_exists(0, $request) ? $request[0] : date('Y-m-d');
		$request['start'] = $date;
		$request['end']   = $date . '+1day';
/*
		$d = explode('-', $date);
		$request['start'] = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
		$request['end']   = $date != date('Y-m-d')
		                  ? $request['start'] + 86400
						  : ceil(time() / 360) * 360;
		$request['period'] = '6min';
*/
		// 1st child: total production
		$child = array_shift($childs);
		$fh = $child->read($request, TRUE);
		// get only attributes line
		rewind($fh);
		$row = fgets($fh);
		fclose($fh);

		$row = unserialize($row);
		$inverter->setCurrentTotalWattHours($row['consumption']);

		// 2nd child: Pac power
		$child = array_shift($childs);
		$this->calcTimesAndPowers($child->read($request), $inverter);

		// other childs: must be the Strings Pdc
		if ($this->useStrings) {
			foreach ($childs as $id=>$child) {
			    $string = new \YieldString;
				$this->calcTimesAndPowers($child->read($request), $string);
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
	protected $ts;

	/**
	 *
	 */
	protected function __construct( $guid ) {
		parent::__construct($guid);
		$this->ts = microtime(TRUE);
		$this->UTC_Offset = file_get_contents(__DIR__ . DS . 'utc_offset');
	}

	/**
	 *
	 */
	protected function calcTimesAndPowers( $fh, $obj ) {
		rewind($fh);
		$start = PHP_INT_MAX;
		$end   = 0;
		while ($row = fgets($fh)) {
			$this->decode($row, $id);
			$start = min($start, $row['timestamp']);
			$end   = max($end,   $row['timestamp']);
			$obj->addPowerValue($row['data']);
		}
		$obj->setTimestampStart($start);
		$obj->setTimestampEnd($end);
		fclose($fh);
	}

	/**
	 *
	 */
	protected function finish( &$yield, $request ) {
		$yield->setCreator('PVLng ' . PVLNG_VERSION);

		$date = array_key_exists(0, $request) ? $request[0] : date('Y-m-d');
		$yield->setDeleteDayBeforeImport(($date != date('Y-m-d')));
#		$yield->setDeleteDayBeforeImport(1);

		// Force timestamp calculation
		$yield->asArray();
		$yield->getPlant()->setPowerValues(array());

		// Emulate a UTC summer time
		$yield->setUtcOffset(date('I', $yield->getPlant()->getTimestampStart())
		                   ? $this->UTC_Offset + 3600 : $this->UTC_Offset);

		$result = $yield->asArray();
#		$result['dbg']['QueryTime'] = sprintf('%.0f ms', (microtime(TRUE) - $this->ts) * 1000);
		return $result;
	}

}