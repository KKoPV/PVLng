<?php
/**
 * Hook class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
abstract class Hook {

	/**
	 *
	 */
	public static function process( $hook, &$channel ) {
	    if (!self::$hooks) {
			$hooks = include ROOT_DIR . DS . 'hook' . DS . 'hook.conf.php';
		}

		if (!isset($hooks[$hook])) return;

        foreach ($hooks[$hook] as $name=>$config) {
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