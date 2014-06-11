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
class Accumulator extends Calculator {

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
                $this->meter = $childs[0]->meter;
                $result = $childs[0]->read($request);
                break;

            default:
                $this->meter = $childs[0]->meter;
                $lazy = !$this->extra;
                $buffer = $childs[0]->read($request);

                // Combine all data for same timestamp
                for ($i=1; $i<$childCnt; $i++) {

                    $next = $childs[$i]->read($request);

                    $row1 = $buffer->rewind()->current();
                    $row2 = $next->rewind()->current();
                    $first1 = $first2 = TRUE;

                    $result = new \Buffer;

                    while (!empty($row1) OR !empty($row2)) {

                        $key1 = $buffer->key();
                        $key2 = $next->key();

                        if ($key1 === $key2) {

                            // same timestamp, combine
                            $row1['data']        += $row2['data'];
                            $row1['min']         += $row2['min'];
                            $row1['max']         += $row2['max'];
                            $row1['consumption'] += $row2['consumption'];

                            $result->write($row1, $key1);

                            // read both next rows
                            $row1 = $buffer->next()->current();
                            $row2 = $next->next()->current();
                            $first1 = $first2 = FALSE;

                        } elseif (is_null($key2) OR !is_null($key1) AND $key1 < $key2) {

                            // write $row1 only, if not in strict mode and data set 2 is not yet started
                            if ($first2 AND $lazy) $result->write($row1, $key1);

                            // read only row 1
                            $row1 = $buffer->next()->current();
                            $first1 = FALSE;

                        } else /* $key1 > $key2 */ {

                            // write $row2 only, if not in stric mode and data set 1 is not yet started
                            if ($first1 AND $lazy) $result->write($row2, $key2);

                            // read only row 2
                            $row2 = $next->next()->current();
                            $first2 = FALSE;

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
