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
        // times no longer used but needed here to not break existing channels
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
        // times no longer used but needed here to not break existing channels
        $channel->extra = array(+$fields['times']['VALUE'], $fields['extra']['VALUE']);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $settings;

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
        }

        $this->settings = new \ORM\Settings;
    }

    /**
     *
     */
    protected function before_read( &$request ) {

        parent::before_read($request);

        if ($this->dataExists(12*60*60)) return; // Buffer for 12h

        if ($this->numeric AND $this->extra) {
            // Fetch average of last x days of irradiation channel to buid curve
            // Base query, clone afterwards for time ranges filter
            $qBase = new \DBQuery('pvlng_reading_num');
            $qBase->get($qBase->MAX('data'), 'data')
                  ->filter('id', \Channel::byGUID($this->extra)->entity)
                  ->group('`timestamp` DIV 86400');

            $mean = ($this->settings->getModelValue('Daylight', 'Average') == 0)
                  ? /* Select harmonic mean   */ 'COUNT(`data`)/SUM(1/`data`)'
                  : /* Select arithmetic mean */ 'AVG(`data`)';

            $step = ($sec = self::$Grouping[$this->period[1]][0])
                    // Calculate exact stepping during daylight times
                  ? $this->period[0] * $sec
                  : 60;

            $timeback = $this->settings->getModelValue('Daylight', 'CurveDays')*24*60*60;
        }

        $day = $this->start;

        do {
            $sunrise = $this->settings->getSunrise($day);
            $sunset  = $this->settings->getSunset($day);
            $noon    = ($sunrise + $sunset) / 2;

            if (!$this->numeric) {
                // Static sunrise / sunset marker with time label depending of "times" attribute
                $this->saveValue(
                    $sunrise,
                    date('H:i', $sunrise) . '|'
                  . $this->settings->getModelValue('Daylight', 'SunriseIcon')
                );
                $this->saveValue(
                    $noon,
                    date('H:i', $noon) . '|'
                  . $this->settings->getModelValue('Daylight', 'ZenitIcon')
                );
                $this->saveValue(
                    $sunset,
                    date('H:i', $sunset) . '|'
                  . $this->settings->getModelValue('Daylight', 'SunsetIcon')
                );

            } else {

                $q = clone($qBase);
                $q->filter('timestamp', array('bt' => array($day-$timeback, $day-1)));
                // Fetch mean of inner sql
                $resolution = $this->db->queryOne('SELECT '.$mean.' FROM ('.$q->SQL().') t');

                // Daylight curve
                $daylight = $sunset - $sunrise;

                // Set start point
                $this->saveValue($sunrise, 0);
                // Align to step
                $sunrise = floor($sunrise / $step) * $step + $step;
                while ($sunrise < $sunset) {
                    $this->saveValue($sunrise, sin(($sunset-$sunrise) * M_PI / $daylight) * $resolution);
                    $sunrise += $step;
                }
                // Set end point
                $this->saveValue($sunset, 0);
            }

            $day += 24*60*60;

        } while ($day < $this->end);

        $this->dataCreated();

        $this->resolution = 1;
    }
}
