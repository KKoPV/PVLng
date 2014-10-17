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
class SensorToMeter extends Channel {

    /**
     * Accept only childs without meter attribute set
     */
    public function addChild( $channel ) {
        $childs = $this->getChilds();
        if (empty($childs)) {
            $new  = new \ORM\Channel($channel);
            if ($new->getType() == 0) {
                // Is an alias, get real channel
                $guid = $new->getChannel();
                $new = new \ORM\Tree;
                $new->filterByGuid($guid)->findOne();
            }

            if ($new->getMeter() == 1) {
                throw new \Exception('"SensorToMeter" accept only a non-meter channel as child!');
            }

        }
        // Add child or throw exception about only 1 child...
        return parent::addChild($channel);
    }

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $buffer = $this->getChild(1)->read($request)->rewind();
        $last = 0;

        $result = new \Buffer;

        $consumption = 0;
        while ($row = $buffer->current()) {
            $cons = round($last ? ($row['timestamp'] - $last) / 3600 * $row['data'] : 0, $this->decimals);
            $consumption += $cons;
            $row['data'] = $consumption;
            $row['consumption'] = $cons;
            $result->write($row, $buffer->key());
            $last = $row['timestamp'];
            $buffer->next();
        }

        return $this->after_read($result);
    }
}
