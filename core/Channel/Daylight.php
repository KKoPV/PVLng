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
        parent::beforeCreate($fields);
        $config = \slimMVC\Config::getInstance();
        $fields['latitude']['VALUE']  = $config->get('Location.Latitude');
        $fields['longitude']['VALUE'] = $config->get('Location.Longitude');
    }

    /**
     * Run additional code before data saved to database
     * Read latitude / longitude from extra attribute
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {
        list(
            $fields['latitude']['VALUE'],
            $fields['longitude']['VALUE'],
            $fields['irradiation']['VALUE']
        ) = $channel->extra;
        parent::beforeEdit($channel, $fields);
    }

    /**
     *
     * @param $add2tree integer|null
     */
    public static function checkData( Array &$fields, $add2tree ) {
        if ($ok = parent::checkData($fields, $add2tree)) {
            $guid = &$fields['irradiation'];
            if ($fields['resolution']['VALUE'] == 1 AND $guid['VALUE'] == '') {
                $fields['irradiation']['ERROR'][] = __('channel::ParamIsRequired');
                $ok = false;
            }
            if ($guid['VALUE']) {
                if (!preg_match('~^([0-9a-z]{4}-){7}[0-9a-z]{4}$~', $guid['VALUE'])) {
                    $guid['ERROR'][] = __('channel::NoValidGUID');
                    $ok = FALSE;
                } else {
                    $channel = new \ORM\Tree;
                    if ($channel->findByGUID($guid['VALUE'])->id == '') {
                        $guid['ERROR'][] = __('channel::NoChannelForGUID');
                        $ok = FALSE;
                    }
                }
            }
        }
        return $ok;
    }

    /**
     * Run additional code before data saved to database
     * Save latitude / longitude to extra attribute
     */
    public static function beforeSave( Array &$fields, \ORM\Channel $channel ) {
        parent::beforeSave($fields, $channel);
        $channel->extra = array(
            +$fields['latitude']['VALUE'],
            +$fields['longitude']['VALUE'],
            $fields['irradiation']['VALUE']
        );
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $latitude;

    /**
     *
     */
    protected $longitude;

    /**
     *
     */
    protected $irradiation;

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);

        list($this->latitude, $this->longitude, $this->irradiation) = $this->extra;

        // Switch data table
        if ($this->resolution == 0) {
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

        if ($this->numeric AND $this->irradiation) {
            // Fetch average of last x days of irradiation channel to buid curve
            $channel = \Channel::byGUID($this->irradiation);
            $q = new \DBQuery('pvlng_reading_num');
            $q->get($q->MAX('data'), 'data')
              ->whereEQ('id', $channel->entity)
              ->whereGE('timestamp', $this->start - 5*24*60*60)
              ->whereLT('timestamp', $this->start);
            $this->resolution = $this->db->queryOne($q);
        }

        $day = $this->start;

        #if ($this->period[1] == self::HOUR) $this->period[0] = 0.5;

        do {
            $sunrise = date_sunrise($day, SUNFUNCS_RET_TIMESTAMP, +$this->latitude, +$this->longitude, 90, date('Z')/3600);
            $sunset  = date_sunset($day, SUNFUNCS_RET_TIMESTAMP, +$this->latitude, +$this->longitude, 90, date('Z')/3600);

            if (!$this->numeric) {

                // Static sunrise / sunset marker
                $this->saveValues(array( $sunrise => 'Sunrise', $sunset => 'Sunset' ));

            } else {

                // Daylight curve
                $daylight = $sunset - $sunrise;

                if ($this->TimestampMeterOffset[$this->period[1]]) {
                    // Calculate exact stepping during daylight times
                    $range = $this->period[0] * $this->TimestampMeterOffset[$this->period[1]];
                    if ($range > $daylight) $range = $daylight;
                    $step = floor($daylight / $range);
                    $step = $daylight / $step;
                } else {
                    $step = 60;
                }

                do {
                    $this->saveValue( $sunrise, sin(($sunset-$sunrise) * M_PI / $daylight) );
                    $sunrise += $step;
                } while ($sunrise <= $sunset+1);
            }

            $day += 24*60*60;
        } while ($day < $this->end);

        // Fake period to read pre-calculated data as they are
        $this->period = array(1, self::NO);
    }
}
