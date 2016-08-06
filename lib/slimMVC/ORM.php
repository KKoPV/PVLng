<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace slimMVC;

/**
 * Custom constant for multi_query
 */
defined('MYSQLI_OBJECT') || define('MYSQLI_OBJECT', 4);

/**
 *
 */
abstract class ORM implements \Iterator, \Countable {

    /**
     *
     */
    public static function setDatabase(\MySQLi $db)
    {
        self::$db = $db;
    }

    /**
     *
     */
    public static function getDatabase()
    {
        return self::$db;
    }

    /**
     * Shortcut factory function for fluid interface
     */
    public static function f()
    {
        $name = get_called_class();
        $args = func_get_args();
        if (count($args) == 0) $args = null;
        return new $name($args);
    }

    /**
     *
     * @param mixed $id Key describing one row, on primary keys
     *                  with more than field, provide an array
     */
    public function __construct($id=null)
    {
        if (!self::$db) {
            throw new \Exception('Call '.__CLASS__.'::setDatabase() before!');
        }

        $this->raw = $this->fields;

        if ($id !== null) $this->filter($this->primary, $id)->findOne();
    }

    /**
     *
     * @return void
     */
    public function setThrowException($throw=true)
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
     * @return instance
     */
    public function filterRaw($condition, $params=array())
    {
        $this->filter[] = self::$db->sql($condition, $params);
        return $this;
    }

    /**
     *
     * @return instance
     */
    public function filter($field, $value=null, $reset=false)
    {
        if ($reset) {
            $this->filter = array();
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
                if (!empty($value)) {
                    // OR condition
                    $q = array();
                    foreach ($value as $v) {
                        $q[] = $field.' = '.$this->quote($v);
                    }
                    $this->filter[] = '( ' . implode(' OR ', $q) . ' )';
                }
            }
        } else {
            if (func_num_args() == 1) {
                // Array with key=>value pairs
                foreach ($field as $key=>$value) {
                    $this->filter[] = $this->field($key).' = '.$this->quote($value);
                }
            } else {
                if (!is_array($value)) $value = array($value);
                foreach ($field as $key=>$f) {
                    if (array_key_exists($key, $value))
                        $this->filter[] = $this->field($f).' = '.$this->quote($value[$key]);
                }
            }
        }

