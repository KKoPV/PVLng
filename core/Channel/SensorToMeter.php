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
class SensorToMeter extends \Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = METER_CHANNEL;

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $offset = $this->TimestampMeterOffset[$this->period[1]];

        if ($offset === 0) {
            // No consolidation
            $buffer = $this->getChild(1)->read($request)->rewind();
            $last = FALSE;
        } else {
            // Fetch additional row BEFORE start timestamp
            $request['start'] = $this->start - $offset;
            $buffer = $this->getChild(1)->read($request)->rewind();
            $row = $buffer->current();
            $last = $row['timestamp'];
            // Move data pointer to next (correct start) row
            $buffer->next();
        }

        $result = new \Buffer;

        $consumption = 0;
        while ($row = $buffer->current()) {
            $row['consumption'] = $last ? ($row['timestamp'] - $last) / 3600 * $row['data'] : 0;
            $consumption += $row['consumption'];
            $row['data'] = $consumption;
            $result->write($row, $buffer->key());
            $last = $row['timestamp'];
            $buffer->next();
        }

        return $this->after_read($result);
    }
}
