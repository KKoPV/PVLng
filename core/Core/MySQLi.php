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
namespace Core;

/**
 *
 */
use mysqli_driver;

/**
 *
 */
class MySQLi extends \MySQLi
{
    /**
     *
     */
    public static $charset = 'utf8';

    /**
     *
     */
    public $queries = [];

    /**
     *
     */
    public function __construct()
    {
        $args = func_get_args();

        // Adopt possible defaults from php.ini
        $host     = array_key_exists(0, $args) ? $args[0] : ini_get('mysqli.default_host');
        $username = array_key_exists(1, $args) ? $args[1] : ini_get('mysqli.default_user');
        $passwd   = array_key_exists(2, $args) ? $args[2] : ini_get('mysqli.default_pw');
        $dbname   = array_key_exists(3, $args) ? $args[3] : '';
        $port     = array_key_exists(4, $args) ? $args[4] : ini_get('mysqli.default_port');
        $socket   = array_key_exists(5, $args) ? $args[5] : ini_get('mysqli.default_socket');

        // Init connection
        parent::init();

        // Better safe than sorry
        parent::options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 1');
        // Avoid SQL error (1690): BIGINT UNSIGNED value is out of range
        parent::options(MYSQLI_INIT_COMMAND, 'SET SESSION sql_mode = CONCAT(@@sql_mode, ",NO_UNSIGNED_SUBTRACTION")');
        // Character set
        parent::options(MYSQLI_INIT_COMMAND, 'SET CHARACTER SET '.static::$charset);

        mysqli_report(MYSQLI_REPORT_STRICT);

        // Connect ...
        if (@parent::real_connect($host, $username, $passwd, $dbname, $port, $socket, MYSQLI_CLIENT_COMPRESS)) {
            // http://php.net/manual/en/mysqli.set-charset.php
            //     This is the preferred way to change the charset.
            //     Using mysqli_query() to set it (such as SET NAMES utf8) is not
            //     recommended.
            $this->set_charset(static::$charset);
        }
    }

    /**
     *
     * /
    public function getDatabase()
    {
        return $this->queryOne('SELECT DATABASE()');
    }

    /**
     *
     */
    public function setDieOnError($die = true)
    {
        $this->dieOnError = !!$die;
        return $this;
    }

    /**
     *
     */
    public function setSettingsTable($table)
    {
        $this->Settings[0] = $table;
        return $this;
    }

    /**
     *
     */
    public function setSettingsKey($key)
    {
        $this->Settings[1] = $key;
        return $this;
    }

    /**
     *
     */
    public function setSettingsValue($value)
    {
        $this->Settings[2] = $value;
        return $this;
    }

    /**
     *
     */
    public function getQueryCount()
    {
        return $this->QueryCount;
    }

    /**
     *
     */
    public function getQueryTime()
    {
        return $this->QueryTime;
    }

    /**
     *
     */
    public function setBuffered($buffered = true)
    {
        $this->Buffered = !!$buffered;
        return $this;
    }

    /**
     *
     */
    public function debug($debug = true)
    {
        $this->debug = !!$debug;
        return $this;
    }

    /**
     *
     */
    public function sql($query)
    {
        $args  = func_get_args();
        $query = array_shift($args);

        // Mask any % before replacing...
        $query = str_replace('%', '%%', trim($query));

        // Replace placeholder {1} ... with %1$s ...
        $query = preg_replace('~\{(\d+)\}~', '%$1$s', $query);

        if (count($args)) {
            if (is_array($args[0])) {
                $args = $args[0];
            }
            $args = array_map([$this, 'real_escape_string'], $args);
            $query = vsprintf($query, $args);
        }

        return $query;
    }

