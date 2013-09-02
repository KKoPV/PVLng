<?php
/**
 * Translation class
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
	public static function process( $hook, $channel ) {
	    if (!self::$hooks) {
			$hooks = include ROOT_DIR . DS . 'hook' . DS . 'hook.conf.php';
		}

		if (!isset($hooks[$hook])) return $channel->value;

        foreach ($hooks[$hook] as $name) {
			require_once ROOT_DIR . DS . 'hook' . DS . $name . '.class.php';
			$c = '\Hook\\'.$name;
            $channel->value = $c::getInstance()->$hook($channel);
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