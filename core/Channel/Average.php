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
class Average extends Calculator {

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $childs = $this->getChilds();
        $childCnt = count($childs);

        // No childs, return empty file
        if ($childCnt == 0) {
            return $this->after_read(new \Buffer);
        }

        $buffer = $childs[0]->read($request);

        // Only one child, return as is
        if ($childCnt == 1) {
            return $this->after_read($buffer);
        }

        // Combine all data for same timestamp
        for ($i=1, $c=2; $i<$childCnt; $i++, $c++) {

            $next = $childs[$i]->read($request);

            $row1 = $buffer->rewind()->current();
            $row2 = $next->rewind()->current();

            $result = new \Buffer;

            while (!empty($row1) OR !empty($row2)) {

                $key1 = $buffer->key();
                $key2 = $next->key();

                if ($key1 === $key2) {

                    // Same timestamp, combine
                    $row1['data']        = ($row1['data']*$i        + $row2['data'])        / $c;
                    $row1['min']         = ($row1['min']*$i         + $row2['min'])         / $c;
                    $row1['max']         = ($row1['max']*$i         + $row2['max'])         / $c;
                    $row1['consumption'] = ($row1['consumption']*$i + $row2['consumption']) / $c;

                    $result->write($row1, $key1);

                    // Read both next rows
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
