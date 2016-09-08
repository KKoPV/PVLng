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
class MySQLi extends \MySQLi {

    /**
     *
     */
    public $queries = array();

    /**
     *
     */
    public function __construct( $host, $username, $passwd, $dbname, $port, $socket ) {
        @parent::__construct($host, $username, $passwd, $dbname, $port, $socket);

        $this->DBName = $dbname;
        $this->Cli = !isset($_SERVER['REQUEST_METHOD']);

        // Call org. query with less overhead
        parent::query('SET NAMES "utf8"');
        parent::query('SET CHARACTER SET utf8');

        // Avoid SQL error (1690): BIGINT UNSIGNED value is out of range
        parent::query('SET sql_mode = NO_UNSIGNED_SUBTRACTION');
        mysqli_report(MYSQLI_REPORT_STRICT);
    }

    /**
     *
     */
    public function getDatabase() {
        return $this->DBName;
    }

    /**
     *
     */
    public function setDieOnError( $die=TRUE ) {
        $this->dieOnError = (bool) $die;
    }

    /**
     *
     */
    public function setSettingsTable( $name ) {
        $this->Settings[0] = $name;
    }

    /**
     *
     */
    public function setSettingsKey( $name ) {
        $this->Settings[1] = $name;
    }

    /**
     *
     */
    public function setSettingsValue( $name ) {
        $this->Settings[2] = $name;
    }

    /**
     *
     */
    public function getQueryCount() {
        return $this->QueryCount;
    }

    /**
     *
     */
    public function getQueryTime() {
        return $this->QueryTime;
    }

    /**
     *
     */
    public function setBuffered($buffered=true)
    {
        $this->Buffered = !!$buffered;
    }

    /**
     *
     */
    public function debug( $debug=TRUE ) {
        $this->debug = (bool) $debug;
        return $this;
    }

    /**
     *
     */
    public function sql( $query ) {
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
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $query = $this->sql($query, $args);

        if ($this->debug) {
            echo $this->Cli ? "\n" : '<pre>';
            echo '[' . date('H:i:s'), '] ', $query;
            echo $this->Cli ? "\n" : '</pre>';
        }

        $t = microtime(true);

        $result = parent::query(
            $query,
            $this->Buffered ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT
        );

        if ($this->errno && $this->dieOnError) {
            die(sprintf('MySQL ERROR [%d] %s', $this->errno, $this->error));
        }

        $this->QueryTime += (microtime(TRUE) - $t) * 1000;
        $this->QueryCount++;
        $this->queries[] = preg_replace('~\s+~', ' ', $query);

        return $result;
    }

    /**
     *
     */
    public function queryRows( $query ) {
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $this->Buffered = TRUE;
        $rows = array();
        if ($result = $this->query($query, $args)) {
            /// $t = microtime(TRUE);
            while ($row = $result->fetch_object()) $rows[] = $row;
            /// $this->QueryTime += (microtime(TRUE) - $t) * 1000;
            $result->close();
        }
        $this->Buffered = FALSE;

        return $rows;
    }

    /**
     *
     */
    public function queryRowsArray( $query ) {
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $this->Buffered = TRUE;
        $rows = array();
        if ($result = $this->query($query, $args)) {
            /// $t = microtime(TRUE);
            while ($row = $result->fetch_assoc()) $rows[] = $row;
            /// $this->QueryTime += (microtime(TRUE) - $t) * 1000;
            $result->close();
        }
        $this->Buffered = FALSE;

        return $rows;
    }

    /**
     *
     */
    public function queryRow( $query ) {
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $this->Buffered = TRUE;
        $row = NULL;
        if ($result = $this->query($query, $args)) {
            $row = $result->fetch_object();
            $result->close();
        }
        $this->Buffered = FALSE;

        return $row;
    }

    /**
     *
     */
    public function queryRowArray( $query ) {
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $this->Buffered = TRUE;
        $row = NULL;
        if ($result = $this->query($query, $args)) {
            $row = $result->fetch_assoc();
            $result->close();
        }
        $this->Buffered = FALSE;

        return $row;
    }

    /**
     *
     */
    public function queryOne( $query ) {
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $rc = '';
        if ($result = $this->query($query, $args)) {
            if (is_object($result)) {
                $a = $result->fetch_row();
                $rc = $a[0];
            } else {
                $rc = $result;
            }
        }
        return $rc;
    }

    /**
     *
     */
    public function queryCol( $query ) {
        $args = func_get_args();
        $query = array_shift($args);

        if (isset($args[0]) AND is_array($args[0])) $args = $args[0];

        $rows = array();
        if ($result = $this->query($query, $args)) {
            while ($row = $result->fetch_array()) $rows[] = $row[0];
         }
        return $rows;
    }

    /**
     *
     */
    public function truncate( $table, $optimize=true ) {
        $rc = $this->query('TRUNCATE  `{1}`', $table);
        if ($optimize) {
            $rc += $this->query('OPTIMIZE  `{1}`', $table);
        }
        return $rc;
    }

    /**
     *
     */
    public function multi_query( $sql ) {
        foreach (explode(';', $sql) as $query) {
            $this->queries[] = preg_replace('~\s+~', ' ', $query);
        }
        return parent::multi_query($sql);
    }

    /**
     *
     */
    public function __set( $key, $value ) {
        $this->set($key, $value);
    }

    /**
     *
     */
    public function set( $key, $value ) {
        $replace = sprintf('REPLACE `%s` (`%s`, `%s`) VALUES (LOWER("{1}"), "{2}")',
                           $this->Settings[0], $this->Settings[1], $this->Settings[2]);

        $this->query($replace, $key, $value);
    }

    /**
     *
     */
    public function __get( $key ) {
        return $this->get($key);
    }

    /**
     *
     */
    public function get( $key ) {
        $query = sprintf('SELECT `%s` FROM `%s` WHERE `%s` = LOWER("{1}") LIMIT 1',
                         $this->Settings[2], $this->Settings[0], $this->Settings[1]);

        if (($result = $this->query($query, $key)) && ($obj = $result->fetch_object())) {
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

}
