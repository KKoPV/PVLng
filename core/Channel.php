<?php
/**
 * Abstract base class for all channels
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
abstract class Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     * GROUP_CHANNEL     - generic group
     */
    const TYPE = UNDEFINED_CHANNEL;

    /**
     * Mark that a channel is used as sub channel for readout
     */
    public $isChild = FALSE;

    /**
     * Helper function to build an instance
     */
    public static function byId( $id ) {
        $channel = new ORM\Tree;
        $channel->find('id', $id);

        if ($channel->alias_of) {
            // Is an alias channel, switch direct to the original channel
            return self::byId($channel->alias_of);
        }

        if ($channel->model) {
            $model = $channel->ModelClass();
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

        if ($channel->alias_of) {
            // Is an alias channel, switch direct to the original channel
            return self::byId($channel->alias_of);
        } elseif ($channel->model) {
            // Channel is in tree
            $model = $channel->ModelClass();
            return new $model($channel);
        }

        throw new Exception('No channel found for GUID: '.$guid, 400);
    }

    /**
     * Helper function to build an instance
     */
    public static function byChannel( $id ) {
        $channel = new ORM\ChannelView($id);

        if ($channel->guid) {
            return self::byGUID($channel->guid);
        }

        throw new Exception('No channel found for ID: '.$guid, 400);
    }

    /**
     * Run additional code before a new channel is presented to the user
     */
    public static function beforeCreate( Array &$fields ) {}

    /**
     * Run additional code before existing data presented to user
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {
        foreach ($fields as $name=>&$data) {
            if ($data['TYPE'] == 'numeric') {
                $data['VALUE'] = str_replace('.', __('DSEP'), $data['VALUE']);
            }
        }
    }

    /**
     *
     * @param $add2tree integer|null
     */
    public static function checkData( Array &$fields, $add2tree ) {
        $ok = TRUE;

        foreach ($fields as $name=>&$data) {
            $data['VALUE'] = trim($data['VALUE']);

            if ($data['VISIBLE']) {
                /* check required fields */
                if ($data['REQUIRED'] AND $data['VALUE'] == '') {
                    $data['ERROR'][] = __('channel::ParamIsRequired');
                    $ok = FALSE;
                }
                /* check numeric fields */
                if ($data['VALUE'] != '') {
                    if ($data['TYPE'] == 'numeric') {
                        $data['VALUE'] = str_replace(__('TSEP'), '', $data['VALUE']);
                        $data['VALUE'] = str_replace(__('DSEP'), '.', $data['VALUE']);
                        if (!is_numeric($data['VALUE'])) {
                            $data['ERROR'][] = __('channel::ParamMustNumeric');
                            $ok = FALSE;
                        }
                    } elseif ($data['TYPE'] == 'integer' AND (string) floor($data['VALUE']) != $data['VALUE']) {
                        $data['ERROR'][] = __('channel::ParamMustInteger');
                        $ok = FALSE;
                    }
                }
            }
        }

        return $ok;
    }

    /**
     * Run additional code before data saved to database
     */
    public static function beforeSave( Array &$fields, \ORM\Channel $channel ) {
        foreach ($fields as $name=>$data) {
            $channel->$name = $data['VALUE'];
        }
    }

    /**
     * Run additional code after channel was created / changed
     * If $tree is set, channel was just created
     */
    public static function afterSave( \ORM\Channel $channel, $tree=NULL ) {}

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
    public function __get( $attribute ) {
        throw new Exception('Unknown attribute: '.$attribute, 400);
    }

    /**
     *
     */
    public function getAttributes( $attribute=NULL ) {

        return $attribute != ''
            // Here WITHOUT check, will be handled by __get()
            ? array($attribute => $this->$attribute)
            : array_merge(
                $this->getAttributesShort(),
                array(
                    'start'       => $this->start,
                    'end'         => $this->end,
                    'consumption' => 0,
                    'costs'       => 0
                )
            );
    }

    /**
     *
     */
    public function getAttributesShort() {
        return array(
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
            'public'      => $this->public,
            'icon'        => $this->icon,
            'comment'     => trim($this->comment)
        );
    }

    /**
     *
     */
    public function write( $request, $timestamp=NULL ) {

        $this->before_write($request);

        // Default behavior
        $reading = ORM\Reading::factory($this->numeric);

        if ($this->numeric) {
            // Check that new value is inside the valid range
            if ((!is_null($this->valid_from) AND $this->value < $this->valid_from) OR
                (!is_null($this->valid_to)   AND $this->value > $this->valid_to)) {

                $msg = sprintf('Value %1$s is outside of valid range (%2$s <= %1$f <= %3$s)',
                               $this->value, $this->valid_from, $this->valid_to);

                $cfg = new ORM\Config('LogInvalid');

                if ($cfg->value != 0) ORM\Log::save($this->name, $msg);

                throw new Exception($msg, 200);
            }

            $lastReading = $reading->getLastReading($this->entity);

            // Check that new reading value is inside the threshold range
            if ($this->threshold > 0 AND abs($this->value-$lastReading) > $this->threshold) {
                // Throw away invalid reading value
                return 0;
            }

            // Check that new meter reading value can't be lower than before
            if ($this->meter AND $lastReading AND $this->value < $lastReading) {
                $this->value = $lastReading;
            }
        }

        // Write performance only for "real" savings if the program flow
        // can to here and not returned earlier
        $this->performance->action = 'write';

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
    public function read( $request ) {

        $logSQL = slimMVC\Config::getInstance()->get('Log.SQL');

        $this->performance->action = 'read';

        $this->before_read($request);

        if ($this->isChild AND $this->period[1] == self::NO) {
            // For channels used as childs set period to at least 1 minute
            $this->period[1] = self::ASCHILD;
        }

        $q = DBQuery::forge($this->table[$this->numeric]);

        $buffer = new Buffer;

        if ($this->period[1] == self::READLAST OR
            // Simply read also last data set for sensor channels
            (!$this->meter AND $this->period[1] == self::LAST)) {

            // Fetch last reading and set some data to 0 to get correct field order
            $q->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
              ->get('timestamp')
              ->get('data')
              ->get(0, 'min')
              ->get(0, 'max')
              ->get(0, 'count')
              ->get(0, 'timediff')
              ->get($this->meter ? 'data' : 0, 'consumption')
              ->whereEQ('id', $this->entity)
              ->orderDescending('timestamp')
              ->limit(1);
            $row = $this->db->queryRow($q);

            if (!$row) return $this->after_read($buffer);

            if ($logSQL) ORM\Log::save('Read data', $this->name . ' (' . $this->description . ")\n\n" . $q);

            // Reset query and read add. data
            $q->select($this->table[$this->numeric])
              ->get($q->MIN('data'), 'min')
              ->get($q->MAX('data'), 'max')
              ->get($q->COUNT('id'), 'count')
              ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff')
              ->whereEQ('id', $this->entity)
              ->limit(1);
            $buffer->write(array_merge((array) $row, (array) $this->db->queryRow($q)));

        } else {

            if ($this->period[1] == self::LAST OR $this->period[1] == self::ALL) {

                $q->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
                  ->get('timestamp')
                  ->get('data')
                  ->get('data', 'min')
                  ->get('data', 'max')
                  ->get(1, 'count')
                  ->get(0, 'timediff')
                  ->get('timestamp', 'g');

            } else {

                $q->get($q->FROM_UNIXTIME($q->MIN('timestamp')), 'datetime')
                  ->get($q->MIN('timestamp'), 'timestamp');

                switch (TRUE) {
                    case !$this->numeric:
                        // Raw data for non-numeric channels
                        $q->get('data');  break;
                    case $this->meter:
                        // Max. value for meters
                        $q->get($q->MAX('data'), 'data');  break;
                    case $this->counter:
                        // Summarize counter ticks
                        $q->get($q->SUM('data'), 'data');  break;
                    default:
                        // Average value of sensors/proxies
                        $q->get($q->AVG('data'), 'data');
                } // switch

                $q->get($q->MIN('data'), 'min')
                  ->get($q->MAX('data'), 'max')
                  ->get($q->COUNT('id'), 'count')
                  ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff')
                  ->get($this->periodGrouping(), 'g')
                  ->group('g');
            }

            if ($this->period[1] != self::ALL) {
                // Time is only relevant for period != ALL
                if ($this->start) {
                    if (!$this->meter) {
                        $q->whereGE('timestamp', $this->start);
                    } else {
                        // Fetch also period before start for correct consumption calculation!
                        $q->whereGE('timestamp', $this->start-$this->TimestampMeterOffset[$this->period[1]]);
                    }
                }
                if ($this->end < time()) {
                    $q->whereLT('timestamp', $this->end);
                }
            }

            $q->whereEQ('id', $this->entity)->order('timestamp');

            if ($res = $this->db->query($q)) {

                if ($this->meter) {
                    if ($this->TimestampMeterOffset[$this->period[1]] > 0) {
                        $row = $res->fetch_assoc();
                        $offset = $row['data'];
                    } else {
                        $offset = 0;
                    }
                    $last = 0;
                }

                while ($row = $res->fetch_assoc()) {

                    $row['consumption'] = 0;

                    if ($this->meter) {

                        if ($offset === 0) {
                            // 1st row, calculate start data
                            $offset = $row['data'];
                        }

                        $row['data'] -= $offset;
                        $row['min']  -= $offset;
                        $row['max']  -= $offset;

                        // calc consumption from previous max value
                        $row['consumption'] = $row['data'] - $last;
                        $last = $row['data'];
                    }

                    // remove grouping value and save
                    $id = $row['g'];
                    unset($row['g']);
                    $buffer->write($row, $id);
                }
            }
        }

        if ($logSQL) ORM\Log::save('Read data', $this->name . ' (' . $this->description . ")\n\n" . $q);

        if (array_key_exists('sql', $request) AND $request['sql']) {
            $sql = $this->name;
            if ($this->description) $sql .= ' (' . $this->description . ')';
            Header('X-SQL-'.substr(md5($sql), 8).': '.$sql.': '.$q);
        }

        return $this->after_read($buffer);
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
    protected $period = array( 0, self::NO );

    /**
     *
     */
    protected $time;

    /**
     * Grouping
     */
    const NO        =  0;
    const ASCHILD   =  1; // Required for grouping by at least 1 minute
    const MINUTE    = 10;
    const HOUR      = 20;
    const DAY       = 30;
    const WEEK      = 40;
    const MONTH     = 50;
    const QUARTER   = 60;
    const YEAR      = 70;
    const LAST      = 80;
    const READLAST  = 81;
    const ALL       = 90;

    /**
     *
     */
    protected $TimestampMeterOffset = array(
        self::NO        =>        0,
        self::ASCHILD   =>        0,
        self::MINUTE    =>       60,
        self::HOUR      =>     3600,
        self::DAY       =>    86400,
        self::WEEK      =>   604800,
        self::MONTH     =>  2678400,
        self::QUARTER   =>  7776000,
        self::YEAR      => 31536000,
        self::LAST      =>        0,
        self::READLAST  =>        0,
        self::ALL       =>        0,
    );

    /**
     *
     */
    protected function __construct( ORM\Tree $channel ) {
        $this->time = microtime(TRUE);
        $this->db = slimMVC\MySQLi::getInstance();
        $this->config = slimMVC\Config::getInstance();

        foreach ($channel->getAll() as $key=>$value) {
            $this->$key = $value;
        }
        $this->extra = json_decode($this->extra);

        $this->performance = new ORM\Performance;
    }

    /**
     *
     */
    protected function periodGrouping() {
        static $GroupBy = array(
            self::NO        => '`timestamp`',
            self::ASCHILD   => '`timestamp` DIV 60',
#            self::MINUTE    => '-((UNIX_TIMESTAMP() - `timestamp`) DIV 60) DIV %d',
            self::MINUTE    => 'FROM_UNIXTIME(`timestamp`, "%%Y%%j%%H%%i") DIV %d',
#            self::HOUR      => '`timestamp` DIV (3600 * %f)',
            self::HOUR      => 'FROM_UNIXTIME(`timestamp`, "%%Y%%j%%k") DIV %d',
            self::DAY       => 'FROM_UNIXTIME(`timestamp`, "%%Y%%j") DIV %d',
            self::WEEK      => 'FROM_UNIXTIME(`timestamp`, "%%x%%v") DIV %d',
            self::MONTH     => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV %d',
            self::QUARTER   => 'FROM_UNIXTIME(`timestamp`, "%%Y%%m") DIV (3 * %d)',
            self::YEAR      => 'FROM_UNIXTIME(`timestamp`, "%%Y") DIV %d',
            self::LAST      => '`timestamp`',
            self::READLAST  => '`timestamp`',
            self::ALL       => '`timestamp`',
        );
        return sprintf($GroupBy[$this->period[1]], $this->period[0]);
    }

    /**
     * Lazy load childs on request
     */
    protected function getChilds() {
        if (is_null($this->_childs)) {
            $this->_childs = array();
            foreach (NestedSet::getInstance()->getChilds($this->id) as $child) {
                $child = self::byID($child['id']);
                $child->isChild = TRUE;
                $this->_childs[] = $child;
            }
        }
        return $this->_childs;
    }

    /**
     * Lazy load child on request, 1 based!
     */
    protected function getChild( $id ) {
        $this->getChilds();
        return ($_=&$this->_childs[$id-1]) ?: FALSE;
    }

    /**
     *
     */
    protected function before_write( $request ) {

        if (!$this->write) {
            throw new \Exception('Can\'t write data to '.$this->name.', '
                                .'instance of '.get_class($this), 400);
        }

        if (!isset($request['data']) OR !is_scalar($request['data'])) {
            throw new Exception('Missing data value', 400);
        }

        // Check if a WRITEMAP::{...} exists to rewrite e.g. from numeric to non-numeric
        if (preg_match('~^WRITEMAP::(.*?)$~m', $this->comment, $args) AND
            $map = json_decode($args[1], TRUE)) {
            $request['data'] = ($_=&$map[$request['data']]) ?: 'unknown ('.$request['data'].')';
        }

        $this->value = $request['data'];

        Hook::process('data.save.before', $this);

        if ($this->numeric) {

            $this->value = +$this->value;

            if ($this->meter) {
                if ($this->value == 0) {
                    throw new Exception('Invalid meter reading: 0', 422);
                }

                $lastReading = ORM\Reading::factory($this->numeric)->getLastReading($this->entity);

                if ($this->meter AND $this->value + $this->offset < $lastReading AND $this->adjust) {
                    // Auto-adjust channel offset
                    ORM\Log::save(
                        $this->name,
                        sprintf("Adjust offset\nLast offset: %f\nLast reading: %f\nValue: %f",
                                $this->offset, $lastReading, $this->value)
                    );

                    // Update channel in database
                    $t = new ORM\Channel($this->entity);
                    $t->offset = $lastReading;
                    $t->update();

                    $this->offset = $lastReading;
                }
            }

            // MUST also work for sensor channels
            // Apply offset
            $this->value += $this->offset;

            if ($this->meter AND $this->value == $lastReading) {
                // Ignore for meters values which are equal last reading
                throw new Exception(NULL, 200);
            }
        }
    }

    /**
     *
     */
    protected function before_read( $request ) {
        // Readable channel?
        if (!$this->read)
            throw new \Exception('Can\'t read data from '.$this->name.', '
                                .'instance of '.get_class($this), 400);

        // Required number of child channels?
        if ($this->childs >= 0 AND count($this->getChilds()) != $this->childs)
            throw new \Exception($this->name.' must have '.$this->childs.' child(s)', 400);

        // Prepare analysis of request
        $request = array_merge(
            array(
                'start'  => '00:00',
                'end'    => '24:00',
                'period' => ''
            ),
            $request
        );

        $latitude  = $this->config->get('Location.Latitude');
        $longitude = $this->config->get('Location.Longitude');

        // Start timestamp
        if ($request['start'] == 'sunrise') {
            if ($latitude == '' OR $longitude == '') {
                throw new \Exception('Invalid start timestamp: "sunrise", missing Location in config/config.php', 400);
            }
            $this->start = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600);
        } else {
            $this->start = is_numeric($request['start'])
                         ? $request['start']
                         : strtotime($request['start']);
        }

        if ($this->start === FALSE)
            throw new \Exception('Invalid start timestamp: '.$request['start'], 400);

        // End timestamp
        if ($request['end'] == 'sunset') {
            if ($latitude == '' OR $longitude == '') {
                throw new \Exception('Invalid start timestamp: "sunrise", missing Location in config/config.php', 400);
            }
            $this->end = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600);
        } else {
            $this->end = is_numeric($request['end'])
                       ? $request['end']
                       : strtotime($request['end']);
        }

        if ($this->end === FALSE)
            throw new \Exception('Invalid end timestamp: '.$request['end'], 400);

        // Consolidation period
        if ($request['period'] != '') {
            // normalize aggr. periods
            if (preg_match('~^([.\d]*)(|l|last|r|readlast|i|min|minutes?|h|hours?|d|days?|w|weeks?|m|months?|q|quarters?|y|years|a|all?)$~',
                           $request['period'], $args)) {
                $this->period = array($args[1]?:1, self::NO);
                switch (substr($args[2], 0, 2)) {
                    case 'i': case 'mi':  $this->period[1] = self::MINUTE;   break;
                    case 'h': case 'ho':  $this->period[1] = self::HOUR;     break;
                    case 'd': case 'da':  $this->period[1] = self::DAY;      break;
                    case 'w': case 'we':  $this->period[1] = self::WEEK;     break;
                    case 'm': case 'mo':  $this->period[1] = self::MONTH;    break;
                    case 'q': case 'qa':  $this->period[1] = self::QUARTER;  break;
                    case 'y': case 'ye':  $this->period[1] = self::YEAR;     break;
                    case 'l': case 'la':  $this->period[1] = self::LAST;     break;
                    case 'r': case 're':  $this->period[1] = self::READLAST; break;
                    case 'a': case 'al':  $this->period[1] = self::ALL;      break;
                }
            } else {
                throw new \Exception('Unknown aggregation period: ' . $request['period'], 400);
            }
        }
    }

    /**
     *
     */
    protected function after_read( Buffer $buffer ) {

        $datafile = new Buffer;

        $last = 0;
        $lastrow = FALSE;

        foreach ($buffer as $id=>$row) {

            if ($this->meter) {
                /* check meter values raising */
                if ($this->resolution > 0 AND $row['data'] < $last OR
                    $this->resolution < 0 AND $row['data'] > $last) {
                    $row['data'] = $last;
                }
                $last = $row['data'];
            }

            if ($this->numeric AND $this->resolution != 1) {
                $row['data']        *= $this->resolution;
                $row['min']         *= $this->resolution;
                $row['max']         *= $this->resolution;
                $row['consumption'] *= $this->resolution;
            }

            if ($this->numeric) {
                // Skip invalid (numeric) rows
                // Apply valid_from and valid_to here ONLY if channel
                // is NOT writable, this will be handled during write()
                if ($this->write OR
                    ((is_null($this->valid_from) OR $row['data'] >= $this->valid_from) AND
                     (is_null($this->valid_to)   OR $row['data'] <= $this->valid_to))) {

                    $this->value = $row['data'];
                    Hook::process('data.read.after', $this);
                    $row['data'] = $this->value;

                    $datafile->write($row, $id);
                    $lastrow = $row;
                }
            } else {
                $this->value = $row['data'];
                Hook::process('data.read.after', $this);
                $row['data'] = $this->value;

                $datafile->write($row, $id);
                $lastrow = $row;
            }
        }
        $buffer->close();

        if ($this->period[1] == self::LAST AND $lastrow) {
            $datafile = new \Buffer;
            $datafile->write($lastrow);
        }

        return $datafile;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $config;

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     *
     */
    private $_childs;

}

/**
 *
 */
define('UNDEFINED_CHANNEL', 0);
define('NUMERIC_CHANNEL',   1);
define('SENSOR_CHANNEL',    2);
define('METER_CHANNEL',     3);
define('GROUP_CHANNEL',     4);
