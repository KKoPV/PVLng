<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class Daylight extends InternalCalc {

    /**
     * Run additional code before data saved to database
     * Read latitude / longitude from extra attribute
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {
        parent::beforeEdit($channel, $fields);
        list($fields['times']['VALUE'], $fields['extra']['VALUE']) = $channel->extra;
    }

    /**
     *
     * @param $add2tree integer|null
     */
    public static function checkData( Array &$fields, $add2tree ) {
        if ($ok = parent::checkData($fields, $add2tree)) {
            if ($fields['resolution']['VALUE'] == 1 AND $fields['extra']['VALUE'] == '') {
                $fields['resolution']['ERROR'][] = __('model::Daylight_IrradiationIsRequired');
                $fields['extra']['ERROR'][]      = __('model::Daylight_seeAbove');
                $ok = FALSE;
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
        $channel->extra = array(+$fields['times']['VALUE'], $fields['extra']['VALUE']);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $times;

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);

        list($this->times, $this->extra) = $this->extra;

        // Switch data table
        if ($this->resolution == 0) {
            $this->numeric = 0;
            $this->data = new \ORM\ReadingStrMemory;
            $this->data->id = $this->entity;
            // Clean up
            $this->data->deleteById($this->entity);
        }
    }

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        if ($this->numeric AND $this->extra) {
            // Fetch average of last x days of irradiation channel to buid curve
            $channel = \Channel::byGUID($this->extra);

            $q = new \DBQuery('pvlng_reading_num');

            $q->get($q->MAX('data'), 'data')
              ->filter('id', $channel->entity)
              ->filter('timestamp', array(
                    'bt' => array(
                        $this->start - $this->config->get('Model.Daylight.CurveDays')*24*60*60,
                        $this->start-1
                    )
                ))
              ->group('`timestamp` DIV 86400');

            $mean = ($this->config->get('Model.Daylight.Average') == 0)
                  ? /* Select harmonic mean   */ $q->COUNT('data').'/SUM(1/`data`)'
                  : /* Select arithmetic mean */ $q->AVG('data');
            $this->resolution = $this->db->queryOne('SELECT '.$mean.' FROM ('.$q->SQL().') t');
        }

        // Get marker icons to $Icon_sunrise, $Icon_zenit, $Icon_sunset
        extract($this->config->get('Model.Daylight.Icons'), EXTR_PREFIX_ALL, 'Icon');

        $day = $this->start;

        do {
            $sunrise = $this->config->getSunrise($day);
            $sunset  = $this->config->getSunset($day);

            if (!$this->numeric) {
                // Static sunrise / sunset marker with time label depending of "times" attribute
                $this->setMarker($sunrise,             $Icon_sunrise);
                $this->setMarker(($sunrise+$sunset)/2, $Icon_zenit);
                $this->setMarker($sunset,              $Icon_sunset);

            } else {
                // Daylight curve
                $daylight = $sunset - $sunrise;

                if ($sec = $this->TimestampMeterOffset[$this->period[1]]) {
                    // Calculate exact stepping during daylight times
                    $step = $this->period[0] * $sec;
                } else {
                    $step = 60;
                }

                // Set start point
                $this->saveValue($sunrise, 0);
                // Align to step
                $sunrise = floor($sunrise / $step) * $step + $step;
                while ($sunrise < $sunset) {
                    $this->saveValue($sunrise, sin(($sunset-$sunrise) * M_PI / $daylight));
                    $sunrise += $step;
                }
                // Set end point
                $this->saveValue($sunset, 0);
            }

            $day += 24*60*60;
        } while ($day < $this->end);

        // Fake period to read pre-calculated data as they are
        $this->period = array(1, self::NO);
    }

    /**
     *
     */
    protected function setMarker( $time, $icon ) {
        if ($icon) $this->saveValue($time, ($this->times ? date('H:i', $time) : '') . '|' . $icon);
    }



}
