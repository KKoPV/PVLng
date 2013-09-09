<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-27-gf2cf3da 2013-05-06 15:24:30 +0200 Knut Kohl $
 */
class Channel {

	/**
	 * Helper function to build an instance
	 */
	public static function byId( $id ) {
		$model = new Model;

		if ($entity = $model->getTreeById($id)) {
			$model = trim('Channel\\' . $entity->model,'\\');
			return new $model($id);
		}

		throw new \Exception('No channel found for Id: '.$id, 400);
	}

	/**
	 * Helper function to build an instance
	 */
	public static function byGUID( $guid ) {
		$model = new Model;

		if ($entity = $model->getTreeByGUID($guid)) {
			$model = trim('Channel\\' . $entity->model,'\\');
			return new $model($entity->id);
		}

		throw new \Exception('No channel found for GUID: '.$guid, 400);
	}

	/**
	 *
	 */
	public function addChild( $guid ) {
		$childs = $this->getChilds();

		if (count($this->getChilds()) == $this->childs) {
			throw new \Exception($this->name.' accepts only '
			                    .$this->childs . ' child(s) at all', 400);
		}

		$new = self::byGUID($guid);
		return \Registry::get('ns')->insertChildNode($new->entity, $this->id);
	}

	/**
	 *
	 */
	public function getAttributes( $attribute='' ) {
		$attr = array(
	        'guid'        => $this->guid,
	        'name'        => $this->name,
	        'serial'      => $this->serial,
	        'channel'     => $this->channel,
	        'description' => $this->description,
	        'type'        => $this->type,
	        'unit'        => $this->unit,
	        'decimals'    => $this->decimals,
	        'numeric'     => $this->numeric,
	        'meter'       => $this->meter,
	        'resolution'  => $this->resolution,
	        'threshold'   => $this->threshold,
	        'cost'        => $this->cost,
	        'childs'      => $this->childs,
	        'read'        => $this->read,
	        'write'       => $this->write,
	        'graph'       => $this->graph,
	        'icon'        => $this->icon,
	        'start'       => $this->start,
	        'end'         => $this->end,
			'consumption' => 0,
			'costs'       => 0
		);

		return ($attribute == '' OR $attribute == '*')
		     ? $attr
		     : ( isset($attr[$attribute])
		       ? array($attribute => $attr[$attribute])
		       : ''
		     );
	}

	/**
	 *
	 */
	public function write( $request, $timestamp=NULL ) {

		$this->before_write($request);

		if (is_null($this->value) OR !is_scalar($this->value))
			throw new \Exception('Missing data value', 400);

		if ($this->numeric) {
			// Make numeric
			$this->value = +$this->value;

			// Check that new value is inside the valid range
			if ((!is_null($this->valid_from) AND $this->value < $this->valid_from) OR
			    (!is_null($this->valid_to)   AND $this->value > $this->valid_to)) {

				$msg = sprintf('Value %1$s is outside of valid range (%2$s <= %1$f <= %3$s)',
				               $this->value, $this->valid_from, $this->valid_to);

				$cfg = new \PVLng\Config('LogInvalid');

				if ($cfg->value != 0) {
					$log = new \PVLng\Log;
					$log->scope = $this->name;
					$log->data  = $msg;
					$log->insert();
				}

				throw new \Exception($msg, 200);
			}

			$last = $this->getLastReading();

			// Check that new reading value is inside the threshold range
			if ($this->threshold > 0 AND abs($this->value-$last) > $this->threshold) {
				// Throw away invalid reading value
				return 0;
			}

			// Check that new meter reading value can't be lower than before
			if ($this->meter AND $last AND $this->value < $last) {
				$this->value = $last;
			}
		}

		// Write performance only for "real" savings if the program flow
		// can to here and not returned earlier
		$this->performance->action = 'write';

		// Default behavior
		$reading = $this->numeric ? new \PVLng\ReadingNum : new \PVLng\ReadingStr;

		$reading->id        = $this->entity;
		$reading->timestamp = $timestamp;
		$reading->data      = $this->value;

		$rc = $reading->insert();

		if ($rc) Hook::process('data_save_after', $this);

		return $rc;
	}

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->performance->action = 'read';

