<?php
/**
 * Core class
 *
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2012-2014 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace PVLng;

/**
 *
 */
use Cache\Cache;
use slimMVC\Config;
use slimMVC\MySQLi;
use slimMVC\ORM;
use BabelKitMySQLi;
use BBCode;
use I18N;
use NestedSet;

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
    public static $DEVELOP = false;

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
        self::$RootDir = dirname(dirname(__DIR__));
        self::$TempDir = self::path(self::$RootDir, 'tmp');

        // Composer autoload
        $loader = include self::path(self::$RootDir, 'vendor', 'autoload.php');

        $loader->addPsr4(
            '',
            array(
                self::path(self::$RootDir, 'core'), self::path(self::$RootDir, 'lib')
            )
        );

        if (!empty($dirs)) {
            $loader->addPsr4('', (array) $dirs);
        }

        // Additional classes hardcoded
        $contrib = self::path(self::$RootDir, 'lib', 'contrib');
        $files = array(
            self::path($contrib, 'Array2XML.php'),
            self::path($contrib, 'BabelKit.php'),
            self::path($contrib, 'JavaScriptPacker.php'),
            self::path($contrib, 'PasswordHash.php'),
            self::path($contrib, 'class.nestedset.php'),
            self::path($contrib, 'markdown.php'),
            self::path($contrib, 'nbbc.php'),
            self::path($contrib, 'phpMQTT', 'phpMQTT.php')
        );
        foreach ($files as $file) {
            include_once $file;
        }

        // Nested set for channel tree, without database yet!
        NestedSet::Init(
            array (
                'path'  => self::path($contrib, 'messages'),
                'db_table' => array (
                    'tbl'  => 'pvlng_tree',
                    'nid'  => 'id',
                    'l'    => 'lft',
                    'r'    => 'rgt',
                    'mov'  => 'moved',
                    'pay'  => 'entity'
                )
            )
        );

        // Init all relevant objects with database
        // Must run AFTER NestedSet::Init()
        self::setDatabase();

        // Extend configuration
        $config = self::getConfig();
        foreach (\ORM\SettingsKeys::f()->find() as $setting) {
            $config->set($setting->getKey(), $setting->getValue());
        }

        // I18n
        BabelKitMySQLi::setParams(array('table' => 'pvlng_babelkit'));
        BabelKitMySQLi::setCache(self::getCache());

        try {
            I18N::setBabelKit(BabelKitMySQLi::getInstance());
        } catch (Exception $e) {
            die(
                '<p>Missing translations!</p><p>Did you loaded '
                .'<tt><strong>sql/pvlng.sql</strong></tt> '
                .'into your database?!</p>'
            );
        }

        I18N::setCodeSet('app');
        I18N::setAddMissing(self::getConfig()->get('I18N.Add'));
        I18N::setBBCode(new BBCode);

        // Version
        $version = file(self::path(self::$RootDir, '.version'), FILE_IGNORE_NEW_LINES);
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
        $db = self::getDatabase($reconnect);
        // All these objects need database
        BabelKitMySQLi::setDB($db);
        NestedSet::setDatabase($db);
        ORM::setDatabase($db);
        // Create memory tables if needed
        \ORM\Performance::checkMemoryTable();
        \ORM\ReadingBuffer::checkMemoryTable();
        \ORM\ReadingNumMemory::checkMemoryTable();
        \ORM\ReadingStrMemory::checkMemoryTable();
        \ORM\ReadingScatter::checkMemoryTable();
    }

    /**
     *
     */
    public static function getConfig()
    {
        if (!self::$config) {
            self::$config = new Config;
            self::$config->load(self::path(self::$RootDir, 'config', 'config.default.php'))
                ->load(self::path(self::$RootDir, 'config', 'config.php'))
                ->load('config.php', false);
        }
        return self::$config;
    }

    /**
     *
     */
    public static function getDatabase($reconnect = false)
    {
        if ($reconnect || !self::$db) {
            extract(self::getConfig()->get('Database'), EXTR_REFS);
            self::$db = new MySQLi($host, $username, $password, $database, $port, $socket);
            self::$db->setSettingsTable('pvlng_config');
        }
        return self::$db;
    }

    /**
     *
     */
    public static function getCache()
    {
        if (!self::$cache) {
            self::$cache = Cache::factory(
                array(
                    'Directory' => self::$TempDir,
                    'TTL'       => 86400
                ),
                self::getConfig()->get('Cache', 'MemCache,APC,File')
            );
        }
        return self::$cache;
    }

    /**
     *
     */
    public static function getApiKey()
    {
        return self::getDatabase()->queryOne('SELECT `pvlng_api_key`()');
    }

    /**
     *
     */
    public static function checkApiKey($key)
    {
        return ($key == self::getApiKey());
    }

    /**
     *
     */
    public static function getLoginToken()
    {
        $cfg = self::getConfig();
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
        $db = self::getDatabase();
        $sql = '
            SELECT `value` FROM `pvlng_settings`
             WHERE `scope` = "core"
               AND `name`  = ""
               AND `key`   = "SendStats"
             LIMIT 1
        ';

        // Send statistic at all, once a day
        if (!$db->queryOne($sql) || time() < $db->LastStats + 24*60*60) {
            return;
        }

        // This data will be send
        $args = array(
            // Unique installation id
            $db->queryOne('SELECT `pvlng_id`()'),
            // Real channels, writable and no childs allowed
            (new \ORM\ChannelView)->filterByChilds(0)->filterByWrite(1)->find()->count(),
            // Row count in numeric and non-numeric readings tables
            (new \ORM\ReadingNum)->rowCount() + (new \ORM\ReadingStr)->rowCount()
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
        return implode(DIRECTORY_SEPARATOR, func_get_args());
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
}
