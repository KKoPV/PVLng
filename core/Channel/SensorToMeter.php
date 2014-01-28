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

        if ($this->period[1] == self::LAST) {
            // Festch ALL data from child channel for correct consumption calculation
            unset($request['period']);
        }

        if ($this->TimestampMeterOffset[$this->period[1]] === 0) {
            // No consolidation
            $buffer = $this->getChild(1)->read($request)->rewind();
            $last = FALSE;
        } else {
            // Fetch add. row before start timestamp
            $request['start'] = $this->start - $this->TimestampMeterOffset[$this->period[1]];
            $buffer = $this->getChild(1)->read($request)->rewind();
            $row = $buffer->current();
            $last = $row['timestamp'];
            // Move data pointer to next row
            $buffer->next();
        }

        $consumption = $id = 0;

        while ($row = $buffer->current()) {

            if ($last) {
                $c = ($row['timestamp'] - $last) / 3600 * $row['data'];
                $row['consumption'] = $c;
                $consumption += $c;
            }

            $row['data'] = $consumption;
            $result->write($row, $id++);

            $last = $row['timestamp'];
            $buffer->next();
        }

        return $this->after_read($result);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     * /
    protected function __construct( $guid ) {
        parent::__construct($guid);

        $this->meter = TRUE;

        if ($this->resolution != 0) {
            $this->resolution = 1 / $this->resolution;
        } else {
            $this->resolution = 1;
        }
    }
*/
}
