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
class SolarEstimate extends InternalCalc {

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        // Only for today possible, not backwards
        if ($this->end < strtotime('midnight')) return;

        // Only during daylight times today
        $sunset = $this->config->getSunset($this->start);
        if ($sunset < time()) return;

        $child = $this->getChild(1);
        $days  = $this->extra;

        // Get todays production, set field names to 0 and 1 for list(...)
        $sql = 'SELECT MIN(`data`) AS `0`
                     , MAX(`data`) AS `1`
                     , MAX(`timestamp`) AS `2`
                  FROM `pvlng_reading_num`
                 WHERE `id` = {1}
                    -- Align to today 00:00
                   AND `timestamp` >= UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))';

        list($Production1st,
             $ProductionLast,
             $lastTimestampToday) = $this->db->queryRowArray($sql, $child->entity);

        // Do we have still data today?
        if (!$lastTimestampToday) return;

        $ProductionToday = $ProductionLast - $Production1st;

        // Start average value to align data
        $sql = 'SELECT MIN(`data`) FROM (
                    SELECT * FROM (
                            -- If there is more than one reading for a minute, consolidate to one!
                        SELECT `timestamp`, AVG(`data`) AS `data`
                          FROM `pvlng_reading_num`
                         WHERE `id` = {1}
                            -- Align to ? days back 00:00
                           AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                            -- Align to today 00:00
                           AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                           AND UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) >= {3}
                         GROUP BY `timestamp` DIV 60
                    ) t1
                 GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                HAVING count(*) = {2} ) t2';

        $sql = $this->db->sql($sql, $child->entity, $days, $lastTimestampToday);
        $Average1st = $this->db->queryOne($sql);

        // Estimated production from now
        $sql = 'SELECT UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "),
                                             DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))
                       ) AS `timestamp`
                     , `data`
                  FROM ( -- If there is more than one reading for a minute, consolidate to one!
                         SELECT `timestamp`, (AVG(`data`) - {3}) + {4} AS `data`
                           FROM `pvlng_reading_num`
                          WHERE `id` = {1}
                             -- Align to ? days back 00:00
                            AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                             -- Align to today 00:00
                            AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                          GROUP BY `timestamp` DIV 60
                       ) t
                 WHERE UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) >= {5}
              GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                HAVING count(*) = {2}';

        $sql = $this->db->sql($sql, $child->entity, $days, $Average1st,
                              $ProductionToday, $lastTimestampToday);
        $res = $this->db->query($sql);

        // Apply child resolution here
        $Scale = $child->resolution;

        if ($res) {
            while ($row = $res->fetch_object()) {
                $row->data = round($row->data * $Scale, $this->decimals);
                if ($lastTimestampToday) {
                    // Save 1st row to last reading timestamp from child channel
                    $this->saveValue($lastTimestampToday, $row->data);
                    // Unset timestamp as marker
                    $lastTimestampToday = FALSE;
                } else {
                    $this->saveValue($row->timestamp, $row->data);
                }
                $lastrow = $row;
            }
        }

        if (isset($lastrow)) {
            // Add a last value at sunset
            $this->saveValue($sunset, $lastrow->data);
        } else {
            // Fill space at end with actual production
            $value = round($ProductionToday * $Scale, $this->decimals);
            $this->saveValue($lastTimestampToday, $value);
            $this->saveValue($sunset, $value);
        }
    }

    /**
     *
     */
    protected function after_read( \Buffer $buffer ) {

        $datafile = new \Buffer;
        $last = FALSE;

        // Fake consumption
        foreach (parent::after_read($buffer) as $id=>$row) {
            $row['consumption'] = $last ? $row['data'] - $last : 0;
            $datafile->write($row, $id);
            $last = $row['data'];
        }

        // Now set to meter
        $this->meter = TRUE;

        return $datafile;
    }
}
