<?php
/**
 * An Accumulator sums channels with the same unit to retrieve them as one channel
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
class AccumulatorFull extends \Channel {

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
        $new   = self::byGUID($guid);
        if ($first->type == $new->type) {
            // ok, add new child
            return parent::addChild($guid);
        }

        throw new Exception('"'.$this->name.'" accepts only childs of the same type!', 400);
    }

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $childs = $this->getChilds();
        $childCnt = count($childs);

        switch($childCnt) {

            case 0: // No childs, return empty result
                $result = new \Buffer;
                break;

            case 1: // Only one child, return as is
                $result = $childs[0]->read($request);
                break;

            default:
                $buffer = $childs[0]->read($request);
                $meter  = $childs[0]->meter;

                // Combine all data for same timestamp
                for ($i=1; $i<$childCnt; $i++) {

                    $next = $childs[$i]->read($request);

                    $row1 = $buffer->rewind()->current();
                    $row2 = $next->rewind()->current();

                    $result = new \Buffer;
                    $last1 = $last2 = NULL;

                    while (!empty($row1) OR !empty($row2)) {

                        $key1 = $buffer->key();
                        $key2 = $next->key();

                        if ($key1 === $key2) {

                            if ($meter) {
                                $last1 = $row1;
                                $last2 = $row2;
                            }

                            // same timestamp, combine
                            $row1['data']        += $row2['data'];
                            $row1['min']         += $row2['min'];
                            $row1['max']         += $row2['max'];
                            $row1['consumption'] += $row2['consumption'];

                            $result->write($row1, $key1);

                            // read both next rows
                            $row1 = $buffer->next()->current();
                            $row2 = $next->next()->current();

                        } elseif (is_null($key2) OR !is_null($key1) AND $key1 < $key2) {

                            if ($meter) {
                                $last1 = $row1;
                            }

                            if ($meter AND $last2) {
                                $row1['data']        += $last2['data'];
                                $row1['min']         += $last2['min'];
                                $row1['max']         += $last2['max'];
                                $row1['consumption'] += $last2['consumption'];
                            }
                            $result->write($row1, $key1);

                            // read only row 1
                            $row1 = $buffer->next()->current();

                        } else /* $key1 > $key2 */ {

                            if ($meter) {
                                $last2 = $row2;
                            }

                            if ($meter AND $last1) {
                                $row2['data']        += $last1['data'];
                                $row2['min']         += $last1['min'];
                                $row2['max']         += $last1['max'];
                                $row2['consumption'] += $last1['consumption'];
                            }
                            $result->write($row2, $key2);

                            // read only row 2
                            $row2 = $next->next()->current();

                        }
                    }
                    $next->close();

                    // Set result to buffer for next loop
                    $buffer = $result;
                }
        } // switch

        return $this->after_read($result);
    }
}
