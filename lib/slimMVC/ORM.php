<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace slimMVC;

/**
 *
 */
abstract class ORM implements \Iterator, \Countable {

    /**
     *
     * @param mixed $id Key describing one row, on primary keys
     *                  with more than field, provide an array
     */
    public function __construct( $id=NULL ) {
        $this->app = App::getInstance();
        $this->db = $this->app->db;

        if (empty($this->fields)) {
            $schemaKey = 'ORMtable.' . MySQLi::getDatabase() . '.' . $this->table;
            if ($schema = $this->app->cache->get($schemaKey)) {
                list($this->fields, $this->nullable, $this->primary, $this->autoinc) = $schema;
            } else {
                // Read table schema
                $res = $this->db->query('SHOW COLUMNS FROM '.$this->table);
                while ($row = $res->fetch_object()) {
                    $this->fields[$row->Field]   = '';
                    $this->nullable[$row->Field] = ($row->Null == 'YES');
                    if ($row->Key   == 'PRI') $this->primary[] = $row->Field;
                    if ($row->Extra == 'auto_increment') $this->autoinc = $row->Field;
                }
                $this->app->cache->set($schemaKey, array(
                    $this->fields, $this->nullable, $this->primary, $this->autoinc
                ));
            }
        }

        if ($id !== NULL) $this->filter($this->primary, $id)->findOne();
    }

    /**
     *
     * @return void
     */
    public function setThrowException( $throw=TRUE ) {
        $this->throwException = (bool) $throw;
    }

    /**
     *
     * @return void
     */
    public function getThrowException( $throw=TRUE ) {
        return $this->throwException;
    }

    /**
     *
     * @return instance
     */
    public function filter( $field, $value=NULL ) {
        if (!is_array($field)) {
            $field = $this->field($field);
            if (!is_array($value)) {
                // Simple equal condition
                $this->filter[] = $field.' = "'.$this->quote($value).'"';
            } else {
                // Complex conditions
                if (array_key_exists('min', $value)) {
                    $this->filter[] = $field.' >= "'.$this->quote($value['min']).'"';
                    unset($value['min']);
                }
                if (array_key_exists('max', $value)) {
                    $this->filter[] = $field.' <= "'.$this->quote($value['max']).'"';
                    unset($value['max']);
                }
                if (!empty($value)) {
                    // OR condition
                    $q = array();
                    foreach ($value as $v) {
                        $q[] = $field.' = "'.$this->quote($v).'"';
                    }
                    $this->filter[] = '( ' . implode(' OR ', $q) . ' )';
                }
            }
        } else {
            if (func_num_args() == 1) {
                // Array with key=>value pairs
                foreach ($field as $key=>$value) {
                    $this->filter[] = $this->field($key).' = "'.$this->quote($value).'"';
                }
            } else {
                if (!is_array($value)) $value = array($value);
                foreach ($field as $key=>$f) {
                    if (array_key_exists($key, $value))
                        $this->filter[] = $this->field($f).' = "'.$this->quote($value[$key]).'"';
                }
            }
        }

        return $this;
    }

    /**
     *
     * @return instance
     */
    public function order( $field, $desc=FALSE ) {
        $this->order[] = $this->field($field) . ($desc ? ' DESC' : '');
        return $this;
    }

    /**
     *
     * @return instance
     */
    public function limit( $limit ) {
        $this->limit = $limit;
        return $this;
    }

