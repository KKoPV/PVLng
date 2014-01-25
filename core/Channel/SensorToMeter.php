<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class SensorToMeter extends Meter {

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $result = new \Buffer;

        $last = $consumption = $sum = 0;

        if (isset($request['period']) AND $request['period'] == 'last') {
            unset($request['period']);
        }

        foreach ($this->getChild(1)->read($request) as $id=>$row) {

            if ($last) {
                $consumption = ($row['timestamp'] - $last) / 3600 * $row['data'];
                $sum += $consumption;
            }

            $row['data']        = $sum;
            $row['consumption'] = $consumption * $this->resolution;
            $result->write($row, $id);

            $last = $row['timestamp'];
        }

        return $this->after_read($result);
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

        if ($this->resolution != 0) {
            $this->resolution = 1 / $this->resolution;
        } else {
            $this->resolution = 1;
        }
    }

}
