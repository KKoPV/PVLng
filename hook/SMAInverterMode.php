<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Hook;

/**
 *
 */
class SMAInverterMode extends Base {

    /**
     * string => integer
     *
     */
    public static function data_save_before( &$channel, $config ) {

        $value = strtolower($channel->value);

        $channel->value = array_key_exists($value, self::$mapSave)
                        ? self::$mapSave[$value]
                        : -99;

    }

    /**
     * integer => string
     *
     */
    public static function data_read_after( &$channel, $config ) {

        // Deliver strings, so set to non-numeric
        $channel->numeric = 0;

        // Read without checks, index MUST exist
        $channel->value = self::$mapRead[(int) $channel->value];

    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Map inverter mode string to an integer mode
     */
    protected static $mapSave = array(
        ''        => -1,
        'warten'  =>  0,
        'wait'    =>  0,
        'waiting' =>  0,
        'mpp'     =>  1,
    );

    /**
     * Map integer mode back to a string
     */
    protected static $mapRead = array(
        -99 => 'unknown',
         -1 => 'OFF',
          0 => 'WAIT',
          1 => 'MPP',
    );

}