        return $this;
    }

    /**
     *
     * @return instance
     */
    public function order($field, $desc=false)
    {
        $this->order[] = $this->field($field) . ($desc ? ' DESC' : '');
        return $this;
    }

    /**
     *
     * @return instance
     */
    public function orderDesc($field)
    {
        return $this->order($field, true);
    }

    /**
     *
     * @return instance
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     *
     * @return array of result objects
     */
    public function find()
    {
        $this->resultRows     = array();
        $this->resultPosition = 0;

        $sql = 'SELECT * FROM `'.$this->table.'`'
             . $this->_filter()
             . $this->_order()
             . $this->_limit();

        if ($res = $this->_query($sql)) {
            while ($row = $res->fetch_assoc()) {
                foreach ($row as $key=>$value) {
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
        $this->resultRows     = array();
        $this->resultPosition = 0;

        $sql = 'SELECT * FROM `'.$this->table.'`'
             . $this->_filter()
             . $this->_order()
             . ' LIMIT 1';

        if (($res = $this->_query($sql)) && ($row = $res->fetch_assoc())) {
            foreach ($row as $key=>$value) {
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
        // Select direct from information_schema, SELECT COUNT(*) on large partitioned takes to long
        $sql = 'SELECT `table_rows`
                  FROM `information_schema`.`tables`
                 WHERE `table_schema` = DATABASE()
                   AND `table_name`   = '.$this->quote($this->table).'
                 LIMIT 1';
        return (($res = $this->_query($sql)) && (($row = $res->fetch_array(MYSQLI_NUM))) ? +$row[0] : 0);
    }

    /**
     *
     */
    public function asAssoc()
    {
        if ($this->lastFind == 1) {
            $data = array();
            foreach (array_keys($this->fields) as $field) {
                // Force getter usage!
                $data[$field] = $this->get($field);
            }
            return $data;
        } else {
            $rows = array();
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
            return $this->_asObject();
        } else {
            $rows = array();
            foreach ($this->resultRows as $row) {
                $rows[] = $row->_asObject();
            }
            return !empty($rows) ? $rows : $this->_asObject();
        }
    }

    /**
     *
     */
    public function getFields()
    {
        return array_keys($this->fields);
    }

    /**
     *
     */
    public function insert()
    {
        return $this->_insert('INSERT');
    }

    /**
     *
     */
    public function replace()
    {
        return $this->_insert('REPLACE');
    }

    /**
     *
     */
    public function update()
    {
        $set = array();
        foreach ($this->fields as $field=>$value) {
            // Skip primary key(s) and not changed values
            if (!in_array($field, $this->primary) &&
                (!array_key_exists($field, $this->oldfields) ||
                 ((string) $value != (string) $this->oldfields[$field]) ||
                 ($this->raw[$field] != ''))) {
                if ($this->raw[$field] != '') {
                    $set[] = sprintf('`%s` = %s', $field, $this->raw[$field]);
                } elseif ($value == '' AND $this->nullable[$field]) {
                    $set[] = sprintf('`%s` = NULL', $field);
                } else {
                    $set[] = sprintf('`%s` = %s', $field, $this->quote($value));
                }
            }
        }

        // Anything changed?
        if (empty($set)) return 0;

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $set) . $this->_filter() . ' LIMIT 1';
        $this->_query($sql);
        return self::$db->affected_rows;
    }

    /**
     *
     */
    public function delete()
    {
        $sql = 'DELETE FROM `' . $this->table . '`'
             . $this->_filter($this->primary, $this->primaryValues());
        return $this->_query($sql);
    }

    /**
     *
     */
    public function truncate()
    {
        return $this->_query('TRUNCATE `' . $this->table . '`');
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
        return !!self::$db->errno;
    }

    /**
     *
     */
    public function Error()
    {
        return self::$db->error;
    }

    /**
     *
     */
    public function reset()
    {
        foreach ($this->fields as $key=>$value) {
            $this->fields[$key] = $this->raw[$key] = NULL;
        }
        $this->filter = array();
        $this->order = array();
        $this->limit = NULL;
        return $this;
    }

    /**
     *
     */
    public function set($name, $value=null)
    {
        if ($name != '') {
            if (is_array($name) && func_num_args() == 1) {
                // Array as only parameter given
                foreach ($name as $key=>$value) $this->set($key, $value);
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
    public function setRaw($name, $value=null)
    {
        if ($name != '') {
            if (is_array($name) && func_num_args() == 1) {
                // Array as only parameter given
                foreach ($name as $key=>$value) $this->setRaw($key, $value);
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
    public function multi_query(
        $sql, $resulttype=MYSQLI_NUM, $class='stdClass', array $params=array()
    ) {

        $result = array();
        $i = 0;

        // Calling a procedure is a bit affort via multi_query()
        // http://php.net/manual/mysqli.multi-query.php
        if (self::$db->multi_query($sql)) {
            do {
                // Will return only 1 row
                if ($res = self::$db->store_result()) {
                    // Rows for each query
                    if ($resulttype == MYSQLI_OBJECT) {
                        while ($row = $res->fetch_object($class, $params)) $result[$i][] = $row;
                    } else {
                        while ($row = $res->fetch_array($resulttype)) $result[$i][] = $row;
                    }
                    $i++;
                }
            } while (self::$db->more_results() && self::$db->next_result());
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
    public function multi_query_merge(
        $sql, $resulttype=MYSQLI_NUM, $class='stdClass', array $params=array()
    ) {

        $result = array();
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
        $this->resultRows     = array();
        $this->resultPosition = 0;
        $this->lastFind       = 1;
        $this->oldfields      = $this->fields;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * Overwrite for real classes according to real fields,
     * but only for tabels without AUTOINC
     */
    protected function onDuplicateKey() {}

    /**
     *
     */
    protected static $db;

    /**
     *
     */
    protected $throwException = false;

    /**
     *
     */
    protected $table;

    /**
     *
     */
    protected $sql = array();

    /**
     *
     */
    protected $fields = array();

    /**
     *
     */
    protected $raw = array();

    /**
     *
     */
    protected $oldfields = array();

    /**
     *
     */
    protected $nullable = array();

    /**
     *
     */
    protected $primary = array();

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
    protected $filter = array();

    /**
     *
     */
    protected $order;

    /**
     *
     */
    protected $limit;

    /**
     *
     */
    protected $resultPosition;

    /**
     *
     */
    protected $resultRows = array();

    /**
     * Needed for update/delete
     */
    protected function primaryValues()
    {
        $values = array();
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
        return '"' . self::$db->real_escape_string($value) . '"';
    }

    /**
     *
     */
    protected function _asObject()
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
    protected function _insert($mode)
    {
        if ($this->autoinc) {
            $this->fields[$this->autoinc] = null;
        }

        $keys = $values = array();
        foreach ($this->fields as $field=>$value) {
            // Don't insert/replace empty fields
            if ($value != '' || $this->raw[$field] != '') {
                $keys[]   = $field;
                $values[] = $this->raw[$field] == '' ? $this->quote($value) : $this->raw[$field];
            }
        }

        $sql = $mode.' INTO ' . $this->table
             . ' (`' . implode('`, `', $keys) . '`) '
             . 'VALUES'
             . ' (' . implode(', ', $values) . ')';

        if (($mode == 'INSERT') && ($dup = $this->onDuplicateKey())) {
            $sql .= ' ON DUPLICATE KEY UPDATE ' . $dup;
        }

        try {
            if ($this->_query($sql) && $this->autoinc AND self::$db->insert_id) {
                $this->set($this->autoinc, self::$db->insert_id);
            }
            return (self::$db->affected_rows <= 0) ? 0 : self::$db->affected_rows;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    protected function _filter()
    {
        return !empty($this->filter) ? ' WHERE ' . implode(' AND ', $this->filter) : '';
    }

    /**
     *
     */
    protected function _order()
    {
        $order = !empty($this->order) ? $this->order : $this->primary;
        if (!empty($order)) {  // e.g. Views don't have a primary key!
            return ' ORDER BY ' . implode(', ', array_map(function($f) { return $this->field($f); }, $order));
        }
    }

    /**
     *
     */
    protected function _limit()
    {
        return ($this->limit != '') ? ' LIMIT '.$this->limit : '';
    }

    /**
     * Wrapper for real query()
     */
    protected function _query($sql)
    {
        $this->sql[] = $sql;
        $res = self::$db->query($sql);

        // You have an error in your SQL syntax; check the manual ...
        if (self::$db->errno == 1149) die(self::$db->error . ' : ' . $sql);

        if ($this->throwException AND self::$db->errno) {
            throw new \Exception('Database error: '.self::$db->error, self::$db->errno);
        }

        return $res;
    }

}
