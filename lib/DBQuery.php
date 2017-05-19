<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
class DBQuery
{
    /**
     *
     */
    public static function forge($table=null, $fields=array())
    {
        return new DBQuery($table, $fields);
    }

    /**
     *
     */
    public function __construct($table=null, $fields=array())
    {
        return $this->select($table, $fields);
    }

    /**
     *
     */
    public function select($table, $fields=array())
    {
        $this->reset();
        $this->sql = 'SELECT';
        $this->table = $table;
        if (!is_array($fields)) $fields = array( $fields => '' );
        foreach ($fields as $field=>$as) $this->get($field, $as);
        return $this;
    }

    /**
     *
     */
    public function insert($table)
    {
        $this->reset();
        $this->sql = 'INSERT';
        $this->table = $table;
        return $this;
    }

    /**
     *
     */
    public function replace($table)
    {
        $this->reset();
        $this->sql = 'REPLACE';
        $this->table = $table;
        return $this;
    }

    /**
     *
     */
    public function update($table)
    {
        $this->reset();
        $this->sql = 'UPDATE';
        $this->table = $table;
        return $this;
    }

    /**
     *
     */
    public function delete($table)
    {
        $this->sql = 'DELETE';
        $this->table = $table;
        return $this;
    }

    /**
     *
     */
    public function set($field, $value, $raw=false)
    {
        if ($field != '') {
            $this->set[$field] = array($value, $raw);
        }
        return $this;
    }

    /**
     *
     */
    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    /**
     *
     */
    public function get($field, $as='')
    {
        if (is_array($field)) {
            foreach ($field as $f) $this->get($f);
        } else {
            if ($field == '0' OR $field != '') {
                if ($as != '') $as = ' AS `' . $as . '`';
                $this->get[] = $this->field($field) . $as;
            }
        }
        return $this;
    }

    /**
     * Harmonic average: count(val) / sum(1/val)
     */
    public function HAVG($field)
    {
        if ($field != '') {
            return sprintf('COUNT(%1$s)/SUM(1/%1$s)', $this->field($field));
        }
    }

    /**
     * Geometric average: exp(avg(ln(val)))
     */
    public function GAVG($field)
    {
        if ($field != '') {
            return sprintf('EXP(AVG(LN(%s)))', $this->field($field));
        }
    }

    /**
     * @field string|array String => USING(...), array ==> ON $key = $value
     */
    public function join($table, $field, $dir='')
    {
        $join = ($dir ? strtoupper($dir).' ' : '') . 'JOIN ' . $table . ' ';
        if (is_array($field)) {
            $join .= 'ON ';
            $j = array();
            foreach ($field as $key=>$value) {
                $j[] = $this->_table() . '.' . $key . ' = ' . $table . '.' . $value;
            }
            $join .= implode(' AND ', $j);
        } else {
            $join .= 'USING (' . $field . ')';
        }

        $this->join[] = $join;
        return $this;
    }

    /**
     *
     */
    protected static $conditions = array(
        'eq'      => '%1$s =  "%2$s"',
        'ne'      => '%1$s <> "%2$s"',
        'gt'      => '%1$s >  "%2$s"',
        'ge'      => '%1$s >= "%2$s"',
        'min'     => '%1$s >= "%2$s"',
        'max'     => '%1$s <= "%2$s"',
        'le'      => '%1$s <= "%2$s"',
        'lt'      => '%1$s <  "%2$s"',
        'bt'      => '%1$s BETWEEN "%2$s" AND "%3$s"',
        'notbt'   => 'NOT %1$s BETWEEN "%2$s" AND "%3$s"',
        'find'    => '%1$s LIKE "%%%2$s%%"',
        'like'    => '%1$s LIKE "%2$s"',
        'notlike' => 'NOT %1$s LIKE "%2$s"',
        'null'    => '%1$s IS NULL',
        'notnull' => 'NOT %1$s IS NULL',
    );

    /**
     *
     */
    public function filter($field, $value=null)
    {
        if (!is_array($field)) {
            if (func_num_args() == 1) {
                // Raw condition
                $this->where[] = $field;
            } else {
                $field = $this->field($field);
                if (!is_array($value)) {
                    // Simple equal condition
                    $this->where[] = $field.' = '.$this->quote($value);
                } else {
                    // Complex conditions
                    foreach ($value as $k=>$v) {
                        $kl = strtolower($k);
                        if (array_key_exists($kl, self::$conditions)) {
                            if (!is_array($v)) $v = array($v);
                            array_unshift($v, $field);
                            $this->where[] = vsprintf(self::$conditions[$kl], $v);
                            unset($value[$k]);
                        } else {
                            throw new Exception("Unknown condition: $k > $v");
                        }
                    }
                }
            }
        } else {
            if (func_num_args() == 1) {
                // Array with key=>value pairs
                foreach ($field as $key=>$value) {
                    $this->where[] = $this->field($key).' = '.$this->quote($value);
                }
            } else {
                if (!is_array($value)) $value = array($value);
                foreach ($field as $key=>$f) {
                    if (array_key_exists($key, $value))
                        $this->where[] = $this->field($f).' = '.$this->quote($value[$key]);
                }
            }
        }

        return $this;
    }

