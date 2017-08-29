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
use ORM\Channel as ORMChannel;
use ORM\ReadingStrMemory as ORMReadingStrMemory;
use ORM\Settings as ORMSettings;
use ORM\Tree as ORMTree;
use DBQuery;
use I18N;

/**
 *
 */
class Daylight extends InternalCalc
{
    /**
     * Run additional code before data saved to database
     * Read latitude / longitude from extra attribute
     */
    public static function beforeEdit(ORMChannel $channel, array &$fields)
    {
        parent::beforeEdit($channel, $fields);
        // times no longer used but needed here to not break existing channels
        list($fields['times']['VALUE'], $fields['extra']['VALUE']) = $channel->extra;
    }

    /**
     *
     * @param $add2tree integer|null
     */
    public static function checkData(array &$fields, $add2tree)
    {
        if ($ok = parent::checkData($fields, $add2tree)) {
            if ($fields['resolution']['VALUE'] == 1 and $fields['extra']['VALUE'] == '') {
                $fields['resolution']['ERROR'][] = I18N::translate('model::Daylight_IrradiationIsRequired');
                $fields['extra']['ERROR'][]      = I18N::translate('model::Daylight_seeAbove');
                $ok = false;
            }
        }
        return $ok;
    }

    /**
     * Run additional code before data saved to database
     * Save latitude / longitude to extra attribute
     */
    public static function beforeSave(array &$fields, ORMChannel $channel)
    {
        parent::beforeSave($fields, $channel);

        // Times no longer used but needed here to not break existing channels
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
    protected function __construct(ORMTree $channel)
    {
        parent::__construct($channel);

        list($this->times, $this->extra) = $this->extra;

        // Switch data table
        if ($this->resolution == 0) {
            $this->numeric = 0;
            $this->data = new ORMReadingStrMemory;
            $this->data->id = $this->entity;
        }

        $this->settings = new ORMSettings;
    }

    /**
     *
     */
    protected function beforeRead(&$request)
    {
        parent::beforeRead($request);

        if ($this->dataExists(60*60)) {
            return; // Buffer 1 hour
        }

        if ($this->numeric and $this->extra) {
            // Fetch average of last x days of irradiation channel to buid curve
            // Base query, clone afterwards for time ranges filter
            $qBase = DBQuery::factory('pvlng_reading_num')
                    ->get($qBase->MAX('data'), 'data')
                    ->filter('id', Channel::byGUID($this->extra)->entity)
                    ->group('`timestamp` DIV 86400');

            $mean = (ORMSettings::getModelValue('Daylight', 'Average') == 0)
                  ? /* Select harmonic mean   */ 'COUNT(`data`)/SUM(1/`data`)'
                  : /* Select arithmetic mean */ 'AVG(`data`)';

            $step = $this->period[0] * self::$secondsPerPeriod[$this->period[1]];

            $timeback = ORMSettings::getModelValue('Daylight', 'CurveDays', 5)*24*60*60;
        }

        // Get icons
        $SunriseIcon = ORMSettings::getModelValue('Daylight', 'SunriseIcon');
        $ZenitIcon   = ORMSettings::getModelValue('Daylight', 'ZenitIcon');
        $SunsetIcon  = ORMSettings::getModelValue('Daylight', 'SunsetIcon');

        $day = $this->start;

        do {
            $sunrise = ORMSettings::getSunrise($day);
            $sunset  = ORMSettings::getSunset($day);
            $noon    = ($sunrise + $sunset) / 2;

            if (!$this->numeric) {
                // Static sunrise / sunset marker with time label depending of "times" attribute
                $this->saveValue($sunrise, date('H:i', $sunrise) . '|' . $SunriseIcon);
                $this->saveValue($noon, date('H:i', $noon) . '|' . $ZenitIcon);
                $this->saveValue($sunset, date('H:i', $sunset) . '|' . $SunsetIcon);
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

            $day += 86400;
        } while ($day < $this->end);

        $this->dataCreated();

        $this->resolution = 1;
    }
}
