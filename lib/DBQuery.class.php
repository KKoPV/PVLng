<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class DBQuery {

	/**
	 *
	 */
	public function __construct( $table=NULL, $fields=array() ) {
		return $this->select($table, $fields);
	}

	/**
	 *
	 */
	public function select( $table, $fields=array() ) {
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
	public function insert( $table ) {
		$this->reset();
		$this->sql = 'INSERT';
		$this->table = $table;
		return $this;
	}

	/**
	 *
	 */
	public function replace( $table ) {
		$this->reset();
		$this->sql = 'REPLACE';
		$this->table = $table;
		return $this;
	}

	/**
	 *
	 */
	public function update( $table ) {
		$this->reset();
		$this->sql = 'UPDATE';
		$this->table = $table;
		return $this;
	}

	/**
	 *
	 */
	public function delete( $table ) {
		$this->sql = 'DELETE';
		$this->table = $table;
		return $this;
	}

	/**
	 *
	 */
	public function set( $field, $value, $raw=FALSE ) {
		if ($field != '') {
			$this->set[$field] = array($value, $raw);
		}
		return $this;
	}

	/**
	 *
	 */
	public function get( $field, $as='', $raw=FALSE ) {
		if ($field != '') {
			if ($as != '') $as = ' AS `' . $as . '`';
			$this->get[] = ($raw ? $field : $this->field($field)) . $as;
		}
		return $this;
	}

	/**
	 * @field string|array String => USING(...), array ==> ON $key = $value
	 */
	public function join( $table, $field, $dir='' ) {
		$join = ($dir ? strtoupper($dir).' ' : '') . 'JOIN `' . $table . '` ';
		if (is_array($field)) {
			$join .= 'ON ';
			$j = array();
			foreach ($field as $key=>$value) {
				$j[] = $this->_table() . '.' . $this->field($key) . ' = '
					 . '`' . $table . '`.' . $this->field($value);
            }
			$join .= implode(' AND ', $j);
		} else {
			$join .= 'USING (' . $this->field($field) . ')';
		}

		$this->join[] = $join;
		return $this;
	}

	/**
	 *
	 */
	public function where( $field, $cond='', $value='' ) {
		if ($field != '') {
			if ($cond) $cond = ' ' . $cond . ' ' . $this->quote($value);
			$this->where[] = $this->field($field) . $cond;
		}
		return $this;
	}

	/**
	 *
	 */
	public function whereEQ( $field, $value='' ) {
		return $this->where($field, '=', $value);
	}

	/**
	 *
	 */
	public function whereNE( $field, $value='' ) {
		return $this->where($field, '<>', $value);
	}

	/**
	 *
	 */
	public function whereLT( $field, $value='' ) {
		return $this->where($field, '<', $value);
	}

	/**
	 *
	 */
	public function whereLE( $field, $value='' ) {
		return $this->where($field, '<=', $value);
	}

	/**
	 *
	 */
	public function whereGT( $field, $value='' ) {
		return $this->where($field, '>', $value);
	}

	/**
	 *
	 */
	public function whereGE( $field, $value='' ) {
		return $this->where($field, '>=', $value);
	}

	/**
	 *
	 */
	public function whereBT( $field, $from, $to ) {
		$this->where[] = $this->field($field) . ' BETWEEN '
		               . $this->quote($from) . ' AND ' . $this->quote($to);
		return $this;
	}

	/**
	 *
	 */
	public function whereNotBT( $field, $from, $to ) {
		$this->where[] = 'NOT '
		               . $this->field($field) . ' BETWEEN '
		               . $this->quote($from) . ' AND ' . $this->quote($to);
		return $this;
	}

	/**
	 *
	 */
	public function whereNULL( $field ) {
		if ($field != '') {
			$this->where[] = $this->field($field) . ' IS NULL';
		}
		return $this;
	}

	/**
	 *
	 */
	public function whereNotNULL( $field ) {
		if ($field != '') {
			$this->where[] = $this->field($field) . ' IS NOT NULL';
		}
		return $this;
	}

	/**
	 *
	 */
	public function where_or() {
		if (($idx = count($this->where)) > 0) $this->whereExtra['or'][$idx] = TRUE;
		return $this;
	}

	/**
	 *
	 */
	public function where_open() {
		$this->whereExtra['('][count($this->where)] = TRUE;
		return $this;
	}

	/**
	 *
	 */
	public function where_close() {
		$this->whereExtra[')'][count($this->where)-1] = TRUE;
		return $this;
	}

	/**
	 *
	 */
	public function where_close_open() {
		$this->whereExtra[')'][count($this->where)-1] = TRUE;
		$this->whereExtra['('][count($this->where)] = TRUE;
		return $this;
	}

	/**
	 *
	 */
	public function group( $field ) {
		if ($field != '') {
			$this->group[] = $this->field($field);
		}
		return $this;
	}

	/**
	 *
	 */
	public function having( $field, $cond, $value ) {
		if ($field != '') {
			if ($cond) $cond = ' ' . $cond . ' ' . $this->quote($value);
			$this->having[] = $this->field($field) . $cond;
		}
		return $this;
	}

	/**
	 *
	 */
	public function order( $field, $ASC=TRUE ) {
		if ($field != '') {
			$this->order[] = $this->field($field) . ($ASC ? '' : ' DESC');
		}
		return $this;
	}

	/**
	 *
	 */
	public function limit( $limit ) {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Wrap SQL functions
	 *
	 * $this->MAX('field')      => MAX(`field`)
	 * $this->ROUND('field', 4) => ROUND(`field`, 4)
	 */
	public function __call( $method, $params ) {
		$result = $method . '(' . $this->field($params[0]);
		$count = count($params);
		for ($i=1; $i<$count; $i++) {
			$result .= ', ' . $this->quote($params[$i]);
		}
		return $result . ')';
	}

	/**
	 *
	 */
	public function SQL() {
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
	public function __toString() {
		return $this->SQL() . ';';
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
	protected function reset() {
		$this->sql    = '';
		$this->get    = array();
		$this->set    = array();
		$this->where  = array();
		$this->group  = array();
		$this->having = array();
		$this->order  = array();
		$this->limit  = 0;
	}

	/**
	 *
	 */
	protected function SelectSQL() {
		return 'SELECT '
		     . $this->_get()
		     . "\n".'  FROM `' . $this->table . '`'
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
	protected function InsertSQL( $mode='INSERT' ) {
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
	protected function UpdateSQL() {
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
	protected function DeleteSQL() {
		return 'DELETE '
		     . "\n".'	FROM `' . $this->table . '`'
		     . $this->_where()
		     . $this->_limit();
	}

	/**
	 *
	 */
	protected function field( $field ) {
		return preg_match('~^[[:alpha:]_][\w_]*$~', $field) ? '`' . $field . '`' : $field;
	}

	/**
	 *
	 */
	protected function quote( $value ) {
		// Interpret values beginning with ! as raw data
		if (substr($value, 0, 1) == '!') return substr($value, 1);

		if ((string) $value == (string) +$value) return $value;

		return '"' . str_replace(array('\\',   '"',   '\'',   "\r", "\n"),
		                         array('\\\\', '\\"', '\\\'', '\r', '\n'), $value) . '"';
	}

	/**
	 *
	 */
	protected function _table() {
		return '`' . $this->table . '`';
	}

	/**
	 *
	 */
	protected function _get() {
		$s = implode("\n".'      ,', $this->get);
		return $s ? $s : '*';
	}

	/**
	 *
	 */
	protected function _join() {
		return implode("\n", $this->join);
	}

	/**
	 *
	 */
	private function _whereGroup( $idx ) {
		return (isset($this->whereExtra['('][$idx]) ? '( ' : '')
			 . $this->where[$idx]
			 . (isset($this->whereExtra[')'][$idx]) ? ' )' : '');
	}

	/**
	 *
	 */
	protected function _where() {
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
	protected function _group() {
		$s = implode("\n".'         ,', $this->group);
		return $s ? "\n" . ' GROUP BY ' . $s : '';
	}

	/**
	 *
	 */
	protected function _having() {
		$s = implode(' AND ', $this->having);
		return $s ? "\n" . 'HAVING ' . $s : '';
	}

	/**
	 *
	 */
	protected function _order() {
		$s = implode(', ', $this->order);
		return $s ? "\n" . ' ORDER BY ' . $s : '';
	}

	/**
	 *
	 */
	protected function _limit() {
		return $this->limit ? "\n" . ' LIMIT ' . $this->limit : '';
	}

}