    /**
     *
     */
    public function where($field, $cond='', $value='')
    {
        if ($field != '') {
            if ($cond) $cond = ' ' . $cond . ' ' . $this->quote($value);
            $this->where[] = '`'.$field.'`' . $cond;
        }
        return $this;
    }

    /**
     *
     */
    public function whereEQ($field, $value='')
    {
        return $this->where($field, '=', $value);
    }

    /**
     *
     */
    public function whereNE($field, $value='')
    {
        return $this->where($field, '<>', $value);
    }

    /**
     *
     */
    public function whereLT($field, $value='')
    {
        return $this->where($field, '<', $value);
    }

    /**
     *
     */
    public function whereLE($field, $value='')
    {
        return $this->where($field, '<=', $value);
    }

    /**
     *
     */
    public function whereGT($field, $value='')
    {
        return $this->where($field, '>', $value);
    }

    /**
     *
     */
    public function whereGE($field, $value='')
    {
        return $this->where($field, '>=', $value);
    }

    /**
     *
     */
    public function whereBT($field, $from, $to)
    {
        $this->where[] = $field . ' BETWEEN '
                       . $this->quote($from) . ' AND ' . $this->quote($to);
        return $this;
    }

    /**
     *
     */
    public function whereNotBT($field, $from, $to)
    {
        return $this->whereBT('NOT '.$field, $from, $to);
    }

    /**
     *
     */
    public function whereLIKE($field, $value)
    {
        return $this->where($field, 'LIKE', $value);
    }

    /**
     *
     */
    public function whereNotLIKE($field, $value)
    {
        return $this->where($field, 'NOT LIKE', $value);
    }

    /**
     *
     */
    public function whereNULL($field)
    {
        $this->where[] = $field . ' IS NULL';
        return $this;
    }

    /**
     *
     */
    public function whereNotNULL($field)
    {
        return $this->whereNULL('NOT '.$field);
    }

    /**
     *
     */
    public function where_or()
    {
        if (($idx = count($this->where)) > 0) {
            $this->whereExtra['or'][$idx] = true;
        }
        return $this;
    }

    /**
     *
     */
    public function where_open()
    {
        $this->whereExtra['('][count($this->where)] = true;
        return $this;
    }

    /**
     *
     */
    public function where_close()
    {
        $this->whereExtra[')'][count($this->where)-1] = true;
        return $this;
    }

    /**
     *
     */
    public function where_close_open()
    {
        $this->whereExtra[')'][count($this->where)-1] = true;
        $this->whereExtra['('][count($this->where)] = true;
        return $this;
    }

    /**
     *
     */
    public function groupBy($field)
    {
        $this->group[] = $this->field($field);
        return $this;
    }

    /**
     *
     */
    public function group($field)
    {
        return $this->groupBy($field);
    }

    /**
     *
     */
    public function having($field, $cond, $value)
    {
        if ($cond) $cond = ' ' . $cond . ' ' . $this->quote($value);
        $this->having[] = $field . $cond;
        return $this;
    }

    /**
     *
     */
    public function orderBy($field, $desc=false)
    {
        return $this->order($field, $desc);
    }

    /**
     *
     */
    public function order($field, $desc=false)
    {
        $this->order[] = $this->field($field) . ($desc ? ' DESC' : '');
        return $this;
    }

    /**
     *
     */
    public function orderDescending($field)
    {
        return $this->order($field, true);
    }

    /**
     *
     */
    public function limit($rowCount, $offset=0)
    {
        $this->limit = $rowCount . ' OFFSET ' . $offset;
        return $this;
    }

    /**
     *
     */
    public function quote($value)
    {
        // Interpret values beginning with ! as raw data
        if (substr($value, 0, 1) == '!') return substr($value, 1);

        if ((string) $value == (string) +$value) return $value;

        return '"' . str_replace(array('\\',   '"',   '\'',   "\r", "\n"),
                                 array('\\\\', '\\"', '\\\'', '\r', '\n'), $value) . '"';
    }

    /**
     * Wrap raw SQL functions
     *
     * $this->MAX('field')      => MAX(`field`)
     * $this->ROUND('field', 4) => ROUND(`field`, 4)
     */
    public function __call($method, $params)
    {
        if (stripos($method, 'filterby') === 0) {
            return $this->filter(substr($method, 8), $params[0]);
        } else {
            return $method . '(' . implode(', ', $params) . ')';
        }
    }

