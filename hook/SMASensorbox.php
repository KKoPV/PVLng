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
class SMASensorbox extends Base {

    /**
     * string => integer
     *
     */
    public static function data_save_before( &$channel, $config ) {
        if ($channel->value == '') throw new Exception('Empty value, ignore', 200);
    }

}
