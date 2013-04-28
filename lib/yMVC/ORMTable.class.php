<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC;

/**
 *
 */
abstract class ORMTable {

	/**
	 *
	 * @param mixed $id Key describing one row, on primary keys
	 *									with more than field, provide an array
	 */
	public function __construct ( $id=NULL ) {
		$this->db = MySQLi::getInstance();

		$dir = self::$CACHEDIR ?: sys_get_temp_dir();
		$schemafile = sprintf('%s%s%s~%s.tbl',
		                      self::$CACHEDIR ?: sys_get_temp_dir(), DS,
		                      substr(md5(__FILE__), -7), $this->table);

		if (!file_exists($schemafile)) {
			$res = $this->db->query('SHOW COLUMNS FROM `'.$this->table.'`');
			while ($row = $res->fetch_object()) {
				$this->fields[$row->Field] = NULL;
				if ($row->Key	 == 'PRI') $this->primary[] = $row->Field;
				if ($row->Extra == 'auto_increment') $this->autoinc = $row->Field;
			}
			file_put_contents($schemafile,
			                  serialize(array($this->fields, $this->primary, $this->autoinc)));
		} else {
			list($this->fields, $this->primary, $this->autoinc) =
				unserialize(file_get_contents($schemafile));
		}

		if (isset($id)) $this->findPrimary($id);
	}

	/**
	 *
	 * @param	integer $limit Will get in most cases one row, so default is 1
	 * @return instance
	 */
	public static function cache( $dir=NULL ) {
		self::$CACHEDIR = $dir;
	}

	/**
	 *
	 * @return instance
	 */
	public function findPrimary( $value=array() ) {
		return $this->find($this->primary, $value);
	}

	/**
	 *
	 * @return instance
	 */
	public function find( $field=array(), $value=array() ) {
		$this->reset();

		$sql = 'SELECT * FROM `'.$this->table.'`'
		     . $this->_where($field, $value)
		     . $this->_limit(1);

		if ($res = $this->_query($sql)) {
			$this->set($res->fetch_assoc());
			$this->oldfields = $this->fields;
		}

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function findMany( $field=array(), $value=array(), $order=array() ) {
		$this->reset();

		$sql = 'SELECT * FROM `'.$this->table.'`'
		     . $this->_where($field, $value)
		     . $this->_order($order);

		$rows = array();
		if ($res = $this->_query($sql)) {
			if ($res->num_rows == 1) {
				// Only 1 row, set data to $this
				$this->set($res->fetch_assoc());
				$this->oldfields = $this->fields;
				$rows[] = $this;
			} elseif ($res->num_rows > 1) {
				while ($row = $res->fetch_assoc()) {
					$new = clone $this;
					$new->set($row);
					$rows[] = $new;
				}
			}
		}

		return $rows;
	}

	/**
	 *
	 * @return instance
	 */
	public function search( $where ) {
		$this->reset();
		$sql = 'SELECT * FROM `'.$this->table.'` WHERE '.$where;
		if ($res = $this->_query($sql)) {
			$this->set($res->fetch_assoc());
			$this->oldfields = $this->fields;
		}
		return $this;
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
			    (string) $value != (string) $this->oldfields[$field]) {
				$set[] = sprintf('`%s`="%s"', $field, $this->db->real_escape_string($value));
			}
		}

		// Anything changed?
		if (empty($set)) return 0;

		$sql = 'UPDATE `' . $this->table . '` SET ' . implode(',', $set)
		     . $this->_where($this->primary, $this->primaryValues());

		return $this->_query($sql);
	}

	/**
	 *
	 */
	public function delete() {
		$sql = 'DELETE FROM `' . $this->table . '`'
		     . $this->_where($this->primary, $this->primaryValues());
		return $this->_query($sql);
	}

	/**
	 *
	 */
	public function truncate() {
		return $this->_query('TRUNCATE `' . $this->table . '`');
	}

	/**
	 *
	 */
	public function __isset( $field ) {
		return in_array($field, array_keys($this->fields));
	}

	/**
	 *
	 */
	public function __get( $field ) {
		return $this->get($field);
	}

	/**
	 *
	 */
	public function get( $field ) {
		// Silently ignore invalid fields
		return isset($this->fields[$field]) ? $this->fields[$field] : NULL;
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
	public function getAll() {
		return $this->fields;
	}

	/**
	 *
	 */
	public function __set( $field, $value ) {
		$this->set($field, $value);
	}

	/**
	 *
	 */
	public function set( $field, $value='' ) {
		if (is_array($field) AND func_num_args() == 1) {
			foreach ($field as $key=>$value) $this->set($key, $value);
			return $this;
		}

		// Silently ignore invalid fields
		if (!in_array($field, array_keys($this->fields))) return $this;

		$this->fields[$field] = $value;

		return $this;
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

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	static protected $CACHEDIR;

	/**
	 *
	 */
	protected $db;

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
	protected $oldfields = array();

	/**
	 *
	 */
	protected $fields = array();

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
	protected function reset() {
		foreach ($this->fields as &$value) $value = NULL;
		return $this;
	}

	/**
	 *
	 */
	protected function primaryValues() {
		$values = array();
		foreach ($this->primary as $field) {
			$values[] = $this->fields[$field];
		}
		return $values;
	}

	/**
	 * INSERT / REPLACE wrapper
	 */
	protected function _insert( $mode ) {
		$keys = $values = array();
		foreach ($this->fields as $field=>$value) {
			// Don't insert/replace empty fields
			if ((string) $value != '') {
				$keys[]	 = $field;
				$values[] = $this->db->real_escape_string($value);
			}
		}

		$sql = $mode.' INTO `' . $this->table . '`'
		     . ' (`' . implode('`, `', $keys) . '`) '
		     . 'VALUES'
		     . ' ("' . implode('", "', $values) . '")';

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
	protected function _where( $fields, $values ) {
		$where = array();
		if (!empty($fields)) {
			if (!is_array($fields)) $fields = array($fields);
			if (!is_array($values)) $values = array($values);
			foreach ($fields as $id=>$field) {
				if (!isset($values[$id])) continue;
				$where[] = '`'.$field.'` = '
				         . '"' . $this->db->real_escape_string($values[$id]) .'"';
			}
		}
		return !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
	}

	/**
	 *
	 */
	protected function _order( $order ) {
		if (empty($order)) {
			$order = $this->primary;
		} elseif (!is_array($order)) {
			$order = array($order);
		}
		return !empty($order) ? ' ORDER BY `' . implode('`, `', $order) . '`' : '';
	}

	/**
	 *
	 */
	protected function _limit( $limit ) {
		return $limit ? ' LIMIT '.$limit : '';
	}

	/**
	 *
	 */
	protected function _query( $sql ) {
		$this->sql[] = $sql;
		$res = $this->db->query($sql);
		// You have an error in your SQL syntax; check the manual ...
		if ($this->db->errno == 1149) die($this->db->error . ' : ' . $sql);

		return $res;
	}

}