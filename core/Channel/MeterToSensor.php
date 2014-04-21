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
class MeterToSensor extends \Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = SENSOR_CHANNEL;

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        if ($offset = $this->TimestampMeterOffset[$this->period[1]]) {
            // Fetch additional row BEFORE start timestamp
            $request['start'] = $this->start - $offset;
        }

        $buffer = $this->getChild(1)->read($request)->rewind();
        $last = $buffer->current();
        $buffer->next();

        $result = new \Buffer;

        while ($row = $buffer->current()) {
            $fact = 3600 / ($row['timestamp'] - $last['timestamp']);
            // Smooth a bit by calculate average of this and last consumption
            $row['data'] = ($row['consumption'] + $last['consumption']) / 2 * $fact;
            $row['min']  = $row['min'] * $fact;
            $row['max']  = $row['max'] * $fact;
            // Remember row before adjust consumption
            $last = $row;
            $row['consumption'] = 0;
            $result->write($row, $buffer->key());
            $buffer->next();
        }

        return $this->after_read($result);
    }
}
