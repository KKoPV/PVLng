<?php
/**
 * Buffering class loader
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
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
        if (isset(self::$classMap[$class])) {
            // Buffered file location found
            $file = self::$classMap[$class];
        } else {
            // Mark to save new class map at the end if a file was found
            if ($file = self::$loader->findFile($class)) {
                self::$classMap[$class] = $file;
                self::$l[] = '//  ' . $class;
                self::$classMapChanged = true;
            }
        }

        return $file ? self::load($file) : false;
    }

    /**
     *
     */
    public static function register($loader, $cache = true)
    {
        self::$loader = $loader;

        // No real path provided
        if ($cache === true) {
            $cache = sys_get_temp_dir();
        }

        self::$classMapFile = $cache
                            ? sprintf(
                                  '%s%sclassmap.%s.php',
                                   $cache, DIRECTORY_SEPARATOR, substr(md5(serialize(self::$loader)), -7)
                              )
                            : false;

        if (self::$classMapFile) {
            // Class map exists and is a valid array?
            if (file_exists(self::$classMapFile) &&
                is_array($classMap = @include self::$classMapFile)) {
                self::$classMap = $classMap;
            }
            // Save class map on exit
            register_shutdown_function('Loader::shutdown');
        }

        // Switch autoload to self
        spl_autoload_register('Loader::autoload');
        self::$loader->unregister();
    }

    /**
     * Cache class map
     */
    public static function shutdown()
    {
        if (!self::$classMapChanged) {
            return;
        }

        // Cache class map if allowed
        ksort(self::$classMap);

        file_put_contents(
            self::$classMapFile,
            '<?php return ' . var_export(self::$classMap, true) . ';'
            . PHP_EOL . PHP_EOL . implode(PHP_EOL, self::$l)
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
        foreach (self::$callbacks as $callback) {
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
        while (isset(self::$callbacks[$position])) {
            $position++;
        }
        self::$callbacks[$position] = $callback;
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
    protected static $classMapFile;

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
