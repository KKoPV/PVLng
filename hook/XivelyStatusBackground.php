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
class XivelyStatusBackground extends Base {

    /**
     *
     */
    public static function data_save_after( &$channel, $config ) {

        // Default format, if still set ignored
        $config[] = '%s';

        $cmd = __DIR__ . DS . 'XivelyUpdate.php '
             . $config[0] . '.csv '
             . $config[1] . ' '
               // Xively CSV: "Channel,Value"
             . $config[2] . ',' . sprintf($config[3], $channel->value)
             . ' >/dev/null 2>&1 &';

        // Fire and forget...
        exec($cmd);
    }

}
