<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
class Loader {

    /**
     *
     */
    public static function autoload( $className ) {
        if (isset(self::$ClassMap[$className])) {
            // Buffered file location found
            self::load(self::$ClassMap[$className]);
            return TRUE;
        } elseif ($file = self::$loader->findFile($className)) {
            // Not yet buffered, remember
            self::$ClassMap[$className] = $file;
            self::load($file);
            return TRUE;
        }
    }

    /**
     *
     */
    public static function register( $loader, $cache=TRUE ) {
        self::$loader = $loader;

        // No real path provided
        if ($cache === TRUE) $cache = sys_get_temp_dir();

        self::$ClassMapFile = $cache
                            ? sprintf('%s%sclassmap.%s.php', $cache, DS,
                              substr(md5(serialize(self::$loader)), -7))
                            : FALSE;

        if (self::$ClassMapFile) {
            // Class map exists?
            if (file_exists(self::$ClassMapFile)) {
                self::$ClassMap = include self::$ClassMapFile;
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
    public static function shutdown() {
        // Cache class map if allowed
        ksort(self::$ClassMap);

        file_put_contents(
            self::$ClassMapFile,
            '<?php return ' . var_export(self::$ClassMap, TRUE) . ';'
        );
    }

    /**
     * Manual file loading with callback
     */
    public static function load( $file ) {
        $file = self::applyCallback($file);
        require_once $file;
        return TRUE;
    }

    /**
     * Manual apply callback
     */
    public static function applyCallback( $file ) {
        foreach (self::$Callback as $Callback) {
            $file = $Callback($file);
        }
        return $file;
    }

    /**
     *
     */
    public static function registerCallback( $Callback ) {
        if (is_callable($Callback)) {
            self::$Callback[] = $Callback;
        } else {
            throw new Exception('Not a callable function provided for Loader::registerCallback()');
        }
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected static $loader;

    /**
     *
     */
    protected static $ClassMapFile;

    /**
     *
     */
    protected static $ClassMap = array();

    /**
     *
     */
    protected static $Callback = array();

}