    /**
     *
     */
    public function SQL()
    {
        switch ($this->sql) {
            case 'SELECT':  return $this->SelectSQL();
            case 'INSERT':
            case 'REPLACE': return $this->InsertSQL();
            case 'UPDATE':  return $this->UpdateSQL();
            case 'DELETE':  return $this->DeleteSQL();
            default:        return 'Missing SQL action (select|insert|replace|update|delete)';
        }
    }

    /**
     * String presentation
     *
     * @return string SQL query
     */
    public function __toString()
    {
        return $this->SQL() . ';';
    }

    /**
     *
     */
    public function reset()
    {
        $this->sql      = 'SELECT';
        $this->distinct = false;
        $this->get      = array();
        $this->set      = array();
        $this->where    = array();
        $this->group    = array();
        $this->having   = array();
        $this->order    = array();
        $this->limit    = 0;
        return $this;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table;

    /**
     *
     */
    protected $distinct;

    /**
     *
     */
    protected $get = array();

    /**
     *
     */
    protected $set = array();

    /**
     *
     */
    protected $join = array();

    /**
     *
     */
    protected $where = array();

    /**
     *
     */
    protected $whereExtra = array('(' => array(), 'or' => array(), ')' => array());

    /**
     *
     */
    protected $group = array();

    /**
     *
     */
    protected $having = array();

    /**
     *
     */
    protected $order = array();

    /**
     *
     */
    protected $limit = 0;

    /**
     *
     */
    protected function SelectSQL()
    {
        return 'SELECT '
             . $this->_get()
             . "\n".'  FROM ' . $this->field($this->table)
             . $this->_join()
             . $this->_where()
             . $this->_group()
             . $this->_having()
             . $this->_order()
             . $this->_limit();
    }

    /**
     *
     */
    protected function InsertSQL($mode='INSERT')
    {
        $sql = $this->sql . ' INTO `' . $this->table . '` (`'
             . implode('`,`', array_keys($this->set)) . '`) ';
        $sql .= 'VALUES (';
        foreach ($this->set as $value) {
            $sql .= ($value[1] ? $value[0] : $this->quote($value[0])) . ', ';
        }
        // remove trailing comma
        $sql = substr($sql, 0, -2) . ')';
        return $sql;
    }

    /**
     *
     */
    protected function UpdateSQL()
    {
        $sql = 'UPDATE `' . $this->table . '` SET ';
        foreach ($this->set as $key=>$value) {
            $sql .= '`' . $key . '` = '
                  . ($value[1] ? $value[0] : $this->quote($value[0]))
                  . ', ';
        }
        // remove trailing comma
        $sql = substr($sql, 0, -2)
             . $this->_where()
             . $this->_limit();
        return $sql;
    }

    /**
     *
     */
    protected function DeleteSQL()
    {
        return 'DELETE '
             . "\n".'    FROM `' . $this->table . '`'
             . $this->_where()
             . $this->_limit();
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
    protected function _table()
    {
        return '`' . $this->table . '`';
    }

    /**
     *
     */
    protected function _get()
    {
        $s = ($this->distinct ? 'DISTINCT ' : '')
           . implode("\n".'      ,', $this->get);
        return $s ?: '*';
    }

    /**
     *
     */
    protected function _join()
    {
        return implode("\n", $this->join);
    }

    /**
     *
     */
    private function _whereGroup($idx)
    {
        return (isset($this->whereExtra['('][$idx]) ? '( ' : '')
             . $this->where[$idx]
             . (isset($this->whereExtra[')'][$idx]) ? ' )' : '');
    }

    /**
     *
     */
    protected function _where()
    {
        if (empty($this->where)) return;

        $s = $this->_whereGroup(0);

        // buffer without 1st element
        $_where = array_slice($this->where, 1);

        foreach ($_where as $idx=>$where) {
            $s .= "\n"
                . (isset($this->whereExtra['or'][$idx+1]) ? '    OR ' : '   AND ')
                . $this->_whereGroup($idx+1);
        }

        return "\n" . ' WHERE ' . $s;
    }

    /**
     *
     */
    protected function _group()
    {
        $s = implode("\n".'         ,', $this->group);
        return $s ? "\n" . ' GROUP BY ' . $s : '';
    }

    /**
     *
     */
    protected function _having()
    {
        $s = implode(' AND ', $this->having);
        return $s ? "\n" . 'HAVING ' . $s : '';
    }

    /**
     *
     */
    protected function _order()
    {
        $s = implode(', ', $this->order);
        return $s ? "\n" . ' ORDER BY ' . $s : '';
    }

    /**
     *
     */
    protected function _limit()
    {
        return $this->limit ? "\n" . ' LIMIT ' . $this->limit : '';
    }

}