		$this->before_read($request);

		$q = new \DBQuery($this->table[$this->numeric]);

		if ($this->period[1] <= 0) {
			// Default behavior

			$q->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
			   ->get('timestamp')
			   ->get('data')
			   ->get('data', 'min')
			   ->get('data', 'max')
			   ->get('1', 'count', TRUE)
			   ->get('0', 'timediff', TRUE)
			   ->get('timestamp', 'g');

		} else {
			// with period
			$grouping = sprintf($this->GroupBy[$this->period[1]], $this->period[0]);

			$q->get($q->FROM_UNIXTIME($q->MIN('timestamp')), 'datetime')
			  ->get($q->MIN('timestamp'), 'timestamp');

			if (!$this->numeric) {
				$q->get('data');
			} elseif ($this->meter) {
				$q->get($q->MAX('data'), 'data');
			} elseif ($this->counter) {
				$q->get($q->ROUND($q->SUM('data'), 4), 'data');
			} else {
				$q->get($q->ROUND($q->AVG('data'), 4), 'data');
			}

			$q->get($q->MIN('data'), 'min')
			  ->get($q->MAX('data'), 'max')
			  ->get($q->COUNT('id'), 'count')
			  ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff', TRUE)
			  ->get($grouping, 'g') // Also as row id used!
			  ->group($grouping);
		}

		$q->whereEQ('id', $this->entity);

		if ($this->period[1] != 8) {
		    // Time is only relevant for select <> period=all
		    // BETWEEN is  start <= ? <= end  incl. end!
		    // Subtract 1 second for excluding end!
 			$q->whereBT('timestamp', $this->start, $this->end-1)
			  ->order('timestamp');
		}

 		$buffer = new Buffer;

		if (array_key_exists('sql', $request) AND $request['sql']) $this->sql = (string) $q;

		if ($res = $this->db->query($q)) {

			$offset = $last = 0;

			while ($row = $res->fetch_assoc()) {

				$this->value = +$row['data'];
				$row['data'] = Hook::process('data_read_after', $this);

				$row['consumption'] = 0;

				if ($this->meter) {
					// calc meter offset for uncompressed data
					if ($offset == 0) {
						$offset = $row['data'];
					}

					if ($res->num_rows > 1) {
						$row['data'] = round($row['data'] - $offset, 4);
						$row['min']  = round($row['min'] - $offset, 4);
						$row['max']  = round($row['max'] - $offset, 4);
					}

					// calc consumption from previous max value
					if ($last == 0) {
						$row['consumption'] = round($row['max'] - $row['min'], 4);
					} else {
						$row['consumption'] = round($row['max'] - $last, 4);
					}
					$last = $row['max'];
				}
				// remove grouping value
				$id = $row['g'];
				unset($row['g']);

				$buffer->write($row, $id);
			}
		}

		return $this->after_read($buffer, $attributes);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $db;

	/**
	 *
	 */
	protected $table = array(
		'pvlng_reading_str', // numeric == 0
		'pvlng_reading_num', // numeric == 1
	);

	/**
	 *
	 */
	protected $counter = 0;

	/**
	 *
	 */
	protected $start;

	/**
	 *
	 */
	protected $end;

	/**
	 *
	 */
	protected $period = array( 1, 0 );

	/**
	 *
	 */
	protected $full;

	/**
	 *
	 */
	protected $mobile;

	/**
	 *
	 */
	protected $sql;

	/**
	 *
	 */
	protected $time;

