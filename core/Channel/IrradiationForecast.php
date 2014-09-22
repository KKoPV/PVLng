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
class IrradiationForecast extends Channel {

    /**
     * Allow for the channel data update
     */
    public $AllowUpdate = TRUE;

    /**
     *
     * @param $add2tree integer|null
     */
    public static function checkData( Array &$fields, $add2tree ) {
        if ($ok = parent::checkData($fields, $add2tree)) {
            if ($fields['resolution']['VALUE'] == 1 AND $fields['extra']['VALUE'] == '') {
                $fields['resolution']['ERROR'][] = __('model::IrradiationForecast_IrrRequired');
                $fields['extra']['ERROR'][]      = __('model::IrradiationForecast_seeAbove');
                $ok = FALSE;
            }
        }
        return $ok;
    }

    /**
     * Only possible as child of Wunderground group
     */
    public static function beforeAdd2Tree( $parent ) {
        if ($parent->type_id != 46) {
            \Messages::Error(__('model::IrradiationForecastHelp'));
            return FALSE;
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function before_write( &$request ) {
        parent::before_write($request);
        // Transform sky cover into "clear sky"
        $request['data'] = 100 - $request['data'];
    }

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        if ($this->extra) {
            // Fetch average of last x days of irradiation channel to buid curve
            $channel = \Channel::byGUID($this->extra);

            $q = new \DBQuery('pvlng_reading_num');

            $q->get($q->MAX('data'), 'data')
              ->filter('id', $channel->entity)
              ->filter('timestamp', array(
                    'bt' => array(
                        $this->start - $this->config->get('Model.IrradiationForecast.AverageDays')*24*60*60,
                        $this->start-1
                    )
                ))
              ->group('`timestamp` DIV 86400');

            // Select average of inner select
            $this->resolution = $this->db->queryOne('SELECT '.$q->AVG('data').' FROM ('.$q->SQL().') t');
        }

        $this->resolution /= 100;
    }

    /**
     * Recalc according to daylight
     */
    protected function after_read( \Buffer $buffer ) {

        $result = new \Buffer;

        foreach ($buffer as $key=>$row) {
            $sunrise = $this->config->getSunrise($row['timestamp']);
            $sunset  = $this->config->getSunset($row['timestamp']);

            if ($row['timestamp'] < $sunrise OR $row['timestamp'] > $sunset) {
                $row['data'] = $row['min'] = $row['max'] = $row['count'] = 0;
            } else {
                $row['data'] = sin(($row['timestamp']-$sunrise) * M_PI / ($sunset-$sunrise)) * $row['data'];
            }
            $result->write($row, $key);
        }
        $buffer->close();

        return parent::after_read($result);
    }

}
