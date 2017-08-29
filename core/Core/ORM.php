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
 * Interfaces
 */
use Iterator;
use Countable;

/**
 * Classes
 */
use MySQLi;
use StdClass;

/**
 * Custom constant for multi_query
 */
// @codingStandardsIgnoreStart
if (!defined('MYSQLI_OBJECT')) {
    define('MYSQLI_OBJECT', 4);
}
// @codingStandardsIgnoreEnd

/**
 *
 */
abstract class ORM implements Iterator, Countable
{
    /**
     *
     */
    public static function setDatabase(MySQLi $db)
    {
        static::$db = $db;
    }

    /**
     *
     */
    public static function getDatabase()
    {
        return static::$db;
    }

    /**
     * Shortcut factory function for fluid interface
     */
    public static function f()
    {
        $name = get_called_class();
        $args = func_get_args();
        if (count($args) == 0) {
            $args = null;
        }
        return new $name($args);
    }

    /**
     * Create MEMORY table
     */
    public static function checkMemoryTable()
    {
        if (!static::$db) {
            throw new Exception('Call '.__CLASS__.'::setDatabase() before!');
        }

        if (static::$memory) {
            // Create memory tables only on 1st call
            static::$db->query(static::$createSQL);
            static::$memory = false;
        }
    }

    /**
     * Class constructor
     *
     * @param mixed $id Key describing one row, on primary keys
     *                  with more than field, provide an array
     */
    public function __construct($id = null)
    {
        if (!static::$db) {
            throw new Exception('Call '.__CLASS__.'::setDatabase() before!');
        }

        self::checkMemoryTable();

        $this->raw = $this->fields;
        if ($id !== null) {
            $this->filter($this->primary, $id)->findOne();
        }
    }

    /**
     *
     * @return Instance
     */
    public function setThrowException($throw = true)
    {
        $this->throwException = !!$throw;
        return $this;
    }

    /**
     *
     * @return void
     */
    public function getThrowException()
    {
        return $this->throwException;
    }

    /**
     *
     * @return Instance
     */
    public function filterRaw($condition, $params = [])
    {
        $this->filter[] = static::$db->sql($condition, $params);
        return $this;
    }

