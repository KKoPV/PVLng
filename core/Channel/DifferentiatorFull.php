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
class DifferentiatorFull extends Calculator {

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

        $this->meter = $childs[0]->meter;
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
            $last = NULL;

            while (!empty($row1) OR !empty($row2)) {

                $key1 = $buffer->key();
                $key2 = $next->key();

                if ($key1 === $key2) {

                    // Remember original row
                    $last = $row1;

                    // same timestamp, combine
                    $row1['data']        -= $row2['data'];
                    $row1['min']         -= $row2['min'];
                    $row1['max']         -= $row2['max'];
                    $row1['consumption'] -= $row2['consumption'];

                    $result->write($row1, $key1);

                    // read both next rows
                    $row1 = $buffer->next()->current();
                    $row2 = $next->next()->current();

                } elseif (is_null($key2) OR !is_null($key1) AND $key1 < $key2) {

                    // Remember original row
                    $last = $row1;

                    // missing row 2, save row 1 as is
                    $result->write($row1, $key1);

                    // read only row 1
                    $row1 = $buffer->next()->current();

                } else /* $key1 > $key2 */ {

                    // missing row 1
                    if ($this->meter AND $last) {
                        $row2['data']        = $last['data'] - $row2['data'];
                        $row2['min']         = $last['min'] - $row2['min'];
                        $row2['max']         = $last['max'] - $row2['max'];
                        $row2['consumption'] = $last['consumption'] - $row2['consumption'];
                    } else {
                        $row2['data']        = -$row2['data'];
                        $row2['min']         = -$row2['min'];
                        $row2['max']         = -$row2['max'];
                        $row2['consumption'] = -$row2['consumption'];
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

        $this->meter = FALSE;

        return $this->after_read($result);
    }
}