    /**
     *
     * @return array of result objects
     */
    public function find() {
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
    public function findOne() {
        $this->resultRows     = array();
        $this->resultPosition = 0;

        $sql = 'SELECT * FROM `'.$this->table.'`' . $this->_filter() . ' LIMIT 1';

        if ($res = $this->_query($sql) AND $row = $res->fetch_assoc()) {
            foreach ($row as $key=>$value) {
                $this->fields[$key] = $value;
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
    public function rowCount() {
        // Select direct from information_schema, SELECT COUNT(*) on large partitioned takes to long
        $sql = 'SELECT `table_rows`
                  FROM `information_schema`.`tables`
                 WHERE `table_schema` = DATABASE()
                   AND `table_name`  = "'.$this->table.'"';
        return ($res = $this->_query($sql) AND $row = $res->fetch_array(MYSQLI_NUM)) ? +$row[0] : 0;
    }

    /**
     *
     */
    public function asAssoc() {
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
    public function getFields() {
        return array_keys($this->fields);
    }

    /**
     *
     */
    public function insert() {
        return $this->_insert('INSERT');
    }

    /**
     *
     */
    public function replace() {
        return $this->_insert('REPLACE');
    }

    /**
     *
     */
    public function update() {
        $set = array();
        foreach ($this->fields as $field=>$value) {
            // Skip primary key(s) and not changed values
            if (!in_array($field, $this->primary) AND
                (!array_key_exists($field, $this->oldfields) OR (string) $value != (string) $this->oldfields[$field])) {
                if ($value == '' AND $this->nullable[$field]) {
                    $set[] = sprintf('`%s` = NULL', $field);
                } else {
                    $set[] = sprintf('`%s` = "%s"', $field, $this->quote($value));
                }
            }
        }

        // Anything changed?
        if (empty($set)) return 0;

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $set)
             . $this->_filter($this->primary, $this->primaryValues());

        return $this->_query($sql);
    }

    /**
     *
     */
    public function delete() {
        $sql = 'DELETE FROM ' . $this->table
             . $this->_filter($this->primary, $this->primaryValues());
        return $this->_query($sql);
    }

    /**
     *
     */
    public function truncate() {
        return $this->_query('TRUNCATE ' . $this->table);
    }

    /**
     *
     */
    public function queries() {
        return $this->sql;
    }

    /**
     *
     */
    public function isError() {
        return (bool) $this->db->errno;
    }

    /**
     *
     */
    public function Error() {
        return $this->db->error;
    }

    /**
     *
     */
    public function reset() {
        foreach ($this->fields as &$value) $value = NULL;
        $this->filter = array();
        $this->order = array();
        $this->limit = NULL;
        return $this;
    }

    /**
     *
     */
    public function set( $name, $value=NULL ) {
        if ($name != '') {
            if (is_array($name) AND func_num_args() == 1) {
                foreach ($name as $key=>$value) $this->set($key, $value);
            } elseif (in_array($name, array_keys($this->fields))) {
                // Use getter, could be overwritten
                $setter = 'set'.str_replace('_', '', $name);
                if (method_exists($this, $setter)) {
                    call_user_func(array($this, $setter), $value);
                } else {
                    $this->fields[$name] = $value;
                }
            } else {
                $this->$name = $value;
            }
        }

        return $this;
    }

    /**
     *
     */
    public function __set( $name, $value ) {
        $this->set($name, $value);
#        throw new Exception('slimMVC\ORM::__set() Unknown property - '.$name);
    }

    /**
     *
     */
    public function get( $name ) {
        // Use getter if exists
        $getter = 'get'.str_replace('_', '', $name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } else {
            if (isset($this->fields[$name])) {
                return $this->fields[$name];
            } elseif (isset($this->$name)) {
                return $this->$name;
            } else {
                return NULL;
            }
        }
    }

    /**
     *
     */
    public function __get( $name ) {
        return $this->get($name);
#        throw new Exception('slimMVC\ORM::__get() Unknown property - '.$name);
    }

    /**
     * Iterator interface
     */

    /**
     * @return void
     */
    public function rewind() {
        $this->resultPosition = 0;
    }

    /**
     * @return mixed
     */
    public function current() {
        return $this->resultRows[$this->resultPosition];
    }

    /**
     * @return scalar
     */
    public function key() {
        return $this->resultPosition;
    }

    /**
     * @return void
     */
    public function next() {
        $this->resultPosition++;
    }

    /**
     * @return boolean
     */
    public function valid() {
        return isset($this->resultRows[$this->resultPosition]);
    }

    /**
     * Countable interface
     */

    /**
     * @return int
     */
    public function count() {
        return count($this->resultRows);
    }

    /**
     * Remove previous found results on clone
     */
    public function __clone() {
        $this->resultRows     = array();
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
    protected $app;

    /**
     *
     */
    protected $db;

    /**
     *
     */
    protected $throwException = FALSE;

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
    protected $autoinc;

    /**
     *
     */
    protected $lastFind;

    /**
     *
     */
    protected $filter;

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
    protected $resultRows;

    /**
     * needed for update/delete
     */
    protected function primaryValues() {
        $values = array();
        foreach ($this->primary as $field) {
            $values[] = $this->fields[$field];
        }
        return $values;
    }

    /**
     *
     */
    protected function field( $field ) {
        return preg_match('~^[[:alpha:]_]\w*$~', $field) ? '`' . $field . '`' : $field;
    }

    /**
     *
     */
    protected function quote( $value ) {
        return $this->db->real_escape_string($value);
    }

    /**
     * INSERT / REPLACE wrapper
     */
    protected function _insert( $mode ) {
        $keys = $values = array();
        foreach ($this->fields as $field=>$value) {
            // Don't insert/replace empty fields
            if ((string) $value != '') {
                $keys[]   = $field;
                $values[] = '"' . $this->quote($value) . '"';
            }
        }

        $sql = $mode.' INTO ' . $this->table
             . ' (`' . implode('`, `', $keys) . '`) '
             . 'VALUES'
             . ' (' . implode(', ', $values) . ')';

        try {
            if ($this->_query($sql) AND $this->autoinc AND $this->db->insert_id) {
                $this->set($this->autoinc, $this->db->insert_id);
            }
            return ($this->db->affected_rows <= 0) ? 0 : $this->db->affected_rows;
        } catch(Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    protected function _filter() {
        return !empty($this->filter) ? ' WHERE ' . implode(' AND ', $this->filter) : '';
    }

    /**
     *
     */
    protected function _order() {
        $order = !empty($this->order) ? $this->order : $this->primary;
        if (!empty($order)) {  // e.g. Views don't have a primary key!
            return ' ORDER BY ' . implode(', ', array_map(function($f) { return $this->field($f); }, $order));
        }
    }

    /**
     *
     */
    protected function _limit() {
        return ($this->limit != '') ? ' LIMIT '.$this->limit : '';
    }

    /**
     *
     */
    protected function _query( $sql ) {
        $this->sql[] = $sql;
        $res = $this->db->query($sql);

        // You have an error in your SQL syntax; check the manual ...
        if ($this->db->errno == 1149) die($this->db->error . ' : ' . $sql);

        if ($this->throwException AND $this->db->errno) {
            throw new \Exception('Database error: '.$this->db->error, $this->db->errno);
        }

        return $res;
    }

}
