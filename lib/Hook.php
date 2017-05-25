<?php
/**
 * Hook class
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.1.0
 *
 * - 1.1.0
 * Ignore not yet created hook.conf.php
 *
 * - 1.0.0
 * Inital creation
 */
use PVLng\PVLng;

/**
 *
 */
abstract class Hook
{

    /**
     *
     */
    public static function process($hook, &$channel)
    {
        if (!self::$hooks) {
            $file = PVLng::path(PVLng::$RootDir, 'hook', 'hook.conf.php');
            self::$hooks = file_exists($file) ? include $file : array();
        }

        if (!isset(self::$hooks[$hook])) {
            return;
        }

        foreach (self::$hooks[$hook] as $name => $config) {
            if (isset($config[$channel->guid])) {
                require_once PVLng::path(PVLng::$RootDir, 'hook', $name.'.php');
                $class  = '\Hook\\'.$name;
                $method = str_replace('.', '', $hook);
                if (method_exists($class, $method)) {
                    $class::$method($channel, $config[$channel->guid]);
                } else {
                    throw new Exception('Missing method \Hook\\'.$name.'::'.$method.'()');
                }
            }
        }

        return $channel->value;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $hooks;
}
