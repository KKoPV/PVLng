<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Channel {

	/**
	 * Helper function to build an instance
	 */
	public static function byId( $id ) {
		$model = new Model;

		if ($entity = $model->getTreeById($id)) {
			$model = trim('Channel\\' . $entity->model,'\\');
			return new $model($entity->guid);
		}

		throw new \Exception('No channel found for Id '.$id, 400);

	}

	/**
	 * Helper function to build an instance
	 */
	public static function byGUID( $guid ) {
		$model = new Model;

		if ($entity = $model->getTreeByGUID($guid)) {
			$model = trim('Channel\\' . $entity->model,'\\');
			return new $model($guid);
		}

		throw new \Exception('No channel found for GuiD '.$guid, 400);
	}

	/**
	 *
	 */
	public function addChild( $guid ) {
		$childs = $this->getChilds();

		if (count($this->getChilds()) == $this->childs) {
			throw new \Exception('"'.$this->name.'" accepts only '
													.$this->childs . ' child(s) at all!', 400);
		}

		$new = self::byGUID($guid);
		return \Registry::get('ns')->insertChildNode($new->entity, $this->id);
	}

	/**
	 *
	 */
	public function getAttributes( $attribute='' ) {
		$attr = array();

		foreach ($this as $key=>$value) {
			if (is_scalar($value)) $attr[$key] = is_numeric($value) ? +$value : $value;
		}

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
	public function write( $value, $timestamp=NULL ) {
		if (!$this->write)
			throw new \Exception('Can\'t write data to "'.$this->name.'", '
													.'instance of "'.get_class($this).'"!', 400);

		if (!is_scalar($value))
			throw new \Exception('Missing "data" parameter!', 400);

		if ($this->numeric) {
			// make numeric
			$value = +$value;

			if ($this->meter OR $this->threshold > 0) {
				if ($last = $this->getLastReading()) {
					if ($this->meter) {
						// ... check that new value can't be lower than before
						if ($value < $last) $value = $last;
					} elseif ($this->threshold > 0) {
						// ... check that new value is inside the threshold range
						if (abs($value-$last) > $this->threshold) $value = $last;
					}
				}
			}

			// ... check that new value is inside the valid range
			if (!is_null($this->valid_from) AND !is_null($this->valid_to) AND
					($value < $this->valid_from OR $value > $this->valid_to)) {

				$msg = sprintf('Value "%s" outside valid range: %f <= value <= %f',
											 $value, $this->valid_from, $this->valid_to);

				$cfg = new \PVLng\Config('LogInvalid');
				if ($cfg->value != 0) {
					$log = new \PVLng\Log;
					$log->scope = $this->name;
					$log->data = $msg;
					$log->insert();
				}

				throw new \Exception($msg, 200);
			}
		}

		// Default behavior
		$reading = $this->numeric ? new \PVLng\ReadingNum : new \PVLng\ReadingStr;
		$reading->id = $this->entity;
		$reading->timestamp = $timestamp;
		$reading->data = $value;

		return $reading->insert();
	}

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

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

			if ($this->meter) {
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
			   ->get($grouping, 'g')
			   ->group($grouping);
		}

		$q->whereEQ('id', $this->entity)
		   // BETWEEN is  start <= ? <= end  incl. end
		   // subtract 1 second for excluding end
		   ->whereBT('timestamp', $this->start, $this->end-1)
		   ->order('timestamp');

#echo $q;

		$tmpfile = $this->tmpfile();
		$meterabsolute = (isset($request['absolute']) AND $request['absolute']);

		if ($res = $this->db->query($q)) {

			$offset = $last = 0;
			while ($row = $res->fetch_assoc()) {

				$data = $row;
				$data['consumption'] = 0;

				if ($this->meter) {
					if (!$meterabsolute) {
						// calc meter offset for uncompressed data
						if ($offset == 0) $offset = $data['data'];
						$data['data'] -= $offset;
					}
					if ($this->period[1] > 0) {
						$data['consumption'] = $data['max'] - $data['min'];
					} else {
						$data['consumption'] = $data['data'] - $last;
						$last = $data['data'];
					}
				}

				$id = $data['g'];
				unset($data['g']);
				fwrite($tmpfile, $this->encode($data, $id));
			}
		}

		return $this->after_read($tmpfile, $attributes);
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
	 * Grouping SQLs
	 */
	protected $GroupBy = array(
		/* last */         -1 => '',
		/* no grouping */   0 => '',
		/* minute */	    1 => '(`timestamp` - UNIX_TIMESTAMP()) DIV (60 * %d)',
		/* hour */	        2 => '`timestamp` DIV (3600 * %f)',
		/* day */	        3 => '`timestamp` DIV (86400 * %d)',
		/* week */	        4 => 'FROM_UNIXTIME(`timestamp`, "%%x%%v") DIV %d',
		/* month */	        5 => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV %d',
		/* quarter */       6 => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV (3 * %d)',
		/* year */          7 => 'FROM_UNIXTIME(`timestamp`, "%%Y") DIV %d',
	);

	/**
	 *
	 */
	protected function __construct( $guid ) {
		$this->db = yMVC\MySQLi::getInstance();

		$model = new Model;
		foreach ($model->getTreeByGUID($guid) as $key=>$value) {
			$this->$key = $value;
		}

		$this->start = strtotime('00:00');
		$this->end	 = strtotime('24:00');
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
	 * Use PHPs internal temp stream, use file for data greater 5 MB
	 */
	protected function tmpfile() {
	    // 5 MB
		$size = 5 * 1024 * 1024;
	    return fopen('php://temp/maxmemory:'.$size, 'w+');
	}

	/**
	 *
	 */
	protected function before_read( $request ) {
		if (!$this->read)
			throw new \Exception('Can\'t read data from "'.$this->name.'", '
			                    .'instance of "'.get_class($this).'"!', 400);

		if ($this->childs >= 0 AND
				count($this->getChilds()) != $this->childs)
			throw new \Exception('"'.$this->name.'" must have '.$this->childs.' child(s)!', 400);

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
			if (preg_match('~^([.\d]*)(|l|last|i|min|minutes?|h|hours?|d|days?|w|weeks?|m|months?|q|quarters?|y|years?)$~',
			               $request['period'], $args)) {
				$this->period = array($args[1]?:1, '');
				switch (substr($args[2], 0, 2)) {
					case 'l': case 'la':	$this->period[1] = -1;	break;
					case 'i': case 'mi':	$this->period[1] =	1;	break;
					case 'h': case 'ho':	$this->period[1] =	2;	break;
					case 'd': case 'da':	$this->period[1] =	3;	break;
					case 'w': case 'we':	$this->period[1] =	4;	break;
					case 'm': case 'mo':	$this->period[1] =	5;	break;
					case 'q': case 'qa':	$this->period[1] =	6;	break;
					case 'y': case 'ye':	$this->period[1] =	7;	break;
				}
			} else {
				throw new \Exception('Unknown aggregation period: ' . $request['period'], 400);
			}
		}

		$this->full = (array_key_exists('full', $request) AND $request['full']);
	}

	/**
	 *
	 */
	protected function after_read( $tmpfile, $attributes ) {

		$tmpfile2 = $this->tmpfile();

		$last = $consumption = 0;
		$lastrow = '';

		rewind($tmpfile);
		while ($row = fgets($tmpfile)) {
			$this->decode($row, $id);

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
				$row['data'] *= $this->resolution;
				$row['min']  *= $this->resolution;
				$row['max']  *= $this->resolution;
				$row['consumption'] *= $this->resolution;
			}

			$row['data'] = $this->valid($row['data']);

			fwrite($tmpfile2, $this->encode($row, $id));

			$lastrow = $row;
		}

		fclose($tmpfile);

		if ($attributes) {
			$attr = $this->getAttributes();
			$attr['consumption'] = $consumption * $this->resolution;
			$attr['costs'] = $attr['consumption'] * $this->cost;
		}

		if ($this->period[1] == -1) {
			// recreate temp. file with last row only
			fclose($tmpfile2);
			$tmpfile2 = $this->tmpfile();
			fwrite($tmpfile2, $this->encode($lastrow, $id));
		}

		if ($attributes) {
			$tmpfile3 = $this->tmpfile();

			fwrite($tmpfile3, serialize($attr) . PHP_EOL);

			rewind($tmpfile2);
			while ($row = fgets($tmpfile2)) {
				$this->decode($row, $id);
				if (!$this->full) {
					// default result: only timestamp and data
					$row = array(
						'timestamp' => $row['timestamp'],
						'data'      => $row['data'],
					);
				}
				fwrite($tmpfile3, serialize($row) . PHP_EOL);
			}
			fclose($tmpfile2);
			return $tmpfile3;
		}

		return $tmpfile2;
	}

	/**
	 *
	 */
	protected function valid( $data ) {
        if (!is_null($this->valid_from) AND $data < $this->valid_from) {
          return $this->valid_from;
        } elseif (!is_null($this->valid_to) AND $data > $this->valid_to) {
          return $this->valid_to;
        }
		return $data;
	}

	/**
	 *
	 */
	protected function encode( $row, $id ) {
		return $id . "\x00" . serialize($row) . PHP_EOL;
	}

	/**
	 *
	 */
	protected function decode( &$row, &$id ) {
		if ($row == '') {
			$id = '';
			return;
		}

		list($id, $row) = explode("\x00", $row);
		$row = unserialize($row);
	}

	// -------------------------------------------------------------------------
	// PRIVATE
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	private $_childs;

}