    /**
     *
     * @return Instance
     */
    public function filter($field, $value = null, $reset = false)
    {
        if ($reset) {
            $this->filter = [];
        }

        if (!is_array($field)) {
            $field = $this->field($field);
            if (!is_array($value)) {
                // Simple equal condition
                $this->filter[] = $field.' = '.$this->quote($value);
            } else {
                // Complex conditions
                if (array_key_exists('min', $value)) {
                    $this->filter[] = $field.' >= '.$this->quote($value['min']);
                    unset($value['min']);
                }
                if (array_key_exists('max', $value)) {
                    $this->filter[] = $field.' <= '.$this->quote($value['max']);
                    unset($value['max']);
                }
                if (array_key_exists('like', $value)) {
                    $this->filter[] = $field.' like '.$this->quote($value['like']);
                    unset($value['like']);
                }
                if (!empty($value)) {
                    // OR condition
                    $q = [];
                    foreach ($value as $v) {
                        $q[] = $field.' = '.$this->quote($v);
                    }
                    $this->filter[] = '( ' . implode(' OR ', $q) . ' )';
                }
            }
        } else {
            if (func_num_args() == 1) {
                // Array with key=>value pairs
                foreach ($field as $key => $value) {
                    $this->filter[] = $this->field($key).' = '.$this->quote($value);
                }
            } else {
                if (!is_array($value)) {
                    $value = array($value);
                }
                foreach ($field as $key => $f) {
                    if (array_key_exists($key, $value)) {
                        $this->filter[] = $this->field($f).' = '.$this->quote($value[$key]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     *
     * @return Instance
     */
    public function order($field)
    {
        $field = explode(',', $field);
        foreach ($field as $f) {
            if (substr($f, 0, 1) != '-') {
                $this->orderFields[] = $f;
            } else {
                $this->orderFields[] = substr($f, 1) . ' DESC';
            }
        }

        return $this;
    }

    /**
     *
     * @return Instance
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit = $offset . ', ' . $limit;
        return $this;
    }

    /**
     *
     * @return Instance
     */
    public function find()
    {
        $sql = $this->buildSelectSql() . $this->buildLimit();

        $this->resultRows     = [];
        $this->resultPosition = 0;

        if ($res = $this->runQuery($sql)) {
            while ($row = $res->fetch_assoc()) {
                foreach ($row as $key => $value) {
                    $this->fields[$key] = $value;
                }
                $this->resultRows[] = clone $this;
            }
        }
        $this->lastFind = 0;

        return $this;
    }

    /**
     *
     * @return Instance
     */
    public function findOne()
    {
        $sql = $this->buildSelectSql() . ' LIMIT 1';

        $this->resultRows     = [];
        $this->resultPosition = 0;

        if (($res = $this->runQuery($sql)) && ($row = $res->fetch_assoc())) {
            foreach ($row as $key => $value) {
                $this->fields[$key] = $value;
                $this->raw[$key]    = '';
            }
            $this->oldfields = $this->fields;
        }
        $this->lastFind = 1;

        return $this;
    }

    /**
     *
     * @return integer
     */
    public function rowCount()
    {
        // Select direct from information_schema, SELECT COUNT(*) on large
        // partitioned tables takes to long
        $sql = 'SELECT `table_rows`
                  FROM `information_schema`.`tables`
                 WHERE `table_schema` = DATABASE()
                   AND `table_name`   = '.$this->quote($this->table).'
                 LIMIT 1';
        return ($res = $this->runQuery($sql)) && (($row = $res->fetch_row())) ? +$row[0] : 0;
    }

    /**
     *
     */
    public function asAssoc()
    {
        if ($this->lastFind == 1) {
            $data = [];
            foreach (array_keys($this->fields) as $field) {
                // Force getter usage!
                $data[$field] = $this->get($field);
            }
            return $data;
        } else {
            $rows = [];
            foreach ($this->resultRows as $row) {
                $rows[] = $row->asAssoc();
            }
            return $rows;
        }
    }

    /**
     *
     */
    public function asObject()
    {
        if ($this->lastFind == 1) {
            return $this->buildObject();
        } else {
            $rows = [];
            foreach ($this->resultRows as $row) {
                $rows[] = $row->buildObject();
            }
            return !empty($rows) ? $rows : $this->buildObject();
        }
    }

    /**
     *
     */
    public function fieldNames()
    {
        return array_keys($this->fields);
    }

    /**
     *
     */
    public function insert()
    {
        return $this->doInsert('INSERT');
    }

    /**
     *
     */
    public function replace()
    {
        return $this->doInsert('REPLACE');
    }

    /**
     *
     */
    public function update()
    {
        $set = [];
        foreach ($this->fields as $field => $value) {
            // Skip primary key(s) and not changed values
            if (!in_array($field, $this->primary) &&
                (!array_key_exists($field, $this->oldfields) ||
                 ((string) $value != (string) $this->oldfields[$field]) ||
                 ($this->raw[$field] != ''))) {
                if ($this->raw[$field] != '') {
                    $set[] = sprintf('`%s` = %s', $field, $this->raw[$field]);
                } elseif ($value == '' && $this->nullable[$field]) {
                    $set[] = sprintf('`%s` = NULL', $field);
                } else {
                    $set[] = sprintf('`%s` = %s', $field, $this->quote($value));
                }
            }
        }

        // Anything changed?
        if (empty($set)) {
            return 0;
        }

        $this->runQuery(sprintf(
            'UPDATE `%s` SET %s %s LIMIT 1',
            $this->table,
            implode(',', $set),
            $this->buildFilter()
        ));

        return static::$db->affected_rows;
    }

    /**
     *
     */
    public function delete()
    {
        return $this->runQuery(sprintf(
            'DELETE FROM `%s` %s LIMIT 1',
            $this->table,
            $this->buildFilter($this->primary, $this->primaryValues())
        ));
    }

    /**
     *
     */
    public function truncate()
    {
        return $this->runQuery(sprintf('TRUNCATE `%s`', $this->table));
    }

    /**
     *
     */
    public function queries()
    {
        return $this->sql;
    }

    /**
     *
     */
    public function isError()
    {
        return !!static::$db->errno;
    }

    /**
     *
     */
    public function error()
    {
        return static::$db->error;
    }

    /**
     *
     */
    public function reset()
    {
        foreach ($this->fields as $key => $value) {
            $this->fields[$key] = $this->raw[$key] = null;
        }
        $this->filter      = [];
        $this->orderFields = [];
        $this->limit       = null;
        return $this;
    }

    /**
     *
     */
    public function set($name, $value = null)
    {
        if ($name != '') {
            if (is_array($name) && func_num_args() == 1) {
                // Array as only parameter given
                foreach ($name as $key => $value) {
                    $this->set($key, $value);
                }
            } elseif (in_array($name, array_keys($this->fields))) {
                // Use getter, could be overwritten
                $setter = 'set'.str_replace('_', '', $name);
                if (method_exists($this, $setter)) {
                    call_user_func(array($this, $setter), $value);
                } else {
                    $this->fields[$name] = $value;
                }
                $this->raw[$name] = '';
            } else {
                $this->$name = $value;
            }
        }

        return $this;
    }

    /**
     *
     */
    public function setRaw($name, $value = null)
    {
        if ($name != '') {
            if (is_array($name) && func_num_args() == 1) {
                // Array as only parameter given
                foreach ($name as $key => $value) {
                    $this->setRaw($key, $value);
                }
            } elseif (in_array($name, array_keys($this->fields))) {
                // Use getter, could be overwritten
                $setter = 'set'.str_replace('_', '', $name).'Raw';
                if (method_exists($this, $setter)) {
                    call_user_func(array($this, $setter), $value);
                } else {
                    $this->raw[$name] = $value;
                }
                $this->fields[$name] = '';
            }
        }

        return $this;
    }

    /**
     *
     */
    public function __set($name, $value)
    {
        # if ($this->throw) throw new Exception('Unknown property: '.$name);
        $this->set($name, $value);
    }

    /**
     *
     */
    public function get($name)
    {
        // Use getter if exists
        $getter = 'get'.str_replace('_', '', $name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } else {
            if (array_key_exists($name, $this->fields)) {
                return $this->fields[$name];
            }
            if (isset($this->$name)) {
                return $this->$name;
            }
            return null;
        }
    }

    /**
     *
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Iterator interface
     */

    /**
     * @return void
     */
    public function rewind()
    {
        $this->resultPosition = 0;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->resultRows[$this->resultPosition];
    }

    /**
     * @return scalar
     */
    public function key()
    {
        return $this->resultPosition;
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->resultPosition++;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return isset($this->resultRows[$this->resultPosition]);
    }

    /**
     * Countable interface
     */

    /**
     * @return int
     */
    public function count()
    {
        return count($this->resultRows);
    }

    /**
     * Extended multi_query
     *
     * @param $sql string
     * @param $resulttype integer Constant indicating what type of array should be produced
     *        Array of arrays
     *            MYSQLI_ASSOC  - associative arrays
     *            MYSQLI_NUM    - numeric arrays
     *            MYSQLI_BOTH   - both
     *            MYSQLI_OBJECT - objects
     * @param $class string
     * @param $params array
     */
    public function multi_query( // @codingStandardsIgnoreLine
        $sql,
        $resulttype = MYSQLI_NUM,
        $class = 'stdClass',
        array $params = []
    ) {
        $result = [];
        $i = 0;

        // Calling a procedure is a bit affort via multi_query()
        // http://php.net/manual/mysqli.multi-query.php
        if (static::$db->multi_query($sql)) {
            do {
                // Will return only 1 row
                if ($res = static::$db->store_result()) {
                    // Rows for each query
                    if ($resulttype == MYSQLI_OBJECT) {
                        while ($row = $res->fetch_object($class, $params)) {
                            $result[$i][] = $row;
                        }
                    } else {
                        while ($row = $res->fetch_array($resulttype)) {
                            $result[$i][] = $row;
                        }
                    }
                    $i++;
                }
            } while (static::$db->more_results() && static::$db->next_result());
        }

        return $result;
    }

    /**
     * Merge parts of multi_query into one result set
     *
     * @param $sql string
     * @param $resulttype integer Constant indicating what type of array should be produced
     *        Array of arrays
     *            MYSQLI_ASSOC  - associative arrays
     *            MYSQLI_NUM    - numeric arrays
     *            MYSQLI_BOTH   - both
     *            MYSQLI_OBJECT - objects
     * @param $class string
     * @param $params array
     */
    public function multi_query_merge( // @codingStandardsIgnoreLine
        $sql,
        $resulttype = MYSQLI_NUM,
        $class = 'stdClass',
        array $params = []
    ) {
        $result = [];
        foreach ($this->multi_query($sql, $class, $params) as $res) {
            $result = array_merge($result, $res);
        }
        return $result;
    }

    /**
     * Remove previous found results on clone
     */
    public function __clone()
    {
        $this->resultRows     = [];
        $this->resultPosition = 0;
        $this->lastFind       = 1;
        $this->oldfields      = $this->fields;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected static $db = null;

    /**
     * Call create table sql on first run and set to false
     */
    protected static $memory = false;

    /**
     * SQL for creation
     */
    protected static $createSQL = null;

    /**
     *
     */
    protected $table = null;

    /**
     *
     */
    protected $throwException = false;

    /**
     *
     */
    protected $sql = [];

    /**
     *
     */
    protected $fields = [];

    /**
     *
     */
    protected $raw = [];

    /**
     *
     */
    protected $oldfields = [];

    /**
     *
     */
    protected $nullable = [];

    /**
     *
     */
    protected $primary = [];

    /**
     *
     */
    protected $autoinc = '';

    /**
     *
     */
    protected $lastFind = 0;

    /**
     *
     */
    protected $filter = [];

    /**
     *
     */
    protected $orderFields = [];

    /**
     *
     */
    protected $limit = null;

    /**
     *
     */
    protected $resultPosition = 0;

    /**
     *
     */
    protected $resultRows = [];

    /**
     * Overwrite for real classes according to real fields,
     * but only for tabels without AUTOINC
     */
    protected function onDuplicateKey()
    {
        return '';
    }

    /**
     * Needed for update/delete
     */
    protected function primaryValues()
    {
        $values = [];
        foreach ($this->primary as $field) {
            $values[] = $this->fields[$field];
        }
        return $values;
    }

    /**
     *
     */
    protected function field($field)
    {
        return preg_match('~^[[:alpha:]_]\w*$~', $field) ? '`' . $field . '`' : $field;
    }

    /**
     *
     */
    protected function quote($value)
    {
        return is_numeric($value)
             ? $value
             : '"' . static::$db->real_escape_string($value) . '"';
    }

    /**
     * WITHOUT limit
     */
    protected function buildSelectSql()
    {
        return sprintf(
            'SELECT * FROM `%s` %s %s',
            $this->table,
            $this->buildFilter(),
            $this->buildOrder()
        );
    }

    /**
     *
     */
    protected function buildObject()
    {
        $data = new stdClass;
        foreach (array_keys($this->fields) as $field) {
            // Force getter usage!
            $data->$field = $this->get($field);
        }
        return $data;
    }

    /**
     * INSERT / REPLACE wrapper
     */
    protected function doInsert($mode)
    {
        if ($this->autoinc) {
            $this->fields[$this->autoinc] = null;
        }

        $keys = $values = [];
        foreach ($this->fields as $field => $value) {
            // Don't insert/replace empty fields
            if ($value != '' || $this->raw[$field] != '') {
                $keys[]   = $field;
                $values[] = $this->raw[$field] == '' ? $this->quote($value) : $this->raw[$field];
            }
        }

        $sql = sprintf(
            '%s INTO `%s` (`%s`) VALUES (%s)',
            $mode,
            $this->table,
            implode('`, `', $keys),
            implode(', ', $values)
        );

        if ($mode == 'INSERT') {
            $sql .= $this->buildOnDuplicateKey();
        }

        try {
            if ($this->runQuery($sql) && $this->autoinc && static::$db->insert_id) {
                $this->set($this->autoinc, static::$db->insert_id);
            }
            return (static::$db->affected_rows <= 0) ? 0 : static::$db->affected_rows;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    protected function buildFilter()
    {
        return !empty($this->filter) ? ' WHERE ' . implode(' AND ', $this->filter) : '';
    }

    /**
     *
     */
    protected function buildOrder()
    {
        $order = !empty($this->orderFields) ? $this->orderFields : $this->primary;

        if (!empty($order)) {  // e.g. Views don't have a primary key!
            return ' ORDER BY ' . implode(', ', array_map(function ($f) {
                return $this->field($f);
            }, $order));
        }
    }

    /**
     *
     */
    protected function buildLimit()
    {
        return ($this->limit != '') ? ' LIMIT '.$this->limit : '';
    }

    /**
     *
     */
    protected function buildOnDuplicateKey()
    {
        $sql = $this->onDuplicateKey();
        return ($sql) ? ' ON DUPLICATE KEY UPDATE ' . $sql : '';
    }

    /**
     * Wrapper for real query()
     */
    protected function runQuery($sql)
    {
        $this->sql[] = trim($sql);

        $res = static::$db->query($sql);

        if (static::$db->errno == 1149) {
            /**
             * Error: 1149 SQLSTATE: 42000 (ER_SYNTAX_ERROR)
             *
             *     You have an error in your SQL syntax; check the manual that
             *     corresponds to your MySQL server version for the right syntax
             *     to use
             *
             * https://dev.mysql.com/doc/refman/en/error-messages-server.html#error_er_syntax_error
             *
             * Always a hard exit!
             */
            die(static::$db->error . ' : ' . $sql);
        }

        if (static::$db->errno && $this->throwException) {
            throw new Exception('DATABASE ERROR: '.static::$db->error, static::$db->errno);
        }

        return $res;
    }
}
