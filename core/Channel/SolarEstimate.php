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
        if ($this->end < strtotime('00:00')) return;

        $childs = $this->getChilds();
        $childCnt = count($childs);

        if ($childCnt == 0) return;

        $days = $this->extra;

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
             $lastTimestampToday) = $this->db->queryRowArray($sql, $childs[0]->entity);

        // Do we have still data today?
        if (!$lastTimestampToday) return;

        $ProductionToday = $ProductionLast - $Production1st;

        // Base average value
        $sql = 'SELECT MIN(`data`)
                  FROM (SELECT ABS(AVG(`data`)) AS `data`
                          FROM `pvlng_reading_num`
                         WHERE `id` = {1}
                            -- Align to ? days back 00:00
                           AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                            -- Align to today 00:00
                           AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                         GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                        HAVING count(*) = {2}
                        ) t';

        $AverageBase = $this->db->queryOne($sql, $childs[0]->entity, $days);

        // Start average value to align data
        $sql = 'SELECT MIN(`data`)
                  FROM (SELECT ABS(AVG(`data`)) AS `data`
                          FROM `pvlng_reading_num`
                         WHERE `id` = {1}
                            -- Align to ? days back 00:00
                           AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                            -- Align to today 00:00
                           AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                           AND UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) >= {3}
                         GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                        HAVING count(*) = {2}
                        ) t';

        $Average1st = $this->db->queryOne($sql, $childs[0]->entity, $days, $lastTimestampToday);

        // Estimated production from now
        $sql = 'SELECT UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) AS `timestamp`
                     , (ABS(AVG(`data`)) - {3}) * {4} + {5} AS `data`
                  FROM `pvlng_reading_num`
                 WHERE `id` = {1}
                    -- Align to ? days back 00:00
                   AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                    -- Align to today 00:00
                   AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                   AND UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) >= {6}
                 GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                HAVING count(*) = {2}';

        $res = $this->db->query($sql, $childs[0]->entity, $days, $Average1st, $ProductionToday/($Average1st-$AverageBase), $ProductionToday, $lastTimestampToday);

        // Apply child resolution here
        $Scale = $childs[0]->resolution;

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

        $sunset = $this->config->getSunset($this->start);

        if (isset($lastrow)) {
            // Add a last value at sunset
            $this->saveValue($sunset, $lastrow->data);
        } elseif (time() < $sunset) {
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
        $last = 0;

        // Fake consumption
        foreach (parent::after_read($buffer) as $id=>$row) {
            $row['consumption'] = $row['data'] - $last;
            $last = $row['data'];

            $datafile->write($row, $id);
        }

        // Now set to meter
        $this->meter = TRUE;

        return $datafile;
    }
}