    /**
     *
     */
    public function query($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $query = $this->sql($query, $args);

        if ($this->debug) {
            $cli = !isset($_SERVER['REQUEST_METHOD']);
            echo $cli ? PHP_EOL : '<pre>';
            echo '[' . date('H:i:s'), '] ', $query;
            echo $cli ? PHP_EOL : '</pre>';
        }

        $t = microtime(true);

        $res = parent::query(
            $query,
            $this->Buffered ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT
        );

        $this->QueryTime += (microtime(true) - $t) * 1000;

        if ($this->errno && $this->dieOnError) {
            die(sprintf('MySQL ERROR [%d] %s', $this->errno, $this->error));
        }

        $this->QueryCount++;
        $this->queries[] = $query;

        return $res;
    }

    /**
     *
     */
    public function queryRows($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $this->Buffered = true;
        $result = [];
        if ($res = $this->query($query, $args)) {
            while ($row = $res->fetch_object()) {
                $result[] = $row;
            }
            $res->close();
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function queryRowsArray($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $this->Buffered = true;
        $result = [];
        if ($res = $this->query($query, $args)) {
            while ($row = $res->fetch_assoc()) {
                $result[] = $row;
            }
            $res->close();
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function queryRow($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $this->Buffered = true;
        $result = null;
        if ($res = $this->query($query, $args)) {
            $result = $res->fetch_object();
            $res->close();
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function queryRowArray($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $this->Buffered = true;
        $result = null;
        if ($res = $this->query($query, $args)) {
            $result = $res->fetch_assoc();
            $res->close();
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function queryOne($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $this->Buffered = true;
        $result = null;
        if ($res = $this->query($query, $args)) {
            $result = is_object($res) ? $res->fetch_row()[0] : $res;
            $res->close();
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function queryCol($query)
    {
        list($query, $args) = $this->splitQueryAndArgs(func_get_args());

        $this->Buffered = true;
        $result = [];
        if ($res = $this->query($query, $args)) {
            while ($row = $res->fetch_row()) {
                $result[] = $row[0];
            }
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function call($procedure)
    {
        $args = func_get_args();
        // Shift out proc. name
        $procedure = array_shift($args);
        // Quote proc. args
        $args = array_map([$this, 'quote'], $args);
        return $this->query('CALL `{1}`({2})', $procedure, implode(', ', $args));
    }

    /**
     *
     */
    public function quote($value)
    {
        return is_numeric($value)
             ? $value
             : '"' . $this->real_escape_string($value) . '"';
    }

    /**
     *
     */
    public function truncate($table)
    {
        // Call direkt parent method
        return parent::query('TRUNCATE TABLE `'.$table.'`');
    }

    /**
     *
     */
    // @codingStandardsIgnoreLine
    public function multi_query($sql)
    {
        foreach (explode(';', $sql) as $query) {
            $this->queries[] = preg_replace('~\s+~', ' ', $query);
        }
        return parent::multi_query($sql);
    }

    /**
     *
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     *
     */
    public function set($key, $value)
    {
        $sql = vsprintf(
            'INSERT INTO `%1$s`
                (`%2$s`, `%3$s`)
            VALUES
                (LOWER("{1}"), "{2}")
            ON DUPLICATE KEY UPDATE
                `%3$s` = "{2}"',
            $this->Settings
        );

        $this->query($sql, $key, $value);
    }

    /**
     *
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     *
     */
    public function get($key)
    {
        $sql = vsprintf(
            'SELECT `%3$s`
               FROM `%1$s`
              WHERE `%2$s` = LOWER("{1}")
              LIMIT 1',
            $this->Settings
        );

        if (($res = $this->query($sql, $key)) && ($row = $res->fetch_row())) {
            return $row[0];
        }
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $Cli = false;

    /**
     *
     */
    protected $dieOnError = false;

    /**
     * Table name, key field name, value field name
     */
    protected $Settings = ['settings', 'key', 'value'];

    /**
     *
     */
    protected $Buffered = false;

    /**
     *
     */
    protected $QueryCount = 0;

    /**
     *
     */
    protected $QueryTime = 0;

    /**
     *
     */
    protected $debug = false;

    /**
     *
     */
    protected function splitQueryAndArgs($args)
    {
        $query = array_shift($args);

        if (isset($args[0]) && is_array($args[0])) {
            $args = $args[0];
        }

        return [$query, $args];
    }
}
