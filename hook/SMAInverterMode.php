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
     * Save mode in uppercase, set empty data to AUS
     *
     */
    public static function data_save_before( &$channel, $config ) {
        $channel->value = ($channel->value != '') ? strtoupper($channel->value) : 'AUS';
    }

}
