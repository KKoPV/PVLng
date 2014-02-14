<?php
/**
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
class Average extends \Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = NUMERIC_CHANNEL;

    /**
     * Accept only childs of the same entity type
     */
    public function addChild( $guid ) {
        $childs = $this->getChilds();
        if (empty($childs)) {
            // Add 1st child
            return parent::addChild($guid);
        }

        // Check if the new child have the same type as the 1st (and any other) child
        $first = self::byID($childs[0]['entity']);
        $new     = self::byGUID($guid);
        if ($first->type == $new->type) {
            // ok, add new child
            return parent::addChild($guid);
        }

        throw new Exception('"'.$this->name.'" accepts only childs of type "'.$first->type.'"', 400);
    }

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $childs = $this->getChilds();
        $childCnt = count($childs);

        // no childs, return empty file
        if ($childCnt == 0) {
            return $this->after_read(new \Buffer);
        }

        $buffer = $childs[0]->read($request);

        // only one child, return as is
        if ($childCnt == 1) {
            return $this->after_read($buffer);
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

                    // Same timestamp, combine
                    $row1['data']        = ($row1['data']*$i        + $row2['data'])        / ($i+1);
                    $row1['min']         = ($row1['min']*$i         + $row2['min'])         / ($i+1);
                    $row1['max']         = ($row1['max']*$i         + $row2['max'])         / ($i+1);
                    $row1['consumption'] = ($row1['consumption']*$i + $row2['consumption']) / ($i+1);

                    $result->write($row1, $key1);

                    // read both next rows
                    $row1 = $buffer->next()->current();
                    $row2 = $next->next()->current();

                } elseif (is_null($key2) OR !is_null($key1) AND $key1 < $key2) {

                    // Missing row 2, read only row 1
                    $row1 = $buffer->next()->current();

                } else /* $key1 > $key2 */ {

                    // Missing row 1, read only row 2
                    $row2 = $next->next()->current();

                }
            }
            $next->close();

            // Set result to buffer for next loop
            $buffer = $result;
        }

        return $this->after_read($result);
    }

}