	/**
	 * Grouping SQLs
	 */
	protected $GroupBy = array(
		/* last */         -1 => '',
		/* no grouping */   0 => '',
		/* minute */	    1 => '(CAST(`timestamp` AS SIGNED) - UNIX_TIMESTAMP()) DIV (60 * %d)',
		/* hour */	        2 => '`timestamp` DIV (3600 * %f)',
		/* day */	        3 => '`timestamp` DIV (86400 * %d)',
		/* week */	        4 => 'FROM_UNIXTIME(`timestamp`, "%%x%%v") DIV %d',
		/* month */	        5 => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV %d',
		/* quarter */       6 => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV (3 * %d)',
		/* year */          7 => 'FROM_UNIXTIME(`timestamp`, "%%Y") DIV %d',
		/* all */           8 => '""',
	);

	/**
	 *
	 */
	protected function __construct( $id ) {
		$this->time = microtime(TRUE);
		$this->db = yMVC\MySQLi::getInstance();

		$model = new Model;
		foreach ($model->getTreeById($id) as $key=>$value) {
			$this->$key = $value;
		}

		$this->start = strtotime('00:00');
		$this->end   = strtotime('24:00');

		$this->performance = new \PVLng\Performance;
	}

	/**
	 *
	 */
	public function __destruct() {
		// Check for real action to log
		if ($this->performance->action == '') return;

		$time = (microtime(TRUE) - $this->time) * 1000;

		$this->performance->entity = $this->entity;
		$this->performance->time = $time;
		$this->performance->insert();

		Header(sprintf('X-Query-Time: %d ms', $time));
	}

	/**
	 * Lazy load childs on request
	 */
	protected function getChilds() {
		if (is_null($this->_childs)) {
			$this->_childs = array();
			foreach (\Registry::get('ns')->getChilds($this->id) as $child) {
				$this->_childs[] = self::byID($child['id']);
			}
		}
		return $this->_childs;
	}

	/**
	 * Lazy load child on request, 1 based!
	 */
	protected function getChild( $id ) {
		$this->getChilds();
		return isset($this->_childs[$id-1]) ? $this->_childs[$id-1] : FALSE;
	}

	/**
	 *
	 */
	protected function getLastReading() {
		$q = new \DBQuery($this->table[$this->numeric]);
		$q->get('data')
		  ->whereEQ('id', $this->entity)
		  ->order('timestamp', FALSE)
		  ->limit(1);

		return $this->db->queryOne($q);
	}

	/**
	 *
	 */
	protected function before_write( $request ) {

		if (!$this->write)
			throw new \Exception('Can\'t write data to '.$this->name.', '
			                    .'instance of '.get_class($this), 400);

		$this->value = isset($request['data']) ? $request['data'] : NULL;

		$this->value = Hook::process('data_save_before', $this);
	}

	/**
	 *
	 */
	protected function before_read( $request ) {
		if (!$this->read)
			throw new \Exception('Can\'t read data from '.$this->name.', '
			                    .'instance of '.get_class($this), 400);

		if ($this->childs >= 0 AND
		    count($this->getChilds()) != $this->childs)
			throw new \Exception($this->name.' must have '.$this->childs.' child(s)', 400);

		if (isset($request['start'])) {
			$this->start = is_numeric($request['start'])
			             ? $request['start']
			             : strtotime($request['start']);
			if ($this->start === FALSE)
				throw new \Exception('No valid start timestamp: '.$this->start, 400);
		}

		if (isset($request['end'])) {
			$this->end = is_numeric($request['end'])
			           ? $request['end']
			           : strtotime($request['end']);
			if ($this->end === FALSE)
				throw new \Exception('No valid end timestamp: '.$this->end, 400);
		}

		if (isset($request['period'])) {
			// normalize aggr. periods
			if (preg_match('~^([.\d]*)(|l|last|i|min|minutes?|h|hours?|d|days?|w|weeks?|m|months?|q|quarters?|y|years|a|all?)$~',
			               $request['period'], $args)) {
				$this->period = array($args[1]?:1, '');
				switch (substr($args[2], 0, 2)) {
					case 'l': case 'la':  $this->period[1] = -1;  break;
					case 'i': case 'mi':  $this->period[1] =  1;  break;
					case 'h': case 'ho':  $this->period[1] =  2;  break;
					case 'd': case 'da':  $this->period[1] =  3;  break;
					case 'w': case 'we':  $this->period[1] =  4;  break;
					case 'm': case 'mo':  $this->period[1] =  5;  break;
					case 'q': case 'qa':  $this->period[1] =  6;  break;
					case 'y': case 'ye':  $this->period[1] =  7;  break;
					case 'a': case 'al':  $this->period[1] =  8;  break;
				}
			} else {
				throw new \Exception('Unknown aggregation period: ' . $request['period'], 400);
			}
		}

		$this->full   = (array_key_exists('full', $request) OR
		                 array_search('full', $request) !== FALSE);
		$this->mobile = (array_key_exists('mobile', $request) OR
		                 array_search('short', $request) !== FALSE);
	}

