<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace slimMVC;

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
    public $queries = array();

    /**
     *
     */
    public function __construct()
    {
        $args = func_get_args();

        // Adopt defaults from php.ini
        $host     = isset($args[0]) ? $args[0] : ini_get('mysqli.default_host');
        $username = isset($args[1]) ? $args[1] : ini_get('mysqli.default_user');
        $passwd   = isset($args[2]) ? $args[2] : ini_get('mysqli.default_pw');
        $dbname   = isset($args[3]) ? $args[3] : '';
        $port     = isset($args[4]) ? $args[4] : ini_get('mysqli.default_port');
        $socket   = isset($args[5]) ? $args[5] : ini_get('mysqli.default_socket');

        @parent::__construct($host, $username, $passwd, $dbname, $port, $socket);

        $this->DBName = $dbname;
        $this->Cli = !isset($_SERVER['REQUEST_METHOD']);

        // Call direct parent method for less overhead
        parent::query('SET NAMES "'.self::$charset.'"');
        parent::query('SET CHARACTER SET '.self::$charset);

        // Avoid SQL error (1690): BIGINT UNSIGNED value is out of range
        parent::query('SET sql_mode = NO_UNSIGNED_SUBTRACTION');

        mysqli_report(MYSQLI_REPORT_STRICT);
    }

    /**
     *
     */
    public function getDatabase()
    {
        return $this->DBName;
    }

    /**
     *
     */
    public function setDieOnError($die=true)
    {
        $this->dieOnError = (bool) $die;
    }

    /**
     *
     */
    public function setSettingsTable($name)
    {
        $this->Settings[0] = $name;
    }

    /**
     *
     */
    public function setSettingsKey($name)
    {
        $this->Settings[1] = $name;
    }

    /**
     *
     */
    public function setSettingsValue($name)
    {
        $this->Settings[2] = $name;
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
    public function setBuffered($buffered=true)
    {
        $this->Buffered = (bool) $buffered;
    }

    /**
     *
     */
    public function debug($debug=true)
    {
        $this->debug = (bool) $debug;
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

        if (isset($args[0])) {
            if (is_array($args[0])) $args = $args[0];
            foreach ($args as &$value) {
                $value = $this->real_escape_string($value);
            }
            $query = vsprintf($query, $args);
        }

        return $query;
    }

    /**
     *
     */
    public function query($query)
    {
        list($query, $args) = $this->query_args(func_get_args());
        $query = $this->sql($query, $args);

        if ($this->debug) {
            echo $this->Cli ? "\n" : '<pre>';
            echo '[' . date('H:i:s'), '] ', $query;
            echo $this->Cli ? "\n" : '</pre>';
        }

        $t = microtime(true);

        $res = parent::query(
            $query,
            $this->Buffered ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT
        );

        if ($this->errno && $this->dieOnError) {
            die(sprintf('MySQL ERROR [%d] %s', $this->errno, $this->error));
        }

        $this->QueryTime += (microtime(true) - $t) * 1000;
        $this->QueryCount++;
        $this->queries[] = preg_replace('~\s+~', ' ', $query);

        return $res;
    }

    /**
     *
     */
    public function queryRows($query)
    {
        list($query, $args) = $this->query_args(func_get_args());

        $this->Buffered = true;
        $result = array();
        if ($res = $this->query($query, $args)) {
            /// $t = microtime(TRUE);
            while ($row = $res->fetch_object()) $result[] = $row;
            /// $this->QueryTime += (microtime(TRUE) - $t) * 1000;
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
        list($query, $args) = $this->query_args(func_get_args());

        $this->Buffered = true;
        $result = array();
        if ($res = $this->query($query, $args)) {
            /// $t = microtime(TRUE);
            while ($row = $res->fetch_assoc()) $result[] = $row;
            /// $this->QueryTime += (microtime(TRUE) - $t) * 1000;
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
        list($query, $args) = $this->query_args(func_get_args());

        $this->Buffered = true;
        $result = NULL;
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
        list($query, $args) = $this->query_args(func_get_args());

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
        list($query, $args) = $this->query_args(func_get_args());

        $this->Buffered = true;
        $result = '';
        if ($res = $this->query($query, $args)) {
            $result = is_object($res) ? $res->fetch_row()[0] : $res;
        }
        $this->Buffered = false;

        return $result;
    }

    /**
     *
     */
    public function queryCol($query)
    {
        list($query, $args) = $this->query_args(func_get_args());

        $this->Buffered = true;
        $result = array();
        if ($res = $this->query($query, $args)) {
            while ($row = $res->fetch_array()) $result[] = $row[0];
         }
        $this->Buffered = false;

        return $result;
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
        $replace = sprintf(
            'REPLACE `%s` (`%s`, `%s`) VALUES (LOWER("{1}"), "{2}")',
            $this->Settings[0], $this->Settings[1], $this->Settings[2]
        );

        $this->query($replace, $key, $value);
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
        $query = sprintf(
            'SELECT `%s` FROM `%s` WHERE `%s` = LOWER("{1}") LIMIT 1',
            $this->Settings[2], $this->Settings[0], $this->Settings[1]
        );

        if (($res = $this->query($query, $key)) && ($obj = $res->fetch_object())) {
            return $obj->value;
        }
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $DBName;

    /**
     *
     */
    protected $Cli;

    /**
     *
     */
    protected $dieOnError = false;

    /**
     * Table name, key field name, value field name
     */
    protected $Settings = array('settings', 'key', 'value');

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
    protected function query_args($args)
    {
        $query = array_shift($args);

        if (isset($args[0]) && is_array($args[0])) {
            $args = $args[0];
        }

        return array($query, $args);
    }

}
