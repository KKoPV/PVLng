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
class InternalConsumption extends InternalCalc {

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
        // Check if the new child is a meter
        $new = self::byGUID($guid);
        if ($new->meter) {
            // ok, add new child
            return parent::addChild($guid);
        }

        throw new Exception('"'.$this->name.'" accepts only meters as sub channels!', 400);
    }

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        $childs = $this->getChilds();

        // For correct calculations read all child data
        $request['period'] = '1i';

        $child1 = $childs[0]->read($request);
        $row1   = $child1->rewind()->current();

        $child2 = $childs[1]->read($request);
        $row2   = $child2->rewind()->current();
        $FirstKey2 = $child2->key();

        $result = new \Buffer;

        $last = 0;

        while (!empty($row1) OR !empty($row2)) {

            $key1 = $child1->key();
            $key2 = $child2->key();

            if (empty($row2)) {
                $last = $row1['data'] = $last + $row1['consumption'];
                $this->saveValue($row1['timestamp'], $row1['data']);
                $row1 = $child1->next()->current();
                continue;
            }

            if ($key1 == $key2) {

                // same timestamp, combine
                if ($row1['consumption'] > $row2['consumption']) {
                    $row1['consumption'] -= $row2['consumption'];
                    $last = $row1['data'] = $last + $row1['consumption'];
                } else {
                    $row1['data'] = $last;
                    $row1['consumption'] = 0;
                }

                $this->saveValue($row1['timestamp'], $row1['data']);

                $row1 = $child1->next()->current();
                $row2 = $child2->next()->current();

            } elseif (is_null($key2) OR !is_null($key1) AND $key1 < $key2) {

                if ($key2 == $FirstKey2) {
                    // Remember $last ONLY for timestamps before 2nd channel
                    // starts and NOT for data holes
                    $last = $row1['data'];

                    $this->saveValue($row1['timestamp'], $row1['data']);
                }

                $row1 = $child1->next()->current();

            } else /* $key1 > $key2 */ {

                if (!empty($row1)) {
                    $last = $row1['data'];
                    $this->saveValue($row1['timestamp'], $row1['data']);
                }

                $row2 = $child2->next()->current();
            }
        }
        $child1->close();
        $child2->close();
    }

}
