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
class Calculator extends \Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = NUMERIC_CHANNEL;

    /**
     * Run additional code before data saved to database
     * Read latitude / longitude from extra attribute
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {
        $fields['cost']['VISIBLE'] = $channel->meter;
    }

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $child = $this->getChild(1);

        // Get some properties from child
        $this->meter = $child->meter;

        // Simply pass-through
        return $this->after_read($child->read($request));
    }

}
