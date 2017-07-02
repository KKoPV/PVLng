<?php
/**
 * Core PVLng class
 *
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2012-2014 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Core;

/**
 *
 */
use Cache\Cache;
use ORM\ChannelView as ORMChannelView;
use ORM\Performance as ORMPerformance;
use ORM\ReadingBuffer as ORMReadingBuffer;
use ORM\ReadingNum as ORMReadingNum;
use ORM\ReadingNumMemory as ORMReadingNumMemory;
use ORM\ReadingScatter as ORMReadingScatter;
use ORM\ReadingStr as ORMReadingStr;
use ORM\ReadingStrMemory as ORMReadingStrMemory;
use ORM\Settings as ORMSettings;
use ORM\SettingsKeys as ORMSettingsKeys;
use slimMVC\Config;

/**
 *
 */
abstract class PVLng
{

    /**
     *
     */
    public static $RootDir;

    /**
     *
     */
    public static $TempDir;

    /**
     *
     */
    public static $DEBUG = false;

    /**
     *
     */
    public static $DEVELOP = 0;

    /**
     *
     */
    const STATS_URL = 'http://stats.pvlng.com/index.php';

    /**
     *
     */
    public static function bootstrap($dirs = null)
    {
        // Common paths
        static::$RootDir = dirname(dirname(__DIR__));
        static::$TempDir = static::pathRoot('tmp');

        // Composer autoload
        $loader = include static::pathRoot('vendor', 'autoload.php');

        $loader->addPsr4('', [static::pathRoot('core'), static::pathRoot('lib')]);

        if (!empty($dirs)) {
            $loader->addPsr4('', (array) $dirs);
        }

        // Additional classes hardcoded
        $contrib = static::pathRoot('lib', 'contrib');
        include_once static::path($contrib, 'Array2XML.php');
        include_once static::path($contrib, 'JavaScriptPacker.php');
        include_once static::path($contrib, 'PasswordHash.php');
        include_once static::path($contrib, 'markdown.php');
        include_once static::path($contrib, 'nbbc.php');
        include_once static::path($contrib, 'phpMQTT', 'phpMQTT.php');

        // Init all relevant objects with database
        static::setDatabase();

        // Extend configuration
        $config = static::getConfig();
        foreach (ORMSettingsKeys::f()->find() as $setting) {
            $config->set($setting->getKey(), $setting->getValue());
        }

        // Version
        $version = file(static::pathRoot('.version'), FILE_IGNORE_NEW_LINES);
        define('PVLNG', 'PhotoVoltaic Logger new generation');
        define('PVLNG_VERSION', $version[0]);
        define('PVLNG_VERSION_DATE', $version[1]);
        define('PVLNG_VERSION_FULL', PVLNG . ' ' . PVLNG_VERSION);

        return $loader;
    }

    /**
     *
     */
    public static function setDatabase($reconnect = false)
    {
        // These objects needs a database
        ORM::setDatabase(static::getDatabase($reconnect));
        // Create memory tables if needed
        include static::pathRoot('core', 'ORM', 'MemoryTables.check.php');
    }

    /**
     *
     */
    public static function getConfig()
    {
        if (!static::$config) {
            static::$config = new Config;
            static::$config->load(static::pathRoot('config', 'config.default.yaml'))
                           ->load(static::pathRoot('config', 'config.yaml'));
        }

        return static::$config;
    }

    /**
     *
     */
    public static function getDatabase($reconnect = false)
    {
        if ($reconnect || !static::$db) {
            extract(static::getConfig()->get('database'), EXTR_REFS);
            static::$db = new MySQLi($host, $username, $password, $database, $port, $socket);
            static::$db->setSettingsTable('pvlng_config');
        }

        return static::$db;
    }

    /**
     *
     */
    public static function getCache()
    {
        if (!static::$cache) {
            static::$cache = Cache::factory(
                ['Directory' => static::$TempDir, 'TTL' => 86400],
                static::getConfig()->get('Cache', 'MemCache,APC,File')
            );
        }

        return static::$cache;
    }

    /**
     *
     */
    public static function getNestedSet()
    {
        if (!static::$NestedSet) {
            static::$NestedSet = new NestedSet(
                static::getDatabase(),
                [
                    'table' => [
                        't'  => 'pvlng_tree',
                        'n'  => 'id',
                        'l'  => 'lft',
                        'r'  => 'rgt',
                        'm'  => 'moved',
                        'p'  => 'entity'
                    ]
                ]
            );
        }

        return static::$NestedSet;
    }

    /**
     *
     */
    public static function getApiKey()
    {
        return static::getDatabase()->queryOne('SELECT `pvlng_api_key`()');
    }

    /**
     *
     */
    public static function checkApiKey($key)
    {
        return ($key == static::getApiKey());
    }

    /**
     *
     */
    public static function getLoginToken()
    {
        $cfg = static::getConfig();
        return sha1(
            __FILE__ . "\x00" . sha1(
                $_SERVER['REMOTE_ADDR'] . "\x00" .
                strtolower($cfg->get('Admin.User')) . "\x00" .
                $cfg->get('Admin.Password')
            )
        );
    }

    /**
     * Send anonymous statistics about channel & readings count
     */
    public static function sendStatistics()
    {
        $db = static::getDatabase();

        // Send statistic at all, once a day
        if (!ORMSettings::getCoreValue('', 'SendStats') || time() < $db->LastStats + 24*60*60) {
            return;
        }

        // This data will be send
        $args = array(
            // Unique installation id
            $db->queryOne('SELECT `pvlng_id`()'),
            // Real channels, writable and no childs allowed
            ORMChannelView::f()->filterByChilds(0)->filterByWrite(1)->find()->count(),
            // Row count in numeric and non-numeric readings tables
            ORMReadingNum::f()->rowCount() + ORMReadingStr::f()->rowCount()
        );

        $ch = curl_init(self::STATS_URL);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        curl_exec($ch);

        // On error, make next try in 1 hour
        $db->LastStats = curl_errno($ch) ? time()-23*60*60 : time();

        curl_close($ch);
    }

    /**
     *
     */
    public static function path()
    {
        $args = func_get_args();
        if (count($args) && is_array($args[0])) {
            $args = $args[0];
        }
        return implode(DIRECTORY_SEPARATOR, $args);
    }

    /**
     *
     */
    public static function pathRoot()
    {
        $args = func_get_args();
        array_unshift($args, static::$RootDir);
        return static::path($args);
    }

    /**
     *
     */
    public static function pathTemp()
    {
        $args = func_get_args();
        array_unshift($args, static::$TempDir);
        return static::path($args);
    }

    /**
     *
     */
    public static function getContent()
    {
        $file = static::path(func_get_args());
        return file_exists($file) ? trim(file_get_contents($file)) : null;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $config;

    /**
     *
     */
    protected static $db;

    /**
     *
     */
    protected static $cache;

    /**
     *
     */
    protected static $NestedSet;
}
