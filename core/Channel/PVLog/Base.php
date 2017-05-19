<?php
/**
 * Base class for generation PV-Log JSON response
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel\PVLog;

/**
 *
 */
use Channel;

/**
 * Classes from PVLog
 */
require_once \PVLng::path(__ROOT__, 'core', 'Yield.php');

/**
 * Base class for generation PV-Log JSON response
 *
 * Fetch data for one inverter and transform many inverter data into correct
 * PV-Log JSON
 */
class Base extends Channel {

  /**
   * Skip strings details until PV-Log supports it!
   */
  public $useStrings = TRUE;

  /**
   *
   */
  public $UTC_Offset = NULL;

  /**
   * r2
   */
  public function GET( $request ) {
    return $this->read($request);
  }

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
    $this->date = !empty($request['p1']) ? $request['p1'] : date('Y-m-d');

    $request['start']  = $this->date;
    $request['end']    = $this->date . '+1day';
    $request['period'] = '5min';

    $consumption = 0;
    // 1st child: total production
    $child = array_shift($childs);
    // Calculate overall consumption
    foreach($child->read($request) as $row) {
        $consumption += $row['consumption'];
    }
    $inverter->setCurrentTotalWattHours($consumption);

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
  protected $date;

  /**
   *
   */
  protected function __construct( $guid ) {
    parent::__construct($guid);
    $this->ts = microtime(TRUE);
    $this->UTC_Offset = file_get_contents(\PVLng::path(__DIR__, 'utc_offset'));
  }

  /**
   *
   */
  protected function calcTimesAndPowers( $buffer, $obj ) {
    $start = PHP_INT_MAX;
    $end   = 0;
    foreach ($buffer as $row) {
      // Round down to next 5 minutes
      $ts    = floor($row['timestamp'] / 300) * 300;
      $start = min($start, $ts);
      $end   = max($end,   $ts);
      $obj->addPowerValue($row['data']);
    }
    $obj->setTimestampStart($start);
    $obj->setTimestampEnd($end);
  }

  /**
   *
   */
  protected function finish( &$yield, $request ) {
    $yield->setCreator('PVLng ' . PVLNG_VERSION);

    $yield->setDeleteDayBeforeImport(isset($request['delete']) && $request['delete'] || $this->date != date('Y-m-d'));

    // Force timestamp calculation
    $yield->asArray();
    $yield->getPlant()->setPowerValues(array());

    // Emulate a UTC summer time
    $dst = date('I', $yield->getPlant()->getTimestampStart()) ? 3600 : 0;
    $yield->setUtcOffset($this->UTC_Offset + $dst);

    $result = $yield->asArray();
    $result['dbg']['query_time'] = sprintf('%.0fms', (microtime(TRUE) - $this->ts) * 1000);
    return $result;
  }

}
