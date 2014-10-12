<?php
/**
 * Real access class for 'pvlng_tariff'
 *
 * To extend the functionallity, edit here
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Initial creation
 */
namespace ORM;

/**
 *
 */
class Tariff extends TariffBase {

    const SECONDS = 0;
    const DAY     = 1;
    const STRING  = 2;

    /**
     *
     */
    public function cloneDatesTimes( $from ) {

        if (!$this->getId() OR !$from) return;

        self::$db->query('
                INSERT INTO `pvlng_tariff_date`
                SELECT {2}, `date`, `cost`
                  FROM `pvlng_tariff_date`
                 WHERE `id` = {1}
        ', $from, $this->getId());

        self::$db->query('
                INSERT INTO `pvlng_tariff_time`
                SELECT {2}, `date`, `time`, `days`, `tariff`, `comment`
                  FROM `pvlng_tariff_time`
                 WHERE `id` = {1}
        ', $from, $this->getId());
    }

    /**
     *
     * @param  int $date Seconds timestamp inside a day
     * @param  int $mode Return times for date, as seconds or as string '00:00'
     * @return array
     */
    public function getTariffDay( $date, $mode=self::DAY ) {

        if (!$this->getId()) return array();

        $date = floor($date / 86400) * 86400;

        $sql = '
          SELECT TIME_TO_SEC(`time`) AS `time`, `tariff`
            FROM `pvlng_tariff_time`
           WHERE `id` = {1}
             AND `date` = ( SELECT MAX(`date`) FROM `pvlng_tariff_time` WHERE `id` = {1} AND `date` <= "{2}" )
             AND FIND_IN_SET(IF(DAYOFWEEK("{2}")=1, 7, DAYOFWEEK("{2}")-1), days)
           ORDER BY `time`';

        $start = $tariff = 0;
        $data = array();

        foreach (self::$db->queryRows($sql, $this->getId(), date('Y-m-d', $date)) as $row) {
            if ($row->time != $start) {
                // New line found
                $data[] = array(
                    'start' => $this->formatTime($date, $start, $mode),
                    'end'   => $this->formatTime($date, $row->time, $mode),
                    'cost'  => $tariff
                );
            }

            // Remember for next/last row
            $start  = $row->time;
            $tariff = $row->tariff;
        }
        $data[] = array(
            'start' => $this->formatTime($date, $start, $mode),
            'end'   => $this->formatTime($date, 86400, $mode), // 24:00
            'cost'  => $tariff
        );

        return $data;
    }

    /**
     *
     * @param  string $from Date
     * @param  string $to Date exclusive
     * @return array
     */
    public function getTariffTimes( $from, $to ) {

        if (!$this->getId()) return array();

        if (!$to) $to = $from + 1; // Force run once

        $sql = '
          SELECT TIME_TO_SEC(`time`) AS `time`, `tariff`
            FROM `pvlng_tariff_time`
           WHERE `id` = {1}
             AND `date` = ( SELECT MAX(`date`) FROM `pvlng_tariff_time` WHERE `id` = {1} AND `date` <= "{2}" )
             AND FIND_IN_SET(IF(DAYOFWEEK("{2}")=1, 7, DAYOFWEEK("{2}")-1), days)
           ORDER BY `time`';

        $last = 0;
        $data = array();

        while ($from < $to) {
            foreach (self::$db->queryRows($sql, $this->getId(), date('Y-m-d', $from)) as $row) {
                if ($last != $row->tariff) {
                 $data[date('U', $from) + $row->time] = $row->tariff;
                   $last = $row->tariff;
                }
            }
            $from += 86400;
        }

        return $data;
    }

    /**
     *
     */
    protected function formatTime( $date, $time, $mode ) {
        switch ($mode) {
            case self::SECONDS:
                return $time;

            case self::DAY:
                return date('U', $date) + $time;

            case self::STRING:
                if ($time == 86400) {
                    return '24:00:00';
                } else {
                    $h = floor($time / 3600);
                    $m = floor(($time - $h*3600) / 60);
                    $s = $time - $h*3600 - $m*60;
                    return sprintf('%02d:%02d:%02d', $h, $m, $s);
                }
        }
    }
}
