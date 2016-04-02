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

    public static $Database;

    /**
     * Mark that a channel is used as sub channel for readout
     */
    public $isChild = FALSE;

    /**
     *
     */
    public static function setCache( Cache $cache ) {
        self::$cache = $cache;
    }

    /**
     * Helper function to build an instance
     */
    public static function byId( $id, $alias=TRUE ) {
        $channel = new ORM\Tree($id);

        if (!$channel->getId()) {
            throw new Exception('No channel found for Id: '.$id, 400);
        }

        $aliasOf = $channel->getAliasOf();

        if ($aliasOf AND $alias) {
            // Is an alias channel, switch direct to the original channel
            return self::byId($aliasOf);
        }

        $model = $channel->getModelClass();
        return new $model($channel);
    }

    /**
     * Helper function to build an instance
     */
    public static function byGUID( $guid, $alias=TRUE ) {
        if ($guid == '') {
            throw new Exception('Missing channel GUID!');
        }

        $channel = new ORM\Tree;
        $channel->filterRaw('`guid` like "'.$guid.'%"')->findOne();
        $aliasOf = $channel->getAliasOf();

        if ($aliasOf AND $alias) {
            // Is an alias channel, switch direct to the original channel
            return self::byId($aliasOf);
        } elseif ($channel->getModel()) {
            // Channel is in tree
            $model = $channel->getModelClass();
            return new $model($channel);
        } else {
            // NOT in tree, may be a real writable channel?! "Fake" a tree entry
            $c = new ORM\ChannelView;
            $c->filterByGuid($guid)->findOne();
            if ($c->getId() AND $c->getWrite()) {
                $data = $c->asAssoc();
                $data['id'] = 0;
                $data['entity'] = $c->getId();
                $channel->set($data);
                $model = $c->getModelClass();
                return new $model($channel);
            }
        }

        throw new Exception('No channel found for GUID: '.$guid, 400);
    }

    /**
     * Helper function to build an instance
     */
    public static function byChannel( $id, $alias=TRUE ) {
        $channel = new ORM\ChannelView($id);

        if ($channel->getGuid()) {
            return self::byGUID($channel->getGuid(), $alias);
        }

        throw new Exception('No channel found for ID: '.$id, 400);
    }

    /**
     * Run additional code before a new channel is presented to the user
     */
    public static function beforeCreate( Array &$fields ) {}

    /**
     * Run additional code before existing data presented to user
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {}

    /**
     * Run additional code after attributes was maintained by user
     *
     * @param $add2tree integer|null
     */
    public static function checkData( Array &$fields, $add2tree ) {
        $ok = TRUE;

        foreach ($fields as $name=>&$data) {
            // Don't check invisible fields
            if (!$data['VISIBLE']) continue;

            $data['VALUE'] = trim($data['VALUE']);

            if ($data['VALUE'] == '') {
                // Check required fields
                if ($data['REQUIRED']) {
                    $data['ERROR'][] = __('channel::ParamIsRequired');
                    $ok = FALSE;
                }
                // No further checks for empty fields required
                continue;
            }

            // Check numeric fields
            switch ($data['TYPE']) {
                case 'numeric':
                    if (!is_numeric($data['VALUE'])) {
                        $data['ERROR'][] = __('channel::ParamMustNumeric');
                        $ok = FALSE;
                    }
                    break;
                case 'integer':
                    if ((string) floor($data['VALUE']) != $data['VALUE']) {
                        $data['ERROR'][] = __('channel::ParamMustInteger');
                        $ok = FALSE;
                    }
                    break;
            } // switch
        }

        return $ok;
    }

    /**
     * Run additional code before data saved to database
     */
    public static function beforeSave( Array &$fields, \ORM\Channel $channel ) {
        foreach ($fields as $name=>$data) {
            $channel->set($name, $data['VALUE']);
        }
    }

    /**
     * Run additional code before channel will be added to hierarchy
     * Return FALSE to skip!
     */
    public static function beforeAdd2Tree( $parent ) {}

    /**
     * Run additional code after channel was created / changed
     * If $tree is set, channel was just created
     */
    public static function afterSave( \ORM\Channel $channel, $tree=NULL ) {}

    /**
     *
     */
    public function addChild( $channel ) {
        $childs = $this->getChilds(TRUE);

        // Root node (id == 1) accept always childs
        if ($this->id == 1 OR $this->childs == -1 OR count($childs) < $this->childs) {
            $c = new ORM\ChannelView($channel);
            $model = $c->getModelClass();
            if ($model::beforeAdd2Tree($this) !== FALSE) {
                return NestedSet::getInstance()->insertChildNode($channel, $this->id);
            }
        } else {
            Messages::Error(__('AcceptChild', $this->childs, $this->name), 400);
        }
        return FALSE;
    }

    /**
     *
     */
    public function removeFromTree() {
        return NestedSet::getInstance()->DeleteBranch($this->id);
    }

    /**
     * Capture not defined attributes
     */
    public function __get( $attribute ) {
        throw new Exception('Unknown attribute: '.$attribute, 400);
    }

    /**
     *
     */
    public function getAttributes( $attribute=NULL ) {
        if ($attribute != '') {
            // Accept attribute name 'factor' for resolution
            // Here WITHOUT check, will be handled by __get()
            return array($attribute => $attribute == 'factor' ? $this->resolution : $this->$attribute);
        } else {
            return array_merge(
                $this->getAttributesShort(),
                array(
                    'start'       => $this->start,
                    'end'         => $this->end,
                    'consumption' => 0,
                    'costs'       => 0
                ),
                $this->attributes
            );
        }
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
            'extra'       => is_array($this->extra) ? implode("\n", $this->extra) : $this->extra,
            'comment'     => trim($this->comment)
        );
    }

    /**
     *
     */
    public function write( $request, $timestamp=NULL ) {

        // Default behavior
        $reading = ORM\Reading::factory($this->numeric);

        $this->lastReading = $reading->getLastReading($this->entity, $timestamp);

        $this->before_write($request);

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

            // Check that new reading value is inside the threshold range,
            // except 1st reading at all ($this->lastreading == NULL)
            if ($this->threshold > 0 AND !is_null($this->lastReading) AND
                abs($this->value-$this->lastReading) > $this->threshold) {
                // Throw away invalid reading value
                throw new Exception('Ignore invalid reading value: '.$this->value, 200);
            }

            // Check that new meter reading value can't be lower than before
            if ($this->meter AND $this->lastReading AND $this->value < $this->lastReading) {
                $this->value = $this->lastReading;
            }
        }

        // Write performance only for "real" savings if the program flow
        // came to here and not returned earlier
        $this->performance->setAction('write');

        $rc = $reading->setId($this->entity)->setTimestamp($timestamp)->setData($this->value)->insert();

        if ($rc == 0 AND $timestamp > time()) {
            $rc = $this->update($request, $timestamp);
        }

        if ($rc) Hook::process('data.save.after', $this);

        return $rc;
    }

    /**
     *
     */
    public function update( $request, $timestamp ) {

        // Default behavior
        $reading = ORM\Reading::factory($this->numeric);

        $this->lastReading = $reading->getLastReading($this->entity, $timestamp);

        $this->check_before_write($request, $timestamp);

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

            // Check that new reading value is inside the threshold range,
            // except 1st reading at all ($this->lastreading == NULL)
            if ($this->threshold > 0 AND !is_null($this->lastReading) AND
                abs($this->value-$this->lastReading) > $this->threshold) {
                // Throw away invalid reading value
                return 0;
            }

            // Check that new meter reading value can't be lower than before
            if ($this->meter AND $this->lastReading AND $this->value < $this->lastReading) {
                return 0;
            }
        }

        // Write performance only for "real" savings if the program flow
        // can to here and not returned earlier
        $this->performance->setAction('update');

        $reading->filterByIdTimestamp($this->entity, $timestamp)->findOne();
        $rc = $reading->getId() ? $reading->setData($this->value)->update() : 0;
