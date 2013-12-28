<?php
/**
 * Hook class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.1.0
 *
 * - 1.1.0
 * Ignore not yet created hook.conf.php
 *
 * - 1.0.0
 * Inital creation
 */
abstract class Hook {

    /**
     *
     */
    public static function process( $hook, &$channel ) {
        if (!self::$hooks) {
            $file = ROOT_DIR . DS . 'hook' . DS . 'hook.conf.php';
            self::$hooks = file_exists($file) ? include $file : array();
        }

        if (!isset(self::$hooks[$hook])) return;

        foreach (self::$hooks[$hook] as $name=>$config) {
            if (isset($config[$channel->guid])) {
                require_once ROOT_DIR . DS . 'hook' . DS . $name . '.php';
                $class = '\Hook\\'.$name;
                $hook = str_replace('.', '_', $hook);
                $class::$hook($channel, $config[$channel->guid]);
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
