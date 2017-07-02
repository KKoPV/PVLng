<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Core;

/**
 *
 */
use Exception;
use Yryie\Yryie;

/**
 *
 */
class Hook
{
    /**
     *
     */
    public static function add($hook, $callback, $position = 10)
    {
        if (!is_callable($callback)) {
            throw new Exception('Not a callable: '.print_r($callback, true));
        }

        if (is_array($hook)) {
            // Apply the same callback to multiple hooks
            array_walk(
                $hook,
                function ($hook) use ($callback, $position) {
                    static::add($hook, $callback, $position);
                }
            );
            return;
        }

        $hook = static::normalizeHook($hook);

        // Prepare hook callback array
        if (!array_key_exists($hook, static::$hooks)) {
            static::$hooks[$hook] = [];
        }

        // Find next free array position
        while (array_key_exists($position, static::$hooks[$hook])) {
            $position++;
        }

        static::$hooks[$hook][$position] = $callback;

        return $position;
    }

    /**
     *
     */
    public static function run($hook, $parameter = null)
    {
        $hook = static::normalizeHook($hook);

        Yryie::add($hook, 'hook');

        if (array_key_exists($hook, static::$hooks)) {
            // Run all callbacks for the hook in correct order
            ksort(static::$hooks[$hook]);

            foreach (static::$hooks[$hook] as $callback) {
                // $parameter as reference!
                call_user_func_array($callback, [&$parameter]);
            }
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $hooks = [];

    /**
     * Simply make hooks lowercase
     */
    protected static function normalizeHook($hook)
    {
        return strtolower($hook);
    }
}
