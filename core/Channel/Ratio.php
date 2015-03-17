<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2015 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class Ratio extends Calculator {

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        // Cache data specific for the aggregation period
        $ttl      = $this->period[0] * $this->GroupingPeriod[$this->period[1]];
        $cacheKey = __CLASS__.'-'.$this->id.'-'.$ttl;

        while (self::$cache->save($cacheKey, $data, $ttl)) {

            $childs = $this->getChilds();

            $child1 = $childs[0]->read($request);
            $child2 = $childs[1]->read($request);

            $row1 = $child1->rewind()->current();
            $row2 = $child2->rewind()->current();

            $data = array();

            while (!empty($row1) OR !empty($row2)) {

                $key1 = $child1->key();
                $key2 = $child2->key();

                if ($key1 == $key2) {

                    // Avoid division by zero
                    $row1['data'] = $row2['data'] ? $row1['data'] / $row2['data'] : 0;
                    $row1['min']  = $row2['min']  ? $row1['min']  / $row2['min']  : 0;
                    $row1['max']  = $row2['max']  ? $row1['max']  / $row2['max']  : 0;

                    // Remove consumption, may be we have a meter channel
                    $row1['consumption'] = 0;

                    $data[$key1] = $row1;

                    // read both next rows
                    $row1 = $child1->next()->current();
                    $row2 = $child2->next()->current();

                } elseif (is_null($key2) OR !is_null($key1) AND $key1 < $key2) {

                    // read only row 1
                    $row1 = $child1->next()->current();

                } else /* $key1 > $key2 */ {

                    // read only row 2
                    $row2 = $child2->next()->current();

                }
            }
            $child1->close();
            $child2->close();

        }

        return $this->after_read(new \Buffer($data));
    }

}
