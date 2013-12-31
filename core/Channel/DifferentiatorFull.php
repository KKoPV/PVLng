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
class DifferentiatorFull extends Differentiator {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = NUMERIC_CHANNEL;

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        $this->before_read($request);

        $childs = $this->getChilds();
        $childCnt = count($childs);

        // no childs, return empty file
        if ($childCnt == 0) {
            return $this->after_read(new \Buffer, $attributes);
        }

        $buffer = $childs[0]->read($request);

        // only one child, return as is
        if ($childCnt == 1) {
            return $this->after_read($buffer, $attributes);
        }

        // combine all data for same timestamp
        for ($i=1; $i<$childCnt; $i++) {

            $next = $childs[$i]->read($request);

            $row1 = $buffer->rewind()->current();
            $row2 = $next->rewind()->current();

            $result = new \Buffer;

            while (!empty($row1) OR !empty($row2)) {

                $key1 = $buffer->key();
                $key2 = $next->key();

                if ($key1 == $key2) {

                    // same timestamp, combine
                    $row1['data']        -= $row2['data'];
                    $row1['min']         -= $row2['min'];
                    $row1['max']         -= $row2['max'];
                    $row1['consumption'] -= $row2['consumption'];

                    $result->write($row1, $key1);

                    // read both next rows
                    $row1 = $buffer->next()->current();
                    $row2 = $next->next()->current();

                } elseif ($key1 AND $key1 < $key2 OR !$key2) {

                    // missing row 2, save row 1 as is
                    $result->write($row1, $key1);

                    // read only row 1
                    $row1 = $buffer->next()->current();

                } else /* $key1 > $key2 */ {

                    // missing row 1
                    $row2['data']        = -$row2['data'];
                    $row2['min']         = -$row2['min'];
                    $row2['max']         = -$row2['max'];
                    $row2['consumption'] = -$row2['consumption'];

                    $result->write($row2, $key2);

                    // read only row 2
                    $row2 = $next->next()->current();

                }
            }
            $next->close();

            // Set result to buffer for next loop
            $buffer = $result;
        }

        return $this->after_read($result, $attributes);
    }

}
