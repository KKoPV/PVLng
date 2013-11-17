<?php
/**
 * Abstract base class for all channels
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-27-gf2cf3da 2013-05-06 15:24:30 +0200 Knut Kohl $
 */
abstract class Channel {

	/**
	 * Helper function to build an instance
	 */
	public static function byId( $id ) {
		$channel = new ORM\Tree;
		$channel->find('id', $id);

		if ($channel->id != '') {
			$model = trim('Channel\\' . $channel->model,'\\');
			return new $model($channel);
		}

		throw new Exception('No channel found for Id: '.$id, 400);
	}

	/**
	 * Helper function to build an instance
	 */
	public static function byGUID( $guid ) {
		$channel = new ORM\Tree;
		$channel->find('guid', $guid);

		if ($channel->id != '') {
			$model = trim('Channel\\' . $channel->model,'\\');
			return new $model($channel);
		}

		throw new Exception('No channel found for GUID: '.$guid, 400);
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
		return NestedSet::getInstance()->insertChildNode($new->entity, $this->id);
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
	        'valid_from'  => $this->valid_from,
	        'valid_to'    => $this->valid_to,
	        'cost'        => $this->cost,
	        'childs'      => $this->childs,
	        'read'        => $this->read,
	        'write'       => $this->write,
	        'graph'       => $this->graph,
	        'icon'        => $this->icon,
	        'comment'     => $this->comment,
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
			throw new Exception('Missing data value', 400);

		if ($this->numeric) {
			// Make numeric
			$this->value = +$this->value;

			// Check that new value is inside the valid range
			if ((!is_null($this->valid_from) AND $this->value < $this->valid_from) OR
			    (!is_null($this->valid_to)   AND $this->value > $this->valid_to)) {

				$msg = sprintf('Value %1$s is outside of valid range (%2$s <= %1$f <= %3$s)',
				               $this->value, $this->valid_from, $this->valid_to);

				$cfg = new ORM\Config('LogInvalid');

				if ($cfg->value != 0) {
					$log = new ORM\Log;
					$log->scope = $this->name;
					$log->data  = $msg;
					$log->insert();
				}

				throw new Exception($msg, 200);
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
		$reading = $this->numeric ? new ORM\ReadingNum : new ORM\ReadingStr;

		$reading->id        = $this->entity;
		$reading->timestamp = $timestamp;
		$reading->data      = $this->value;

		$rc = $reading->insert();

		if ($rc) Hook::process('data.save.after', $this);

		return $rc;
	}

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->performance->action = 'read';

		$this->before_read($request);

		$q = DBQuery::forge($this->table[$this->numeric]);

		$buffer = new Buffer;

		if (!$this->meter AND $this->period[1] == self::LAST) {
			// Simply read last data set for sensor channels
			$q->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
			  ->get('timestamp')
			  ->get('data')
			  ->get('data', 'min')
			  ->get('data', 'max')
			  ->get(1, 'count', TRUE)
			  ->get(0, 'timediff', TRUE)
			  ->get(0, 'consumption', TRUE)
			  ->whereEQ('id', $this->entity)
			  ->orderDescending('timestamp')
			  ->limit(1);

			$buffer->write((array) $this->db->queryRow($q));

		} else {

			if ($this->period[1] == self::NO OR
			    $this->period[1] == self::LAST OR
			    $this->period[1] == self::ALL) {
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
					$q->get($q->SUM('data'), 'data');
				} else {
					$q->get($q->AVG('data'), 'data');
				}

				$q->get($q->MIN('data'), 'min')
				  ->get($q->MAX('data'), 'max')
				  ->get($q->COUNT('id'), 'count')
				  ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff', TRUE)
				  ->get($grouping, 'g') // Also as row id used!
				  ->group($grouping);
			}

			if ($this->period[1] != self::ALL) {
			    // Time is only relevant for select <> period=all
				$q->whereGE('timestamp', $this->start);
				if ($this->end < time()) {
					$q->whereLT('timestamp', $this->end);
				}
			}

			$q->whereEQ('id', $this->entity)
			  ->order('timestamp');

			if ($res = $this->db->query($q)) {

				$offset = $last = 0;

				while ($row = $res->fetch_assoc()) {

					$row['consumption'] = 0;

					if ($this->meter) {
						// calc meter offset for uncompressed data
						if ($offset === 0) {
							$offset = $row['data'];
						}

						if ($this->db->affected_rows > 1) {
							// ONLY if more than 1 row was returned
							$row['data'] = $row['data'] - $offset;
							$row['min']  = $row['min']  - $offset;
							$row['max']  = $row['max']  - $offset;
						}

						// calc consumption from previous max value
						if ($last == 0) {
							$row['consumption'] = $row['max'] - $row['min'];
						} else {
							$row['consumption'] = $row['max'] - $last;
						}
						$last = $row['max'];
					}
					// remove grouping value
					$id = $row['g'];
					unset($row['g']);

					$buffer->write($row, $id);
				}
			}
		}

		if (array_key_exists('sql', $request) AND $request['sql']) {
			$sql = $this->name;
			if ($this->description) $sql .= ' (' . $this->description . ')';
			$sql .= ': ' . str_replace("\n", ' ', $q);
			Header('X-SQL-' . substr(md5($sql), 25) . ': ' . $sql);
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
	protected $time;

	/**
	 * Grouping
	 */
	const NO      =  0;
	const MINUTE  = 10;
	const HOUR    = 20;
	const DAY     = 30;
	const WEEK    = 40;
	const MONTH   = 50;
	const QUARTER = 60;
	const YEAR    = 70;
	const LAST    = 80;
	const ALL     = 90;

	/**
	 * Grouping SQLs
	 */
	protected $GroupBy = array(
		self::NO      => '',
		self::MINUTE  => 'UNIX_TIMESTAMP() - (UNIX_TIMESTAMP() - `timestamp`) DIV (60 * %d)',
		self::HOUR    => '`timestamp` DIV (3600 * %f)',
		self::DAY     => '`timestamp` DIV (86400 * %d)',
		self::WEEK    => 'FROM_UNIXTIME(`timestamp`, "%%x%%v") DIV %d',
		self::MONTH   => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV %d',
		self::QUARTER => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV (3 * %d)',
		self::YEAR    => 'FROM_UNIXTIME(`timestamp`, "%%Y") DIV %d',
		self::LAST    => '',
		self::ALL     => '`timestamp`',
		self::ALL     => '',
	);

	/**
	 *
	 */
	protected function __construct( ORM\Tree $channel ) {
		$this->time = microtime(TRUE);
		$this->db = slimMVC\MySQLi::getInstance();

		foreach ($channel->getAll() as $key=>$value) {
			$this->$key = $value;
		}

		$this->start = strtotime('00:00');
		$this->end   = strtotime('24:00');

		$this->performance = new ORM\Performance;
	}

	/**
	 *
	 */
	public function __destruct() {
		$time = (microtime(TRUE) - $this->time) * 1000;

		Header(sprintf('X-Query-Time: %d ms', $time));

		// Check for real action to log
		if ($this->performance->action == '') return;

		$this->performance->entity = $this->entity;
		$this->performance->time = $time;
		$this->performance->insert();
	}

	/**
	 * Lazy load childs on request
	 */
	protected function getChilds() {
		if (is_null($this->_childs)) {
			$this->_childs = array();
			foreach (NestedSet::getInstance()->getChilds($this->id) as $child) {
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
		$q = DBQuery::forge($this->table[$this->numeric])
		     ->get('data')
		     ->whereEQ('id', $this->entity)
		     ->orderDescending('timestamp')
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

		Hook::process('data.save.before', $this);
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
					case 'i': case 'mi':  $this->period[1] = self::MINUTE;   break;
					case 'h': case 'ho':  $this->period[1] = self::HOUR;     break;
					case 'd': case 'da':  $this->period[1] = self::DAY;      break;
					case 'w': case 'we':  $this->period[1] = self::WEEK;     break;
					case 'm': case 'mo':  $this->period[1] = self::MONTH;    break;
					case 'q': case 'qa':  $this->period[1] = self::QUARTER;  break;
					case 'y': case 'ye':  $this->period[1] = self::YEAR;     break;
					case 'l': case 'la':  $this->period[1] = self::LAST;     break;
					case 'a': case 'al':  $this->period[1] = self::ALL;      break;
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

		foreach ($buffer as $id=>$row) {

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
			    (is_null($this->valid_to)   OR $row['data'] <= $this->valid_to)) {

				$this->value = +$row['data'];
				Hook::process('data.read.after', $this);
				$row['data'] = $this->value;

				$datafile->write($row, $id);
				$lastrow = $row;
			}
		}
		$buffer->close();

		if ($lastrow AND $this->period[1] == self::LAST) {
			// recreate temp. file with last row only
			$datafile->close();
			$datafile = new Buffer;
			$datafile->write($lastrow);
		}

		if (!$attributes) return $datafile;

		// -------------------------------------------------------------------
		// Mostly last call, return attributes and data
		$buffer = new Buffer;

		$attr = $this->getAttributes();
		$attr['consumption'] = round($consumption * $this->resolution, $this->decimals);
		$dec = slimMVC\Config::getInstance()->get('Currency.Decimals');
		$attr['costs'] = round($attr['consumption'] * $this->cost, $dec);

		$buffer->write($attr);

		// Bitmask : 00000011
		//                  ^----- Full
		//                 ^------ Mobile
		$mode = 0;
		if ($this->full)   $mode |= 1;
		if ($this->mobile) $mode |= 2;

		// optimized flow, switch before loop, not switch inside loop...
		switch ($mode) {
			// -------------------
			case 3: // Full mobile

				foreach ($datafile as $row) {
					$row = array_values($row);
					$row = array_map(function($n){ return round($n, $this->decimals); }, $row);
					$buffer->write($row);
				}
				break;

			// -------------------
			case 2: // Short mobile

				foreach ($datafile as $row) {
					// default mobile result: only timestamp and data
					$buffer->write(array(
						/* 0 */ round($row['timestamp'], $this->decimals),
						/* 1 */ round($row['data'], $this->decimals)
					));

				}
				break;

			// -------------------
			case 1: // Full

				// do nothing with $row
				foreach ($datafile as $row) {
					$row = array_map(function($n){ return round($n, $this->decimals); }, $row);
					$buffer->write($row);
				}
				break;

			// -------------------
			default: // Short, default

				foreach ($datafile as $row) {
					// default result: only timestamp and data
					$buffer->write(array(
						'timestamp' => round($row['timestamp'], $this->decimals),
						'data'      => round($row['data'], $this->decimals)
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