/*
        if ($rc) {
            // Log successful updates only
            $msg = isset($this->lastReading)
                 ? sprintf('%s: %f > %f', date('Y-m-d H:i:s', $timestamp), $this->lastReading, $request['data'])
                 : sprintf('%s: %f', date('Y-m-d H:i:s', $timestamp), $request['data']);
            ORM\Log::save($this->name, $msg);
        }
*/
        if ($rc) Hook::process('data.update.after', $this);

        return $rc;
    }

    /**
     *
     */
    public function read( $request ) {

        $this->performance->setAction('read');

        $this->before_read($request);

        $q = DBQuery::forge($this->table[$this->numeric]);

        $buffer = new Buffer;

        if ($this->period[1] == self::READLAST) {

            // Use special table for last readings
            $q = DBQuery::forge('pvlng_reading_last');

            // Fetch last reading and set some data to 0 to get correct field order
            $q->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
              ->get('timestamp')
              ->get('data')
              ->get(0, 'min')
              ->get(0, 'max')
              ->get(0, 'count')
              ->get(0, 'timediff')
              ->get(0, 'consumption')
              ->filter('id', $this->entity)
              ->limit(1);

            $row = $this->db->queryRow($q);

            if (!$row) return $this->after_read($buffer);

            $buffer->write((array) $row);

        } elseif (!$this->meter AND $this->period[1] == self::LAST) {

            // Simply read last data set for sensor channels

            // Fetch last reading and set some data to 0 to get correct field order
            $q->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
              ->get('timestamp')
              ->get('data')
              ->get($q->MIN('data'), 'min')
              ->get($q->MAX('data'), 'max')
              ->get($q->COUNT('id'), 'count')
              ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff')
              ->get(0, 'consumption')
              ->filter('id', $this->entity)
              ->order('timestamp', TRUE)
              ->limit(1);

            $this->filterReadTimestamp($q);

            $row = $this->db->queryRow($q);

            if (!$row) return $this->after_read($buffer);

            $buffer->write((array) $row);

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
                    // Raw data for non-numeric channels
                    case !$this->numeric:
                        $q->get('data');
                        break;
                    // Max./Min. value for meters
                    case $this->meter:
                        $d = ($this->resolution > 0) ? $q->MAX('data') : $q->MIN('data');
                        $q->get($d, 'data');
                        break;
                    // Summarize counter ticks
                    case $this->counter:
                        $q->get($q->SUM('data'), 'data');
                        break;
                    // Average value of sensors/proxies
                    default:
                        $q->get($q->AVG('data'), 'data');
                } // switch

                $q->get($q->MIN('data'), 'min')
                  ->get($q->MAX('data'), 'max')
                  ->get($q->COUNT('id'), 'count')
                  ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff')
                  ->get($this->periodGrouping(), 'g')
                  ->group('g');
            }

            $this->filterReadTimestamp($q);
            $q->filter('id', $this->entity)->order('timestamp');

            // Use bufferd result set
            $this->db->setBuffered();
            $last = 0;

            if ($res = $this->db->query($q)) {

                if ($this->meter && ($first = $res->fetch_assoc())) {
                    $offset = $first['data'];
                } else {
                    $first = true;
                }

                while ($first && ($row = $res->fetch_assoc())) {

                    if ($this->meter) {
                        $row['data'] -= $offset;
                        $row['min']  -= $offset;
                        $row['max']  -= $offset;
                        // calc consumption from previous max value
                        $row['consumption'] = $row['data'] - $last;
                        $last = $row['data'];
                    } else {
                        $row['consumption'] = 0;
                    }

                    // remove grouping value and save
                    $id = $row['g'];
                    unset($row['g']);
                    $buffer->write($row, $id);
                }

                // Don't forget to close for buffered results!
                $res->close();
            }

            $this->db->setBuffered(FALSE);
        }

        $this->SQLHeader($request, $q);

        return $this->after_read($buffer);
    }

    /**
     *
     */
    public function getTag( $tag ) {
        $tag = strtolower($tag);
        return array_key_exists($tag, $this->_tags)
             ? $this->_tags[$tag]
             : NULL;
    }

    /**
     *
     */
    public function __destruct() {
        $time = (microtime(TRUE) - $this->time) * 1000;

        if (!headers_sent()) Header(sprintf('X-Query-Time: %d ms', $time));

        // Check for real action to log
        if ($this->performance->getAction()) {
            $this->performance->setTime($time)->insert();
        }
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected static $cache;

    /**
     *
     */
    protected $db;

    /**
     *
     */
    protected $config;

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
     * Extra attributes
     */
    protected $attributes = array();

    /**
     *
     */
    protected $_tags = array();

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
    protected $GroupingPeriod = array(
        self::NO        =>        0,
        self::ASCHILD   =>       60,
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
        $this->time   = microtime(TRUE);
        $this->db     = $channel::getDatabase();

        foreach ($channel->asAssoc() as $key=>$value) {
            $this->$key = $value;
        }

        foreach (explode("\n", $this->tags) as $tag) {
            list($scope, $value) = explode(':', $tag.':');
            $scope = preg_replace('~\s+~', ' ', trim($scope));
            if ($scope) $this->_tags[strtolower($scope)] = trim($value);
        }

        $this->performance = new ORM\Performance;
    }

    /**
     *
     */
    protected function periodGrouping() {
        static $GroupBy = array(
            self::NO        => '`timestamp`',
            self::ASCHILD   => '`timestamp` DIV 60',
            self::MINUTE    => '`timestamp` DIV (60 * %d)',
            self::HOUR      => 'FROM_UNIXTIME(`timestamp`, "%%Y%%j%%H") DIV %d',
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
    protected function getChilds( $refresh=FALSE ) {
        if ($refresh OR is_null($this->_childs)) {
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
     * Essential checks before write data
     */
    protected function check_before_write( &$request ) {

        if (!$this->write) {
            throw new \Exception(
                'Can\'t write data to '.$this->name.', instance of '.get_class($this),
                400
            );
        }

        if (!isset($request['data']) OR !is_scalar($request['data'])) {
            throw new Exception($this->guid.' - Missing data value', 400);
        }

        // Check if a WRITEMAP::{...} exists to rewrite e.g. from numeric to non-numeric
        if (preg_match('~^WRITEMAP::(.*?)$~m', $this->tags, $args) &&
            ($map = json_decode($args[1], true))) {
            $request['data'] = $this->array_value($map, $request['data'], 'unknown ('.$request['data'].')');
        } elseif (preg_match('~^WRITEMAP::(.*?)$~m', $this->comment, $args) &&
            ($map = json_decode($args[1], true))) {
            $request['data'] = $this->array_value($map, $request['data'], 'unknown ('.$request['data'].')');
        }

        $this->value = $request['data'];

    }

    /**
     *
     */
    protected function before_write( &$request ) {

        $this->check_before_write($request);

        Hook::process('data.save.before', $this);

        if ($this->numeric) {
            // Remove all non-numeric characters
            $this->value = preg_replace('~[^0-9.eE-]~', '', $this->value);

            // Interpret empty numeric value as invalid and ignore them
            if ($this->value == '') throw new Exception(NULL, 200);

            $this->value = +$this->value;

            if ($this->meter) {
                if ($this->value == 0) {
                    throw new Exception('Invalid meter reading: 0', 422);
                }

                if ($this->meter AND $this->value + $this->offset < $this->lastReading AND $this->adjust) {
                    // Auto-adjust channel offset
                    ORM\Log::save(
                        $this->name,
                        sprintf("Adjust offset\nLast offset: %f\nLast reading: %f\nValue: %f",
                                $this->offset, $this->lastReading, $this->value)
                    );

                    // Update channel in database
                    $t = new ORM\Channel($this->entity);
                    $t->offset = $this->lastReading;
                    $t->update();

                    $this->offset = $this->lastReading;
                }
            }

            // MUST also work for sensor channels
            // Apply offset
            $this->value += $this->offset;

            if ($this->meter AND $this->value == $this->lastReading) {
                throw new Exception('Ignore meter values which are equal last reading', 200);
            }
        }
    }

    /**
     *
     */
    protected function before_read( &$request ) {
        // Readable channel?
        if (!$this->read)
            throw new \Exception('Can\'t read data from '.$this->name.', '
                                .'instance of '.get_class($this), 400);

        // Required number of child channels?
        if ($this->childs >= 0 AND count($this->getChilds()) != $this->childs)
            throw new \Exception($this->name.' MUST have '.$this->childs.' child(s)', 400);

        // Prepare analysis of request
        $request = array_merge(
            array('start' => '', 'days' => NULL, 'end' => '', 'period' => ''),
            $request
        );

        // Start timestamp
        if ($request['start'] == '') {
            $request['start'] = 'midnight';
        } elseif (preg_match('~^-(\d+)$~', $request['start'], $args)) {
            // Start ? days backwards
            $request['start'] = 'midnight -'.$args[1].'days';
        } elseif (preg_match('~^sunrise(?:[;-](\d+))*~', $request['start'], $args)) {
            $request['start'] = (new \ORM\Settings)->getSunrise($this->time);
            if (isset($args[1])) $request['start'] -= $args[1]*60;
        }
        $this->start = is_numeric($request['start']) ? $request['start'] : strtotime($request['start']);
        if ($this->start === FALSE) {
            throw new \Exception('Invalid start timestamp: '.$request['start'], 400);
        }

        // 1st days count ...
        if (is_numeric($request['days'])) {
            $request['end'] = $this->start + $request['days']*86400;
        } else
        // ... 2nd end timestamp
        if ($request['end'] == '') {
            $request['end'] = 'midnight next day';
        } elseif (preg_match('~^-(\d+)$~', $request['end'], $args)) {
            $request['end'] = 'midnight -'.$args[1].'days';
        } elseif (preg_match('~^sunset(?:[;+](\d+))*~', $request['end'], $args)) {
            $request['end'] = (new \ORM\Settings)->getSunset($this->time);
            if (isset($args[1])) $request['end'] += $args[1]*60;
        }
        $this->end = is_numeric($request['end']) ? $request['end'] : strtotime($request['end']);
        if ($this->end === FALSE) {
            throw new \Exception('Invalid end timestamp: '.$request['end'], 400);
        }

        // Normalize aggregation period
        if (preg_match(
                '~^([.\d]*)(|l|last|r|readlast|i|min|minutes?|h|hours?|'.
                'd|days?|w|weeks?|m|months?|q|quarters?|y|years|a|all)$~',
                strtolower($request['period']),
                $args
            )) {

            $this->period[0] = $args[1] ?: 1;

            switch (substr($args[2], 0, 2)) {
                default:              $this->period[1] = self::NO;       break;
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

        // If no period is set for channels with childs, align child data at least to 1 min.
        if ($this->childs != 0 AND $this->period[1] == self::NO) {
            // Correct period for this channel
            $this->period = array(1, self::MINUTE);
            // Correct period for later child channel reads
            $request['period'] = '1min';
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
                if ($this->resolution > 0 AND $row['data'] < $last) {
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
                // Apply valid_from and valid_to here only
                // - channel is NOT writable, this will be handled during write()
                // - NOT read as child channel
                if ($this->write OR $this->isChild OR
                    ((is_null($this->valid_from) OR $row['data'] >= $this->valid_from) AND
                     (is_null($this->valid_to)   OR $row['data'] <= $this->valid_to))) {

                    $this->value = $row['data'];
                    Hook::process('data.read.after', $this);
                    $row['data'] = $this->value;

                    if ($this->isChild) {
                        $row['data'] = round($this->value, $this->decimals);
                        $row['min']  = round($row['min'], $this->decimals);
                        $row['max']  = round($row['max'], $this->decimals);

                        if ($this->meter AND $lastrow) {
                            $row['consumption'] = round($row['data'] - $lastrow['data'], $this->decimals);
                        } else {
                            $row['consumption'] = 0;
                        }
                    }

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

    /**
     * Time is only relevant for period != ALL
     */
    protected function filterReadTimestamp( &$q ) {
        if ($this->period[1] == self::ALL) return;

        // Read one period before real start for meter calculation
        $start = $this->start - $this->period[0] * $this->GroupingPeriod[$this->period[1]];
        // End is midnight > minus 1 second
        $q->filter('timestamp', array('bt' => array($start, $this->end-1)));
    }

    /**
     *
     */
    protected function SQLHeader( $request, $q ) {
        if (headers_sent() OR !array_key_exists('sql', $request) OR !$request['sql']) return;

        $sql = $this->name;
        if ($this->description) $sql .= ' (' . $this->description . ')';
        Header('X-SQL-' . uniqid() . ': ' . $sql . ': ' . preg_replace('~\n+~', ' ', $q));
    }

    /**
     * Shortcut method for save array access
     */
    protected function array_value(array $array, $key, $default=null) {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     *
     */
    private $_childs;

}
