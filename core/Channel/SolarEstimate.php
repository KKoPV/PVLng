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
    protected function before_read( &$request ) {

        parent::before_read($request);

        // Only for today possible, not backwards
        if ($this->end < strtotime('midnight')) return;

        // Only during daylight times today
        $sunset = (new \ORM\Settings)->getSunset($this->start);
        if ($sunset < time()) return;

        if ($this->dataExists()) return;

        $child = $this->getChild(1);
        $days  = $this->extra;

        // Get todays production, set field names to 0 and 1 for list(...)
        $sql = $this->db->sql(
            'SELECT MIN(`data`), MAX(`data`), MAX(`timestamp`)
               FROM `pvlng_reading_num`
              WHERE `id` = {1}
                 -- Align to today 00:00
                AND `timestamp` >= UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))',
            $child->entity
        );

        list($Production1st, $ProductionLast, $lastTimestampToday) = array_values($this->db->queryRowArray($sql));

        // Do we have still data today?
        if ($lastTimestampToday) {

            $ProductionToday = $ProductionLast - $Production1st;

            // Start average value to align data
            $sql = $this->db->sql(
                'SELECT MIN(`data`), (MAX(`data`) - MIN(`data`)) -- Average production
                   FROM (
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
                             -- Align to full minute
                          GROUP BY `timestamp` DIV 60
                     ) t1
                  GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                 HAVING COUNT(*) = {2} ) t2',
                $child->entity, $days, $lastTimestampToday
            );

            list($Average1st, $Average) = array_values($this->db->queryRowArray($sql));

            // Scale todays production to average production last days
            // @todo
            #$scale = $Average ? $ProductionToday/$Average : 1;
            $scale = 1;

            // Estimated production from now
            $sql = $this->db->sql(
                'SELECT UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "),
                                              DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))
                        ) AS `timestamp`
                      , `data`
                   FROM ( -- If there is more than one reading for a minute, consolidate to one!
                          SELECT `timestamp`, (AVG(`data`) - {3}) * {4} + {5} AS `data`
                            FROM `pvlng_reading_num`
                           WHERE `id` = {1}
                              -- Align to ? days back 00:00
                             AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                              -- Align to today 00:00
                             AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                              -- Align to full minute
                           GROUP BY `timestamp` DIV 60
                        ) t
                  WHERE UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) >= {6}
                  GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                 HAVING COUNT(*) = {2}',
                $child->entity, $days, $Average1st, $scale, $ProductionToday, $lastTimestampToday
            );

            if ($res = $this->db->query($sql)) {
                while ($row = $res->fetch_object()) {
                    // Apply child resolution here
                    $row->data = $row->data * $child->resolution;
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
                $last = $lastrow->data;
            } else {
                // Fill space at end with actual production
                // Apply child resolution here
                $last = $ProductionToday * $child->resolution;
                $this->saveValue($lastTimestampToday, $last);
            }

            // Add a last value at sunset
            $this->saveValue($sunset, $last);
        }

        $this->dataCreated();
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
