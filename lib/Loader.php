<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
class Loader
{

    /**
     *
     */
    private static $l = array();

    /**
     *
     */
    public static function autoload($class)
    {
        if (isset(static::$classMap[$class])) {
            // Buffered file location found
            $file = static::$classMap[$class];
        } else {
            // Mark to save new class map at the end if a file was found
            if ($file = static::$loader->findFile($class)) {
                static::$classMap[$class] = $file;
                #static::$l[] = '//  ' . $class;
                static::$classMapChanged = true;
            }
        }

        return $file ? self::load($file) : false;
    }

    /**
     *
     */
    public static function register($loader, $cache = true)
    {
        static::$loader = $loader;

        // No real path provided
        if ($cache === true) {
            $cache = sys_get_temp_dir();
        }

        if ($cache) {
            static::$classMapFile = sprintf(
                '%s%sclassmap.%s.php',
                $cache,
                DIRECTORY_SEPARATOR,
                substr(md5(serialize(static::$loader)), -7)
            );
            // Class map exists and is a valid array?
            if (file_exists(static::$classMapFile) &&
                is_array($classMap = @include static::$classMapFile)) {
                static::$classMap = $classMap;
            }
            // Save class map on exit
            register_shutdown_function('Loader::shutdown');
        }

        // Switch autoload to self
        spl_autoload_register('Loader::autoload');
        static::$loader->unregister();
    }

    /**
     * Cache class map
     */
    public static function shutdown()
    {
        if (!static::$classMapChanged) {
            return;
        }

        // Cache class map if allowed
        ksort(static::$classMap);

        file_put_contents(
            static::$classMapFile,
            '<?php return ' . var_export(static::$classMap, true) . ';'
            #. PHP_EOL . PHP_EOL . implode(PHP_EOL, static::$l)
        );
    }

    /**
     * Manual file loading with callback
     */
    public static function load($file)
    {
        return require_once self::applyCallback($file);
    }

    /**
     * Manual apply callback
     */
    public static function applyCallback($file)
    {
        foreach (static::$callbacks as $callback) {
            $file = $callback($file);
        }
        return $file;
    }

    /**
     * Register a loading callback callable
     */
    public static function registerCallback($callback, $position = 0)
    {
        if (!is_callable($callback)) {
            throw new Exception('Not a callable provided for Loader::registerCallback()');
        }

        // If position is occupied move behind
        while (isset(static::$callbacks[$position])) {
            $position++;
        }
        static::$callbacks[$position] = $callback;

        return $position;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $loader;

    /**
     *
     */
    protected static $classMapFile = false;

    /**
     *
     */
    protected static $classMap = array();

    /**
     *
     */
    protected static $classMapChanged = false;

    /**
     *
     */
    protected static $callbacks = array();
}
