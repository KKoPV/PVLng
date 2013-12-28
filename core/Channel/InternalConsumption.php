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
class InternalConsumption extends \Channel {

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
    public function read( $request, $attributes=FALSE ) {

        $this->before_read($request);

        $childs = $this->getChilds();

        $child1 = $childs[0]->read($request);
        $child2 = $childs[1]->read($request);

        $row2 = $child2->rewind()->current();
        $FirstKey2 = $child2->key();

        $result = new \Buffer;

        $last = 0;

        foreach ($child1 as $key1=>$row1) {

            $key2 = $child2->key();

            if (!$key2) {
                $last = $row1['data'] = $last + $row1['consumption'];
                $result->write($row1, $key1);
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

                $result->write($row1, $key1);

                $row2 = $child2->next()->current();

            } elseif ($key1 < $key2) {

                if ($key2 == $FirstKey2) {
                    // Remember $last ONLY for timestamps before 2nd channel
                    // starts and NOT for data holes
                    $last = $row1['data'];

                    $result->write($row1, $key1);
                }

            } else { // $key1 > $key2

                $last = $row1['data'];

                $result->write($row1, $key1);

                $row2 = $child2->next()->current();
            }
        }
        $child1->close();
        $child2->close();

        return $this->after_read($result, $attributes);
    }

}