	/**
	 *
	 */
	protected function after_read( Buffer $buffer, $attributes ) {

		$datafile = new Buffer;

		$last = $consumption = 0;
		$lastrow = FALSE;

		$buffer->rewind();

		while ($buffer->read($row, $id)) {

			if ($this->meter) {
				/* check meter values raising */
				if ($this->resolution > 0 AND $row['data'] < $last OR
				    $this->resolution < 0 AND $row['data'] > $last) {
					$row['data'] = $last;
				}
				$consumption += $row['consumption'];
				$last = $row['data'];
			}
			if ($this->numeric AND $this->resolution != 1) {
				$row['data']        *= $this->resolution;
				$row['min']         *= $this->resolution;
				$row['max']         *= $this->resolution;
				$row['consumption'] *= $this->resolution;
			}

			// Skip invalid rows
			if ((is_null($this->valid_from) OR $row['data'] >= $this->valid_from) AND
			    (is_null($this->valid_to) OR $row['data'] <= $this->valid_to)) {
				$datafile->write($row, $id);
				$lastrow = $row;
			}
		}
		$buffer->close();

		if ($lastrow AND $this->period[1] == -1 /* last */) {
			// recreate temp. file with last row only
			$datafile->close();
			$datafile = new Buffer;
			$datafile->write($lastrow, 0);
		}

		if (!$attributes) return $datafile;

		// -------------------------------------------------------------------
		// Mostly last call, return attributes and data
		$buffer = new Buffer;

		$attr = $this->getAttributes();
		$attr['consumption'] = $consumption * $this->resolution;
		$attr['costs'] = $attr['consumption'] * $this->cost;
		// remover newlines, they will not correct serialized...
		if ($this->sql != '') $attr['sql'] = preg_replace('~\s+~s', ' ', $this->sql);

		$buffer->swrite($attr);

		// Bitmask : 00000011
		//                  ^----- Full
		//                 ^------ Mobile
		$mode = 0;
		if ($this->full)   $mode |= 1;
		if ($this->mobile) $mode |= 2;

		$datafile->rewind();

		// optimized flow...
		switch ($mode) {
			// -------------------
			case 3: // Full mobile

				while ($datafile->read($row, $id)) {
					$buffer->swrite(array_values($row));
				}
				break;

			// -------------------
			case 2: // Short mobile

				while ($datafile->read($row, $id)) {
					// default mobile result: only timestamp and data
					$buffer->swrite(array(
						/* 0 */ $row['timestamp'],
						/* 1 */ $row['data']
					));

				}
				break;

			// -------------------
			case 1: // Full
				// do nothing with $row
				while ($datafile->read($row, $id)) {
					$buffer->swrite($row);
				}
				break;

			// -------------------
			default: // Short, default
				while ($datafile->read($row, $id)) {
					// default result: only timestamp and data
					$buffer->swrite(array(
						'timestamp' => $row['timestamp'],
						'data'      => $row['data']
					));
				}
				break;

		}
		$datafile->close();

		return $buffer;
	}

	// -------------------------------------------------------------------------
	// PRIVATE
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	private $_childs;

}
