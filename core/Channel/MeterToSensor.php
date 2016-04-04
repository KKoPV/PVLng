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
#class MeterToSensor extends InternalCalc {
class MeterToSensor extends Channel {

    /**
     * Accept only childs with meter attribute set
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

            if ($new->getMeter() == 0) {
                throw new \Exception('"SensorToMeter" accept only a meter channel as child!');
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

        if ($offset = self::$Grouping[$this->period[1]][0]) {
            // Fetch additional row BEFORE start timestamp
            $request['start'] = $this->start - $offset;
        }

        $buffer = $this->getChild(1)->read($request)->rewind();
        $last1 = $buffer->current();
        $buffer->next();
        $last2 = $buffer->current();
        $key = $buffer->key();
        $buffer->next();

        $result = new \Buffer;

        while ($row = $buffer->current()) {
            $fact = 3600 / ($row['timestamp'] - $last2['timestamp']);

            // Smooth a bit by calculate average of this and last consumption
            $row['data'] = ($last1['consumption'] + $last2['consumption'] + $row['consumption']) / 3 * $fact;
            $row['min']  = $row['min'] * $fact;
            $row['max']  = $row['max'] * $fact;

            // Remember row before adjust consumption
            $last1 = $last2;
            $last2 = $row;

            // Set timestamp to last timestamp, which is now in $last1
            $row['timestamp'] = $last1['timestamp'];
            $row['datetime']  = $last1['datetime'];
            $row['consumption'] = 0;
            $result->write($row, $key);

            $key = $buffer->key();
            $buffer->next();
        }

        return $this->after_read($result);
    }
}
