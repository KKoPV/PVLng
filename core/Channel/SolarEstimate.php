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

        if ($this->end < strtotime('00:00')) return;

        $childs = $this->getChilds();
        $childCnt = count($childs);

        if ($childCnt == 0) return;

        $days = $this->config->get('Model.SolarEstimate.Days');

        // Get todays production, set field names to 0 and 1 for list(...)
        $sql = 'SELECT MAX(`data`) - MIN(`data`) AS `0`
                     , MAX(`timestamp`) AS `1`
                  FROM `pvlng_reading_num`
                 WHERE `id` = {1}
                    -- Align to today 00:00
                   AND `timestamp` >= UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"));
                ';
        list($production, $last) = $this->db->queryRowArray($sql, $childs[0]->entity);

        // Start average value
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
                        ) t
                ';
        $start = $this->db->queryOne($sql, $childs[0]->entity, $days, $last);

        // Estimated production from now
        $sql = 'SELECT UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) AS `timestamp`
                     , ABS(AVG(`data`)) - {3} + {4} AS `data`
                  FROM `pvlng_reading_num`
                 WHERE `id` = {1}
                    -- Align to ? days back 00:00
                   AND `timestamp` > UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL {2} DAY, "%Y-%m-%d"))
                    -- Align to today 00:00
                   AND `timestamp` < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-%d"))
                   AND UNIX_TIMESTAMP(CONCAT(DATE_FORMAT(NOW(), "%Y-%m-%d "), DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H:%i"))) >= {5}
                 GROUP BY DATE_FORMAT(FROM_UNIXTIME(`timestamp`), "%H%i")
                HAVING count(*) = {2}
                ';
        $res = $this->db->query($sql, $childs[0]->entity, $days, $start, $production, $last);

        // Aplly child resolution here!
        $factor = $childs[0]->resolution;

        while ($row = $res->fetch_object()) {
            if ($last) {
                // Save 1st row to last reading timestamp from child channel
                $this->saveValue($last, $row->data*$factor);
                $last = FALSE;
            } else {
                $this->saveValue($row->timestamp, $row->data*$factor);
            }
            $lastrow = $row;
        }

        $sunset = $this->config->getSunset($this->start);
        if (isset($lastrow)) {
            // Add a last value at sunset
            $this->saveValue($sunset, $lastrow->data*$factor);
        } elseif (time() < $sunset) {
            // Fill space at end with actual production
            $this->saveValue($last, $production*$factor);
            $this->saveValue($sunset, $production*$factor);
        }
    }
}
