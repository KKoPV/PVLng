<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class DatabaseUsage extends Channel {

    /**
     *
     */
    public function read( $request ) {

        $this->performance->setAction('read');

        $this->before_read($request);

        if ($this->period[1] == self::NO) {
            // Set period to at least 1 minute
            $this->period[1] = self::ASCHILD;
        }

        $q = new \DBQuery;

        $buffer = new \Buffer;

        if ($this->period[1] == self::READLAST) {

            $q->select($this->table[$this->extra])
              ->get($q->FROM_UNIXTIME($q->MAX('timestamp')), 'datetime')
              ->get($q->MAX('timestamp'), 'timestamp')
              ->get($q->COUNT('id'), 'data')
              ->get(0, 'min')
              ->get($q->COUNT('id'), 'max')
              ->get($q->COUNT('id'), 'count')
              ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff')
              ->limit(1);

            $buffer->write((array) $this->db->queryRow($q));

        } else {

            if ($this->period[1] == self::LAST OR $this->period[1] == self::ALL) {

                $q->select($this->table[$this->extra])
                  ->get($q->FROM_UNIXTIME('timestamp'), 'datetime')
                  ->get('timestamp')
                  ->get($q->COUNT('id'), 'data')
                  ->get($q->COUNT('id'), 'min')
                  ->get($q->COUNT('id'), 'max')
                  ->get(1, 'count')
                  ->get(0, 'timediff')
                  ->get('timestamp', 'g');

            } else {

                $q->select($this->table[$this->extra])
                  ->get($q->FROM_UNIXTIME($q->MIN('timestamp')), 'datetime')
                  ->get($q->MIN('timestamp'), 'timestamp')
                  ->get($q->COUNT('id'), 'data')
                  ->get($q->COUNT('id'), 'min')
                  ->get($q->COUNT('id'), 'max')
                  ->get($q->COUNT('id'), 'count')
                  ->get($q->MAX('timestamp').'-'.$q->MIN('timestamp'), 'timediff')
                  ->get($this->periodGrouping(), 'g')
                  ->group('g');
            }

            if ($this->period[1] != self::ALL) {
                // Time is only relevant for period != ALL
                if ($this->start) {
                    $q->filter('timestamp', array('min'=>$this->start-self::$Grouping[$this->period[1]][0]));
                }
                if ($this->end < time()) {
                    $q->filter('timestamp', array('max'=>$this->end-1));
                }
            }

            $q->order('timestamp');

            $consumption = 0;

            // Use bufferd result set
            $this->db->setBuffered();

            if ($res = $this->db->query($q)) {

                $last = (self::$Grouping[$this->period[1]][0] > 0)
                      ? $res->fetch_assoc()
                      : FALSE;

                while ($row = $res->fetch_assoc()) {

                    $row['consumption'] = $row['data'];
                    $row['data'] = $last ? $last['data'] + $row['consumption'] : 0;

                    $last = $row;

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

        if (array_key_exists('sql', $request) AND $request['sql']) {
            $sql = $this->name;
            if ($this->description) $sql .= ' (' . $this->description . ')';
            Header('X-SQL-'.substr(md5($sql), 8) . ': ' . $sql . ': ' . $q);
        }

        return $this->after_read($buffer);
    }

}
