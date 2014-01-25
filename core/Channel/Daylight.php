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
use Channel\InternalCalc;

/**
 *
 */
class Daylight extends InternalCalc {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = SENSOR_CHANNEL;

    /**
     * Run additional code before channel edited by user
     * Read latitude / longitude from extra config
     */
    public static function beforeCreate( Array &$fields ) {
        $config = \slimMVC\Config::getInstance();
        $fields['latitude']['VALUE']  = $config->get('Location.Latitude');
        $fields['longitude']['VALUE'] = $config->get('Location.Longitude');
    }

    /**
     * Run additional code before data saved to database
     * Read latitude / longitude from extra attribute
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {
        list($fields['latitude']['VALUE'], $fields['longitude']['VALUE']) = $channel->extra;
    }

    /**
     * Run additional code before data saved to database
     * Save latitude / longitude to extra attribute
     */
    public static function beforeSave( Array &$fields, \ORM\Channel $channel ) {
        $channel->extra = array(+$fields['latitude']['VALUE'], +$fields['longitude']['VALUE']);
    }

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);

        // Switch data table
        if ($this->resolution == 1) {
            $this->numeric = 0;
            $this->data = new \ORM\ReadingStrMemory;
            $this->data->id = $this->entity;
            /* Clean up */
            $this->data->deleteById($this->entity);
        }
    }

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        $day = $this->start;

        do {
            $sunrise = date_sunrise($day, SUNFUNCS_RET_TIMESTAMP, $this->extra[0], $this->extra[1], 90, date('Z')/3600);
            $sunset  = date_sunset($day, SUNFUNCS_RET_TIMESTAMP, $this->extra[0], $this->extra[1], 90, date('Z')/3600);

            if (!$this->numeric) {

                // Static sunrise / sunset marker
                $this->saveValues(array( $sunrise => 'Sunrise', $sunset => 'Sunset' ));

            } else {

                // Daylight curve
                $daylight = $sunset - $sunrise;

                if ($this->TimestampMeterOffset[$this->period[1]]) {
                    // Calculate exact stepping during daylight times
                    $step = $daylight / floor($daylight / ($this->period[0] * $this->TimestampMeterOffset[$this->period[1]]));
                } else {
                    $step = 60;
                }

                do {
                    $this->saveValue( $sunrise, sin(($sunset-$sunrise) * M_PI / $daylight) );
                    $sunrise += $step;
                } while ($sunrise <= $sunset);
            }

            $day += 24*60*60;
        } while ($day < $this->end);

        // Fake period to read pre-calculated data as they are
        $this->period = array(1, self::NO);
    }
}
