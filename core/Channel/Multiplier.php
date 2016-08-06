<?php
/**
 * An Accumulator multiplies readings of child1 with child2
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
class Multiplier extends Calculator {

    /**
     * Accept only childs with NOT meter attribute set
     */
    public function addChild( $channel ) {
        $new = new \ORM\Channel($channel);
        if ($new->getType() == 0) {
            // Is an alias, get real channel
            $guid = $new->getChannel();
            $new = new \ORM\Tree;
            $new->filterByGuid($guid)->findOne();
        }

        if ($new->getMeter()) {
            throw new \Exception('"Multiplier" accept only sensor channels as childs!');
        }

        // Add child or throw exception about only 2 childs...
        if (count($this->getChilds(TRUE)) < 2) {
            return \NestedSet::getInstance()->insertChildNode($channel, $this->id);
        }

        Messages::Error(__('AcceptChild', $this->childs, $this->name), 400);
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

                if ($key1 === $key2) {

                    // Same timestamp, combine
                    $row1['data'] = $row1['data'] * $row2['data'];
                    $row1['min']  = $row1['min']  * $row2['min'];
                    $row1['max']  = $row1['max']  * $row2['max'];

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
