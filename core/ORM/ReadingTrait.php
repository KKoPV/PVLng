<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace ORM;

/**
 *
 */
use Exception;

/**
 *
 */
trait ReadingTrait
{
    /**
     * Advanced setter for field "timestamp"
     *
     * Detect non-numeric timestamps
     *
     * @param  mixed    $timestamp Timestamp value
     * @return Instance For fluid interface
     */
    public function setTimestamp($timestamp)
    {
        if (!is_null($timestamp)) {
            $ts = is_numeric($timestamp) ? $timestamp : strtotime($timestamp);

            if ($ts <= 0) {
                // Throw away invalid timestamps
                throw new Exception('Ignore invalid timestamp: '.$timestamp, 200);
            }

            $timestamp = $ts;
        }

        return parent::setTimestamp($timestamp);
    }

    /**
     *
     */
    public function buffer($numeric)
    {
        $sql = sprintf(
            'INSERT INTO `pvlng_reading_buffer` VALUES (%d, %d, %d, "%s")',
            $numeric,
            $this->fields['id'],
            $this->fields['timestamp'] ?: time(),
            $this->fields['data']
        );

        try {
            $this->runQuery($sql);
            return (static::$db->affected_rows <= 0) ? 0 : static::$db->affected_rows;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Read last measuring value for channel
     *
     * @param integer $id Channel Id
     */
    public function getLastReading($id)
    {
        $sql = '
            SELECT `data`
              FROM `{1}`
             WHERE `id` = {2}
               AND `timestamp` = (
                       SELECT MAX(`timestamp`)
                         FROM `{1}`
                        WHERE `id` = {2}
                   )';

        return static::$db->queryOne($sql, $this->table, $id);
    }

    /**
     * Read last measuring value for channel but before given timestamp
     *
     * @param integer $id Channel Id
     * @param integer $timestamp Timestamp to read before
     */
    public function getLastReadingBeforeTimestamp($id, $timestamp)
    {
        $sql = '
            SELECT `data`
              FROM `{1}`
             WHERE `id` = {2}
               AND `timestamp` < {3}
            OERDER BY `timestamp` DESC
             LIMIT 1';

        return static::$db->queryOne($sql, $this->table, $id, $timestamp);
    }

    /**
     * Remove all future values from reading table
     *
     * @param integer $id Channel Id
     */
    public function deleteFutureReadings($id, $timestamp = null)
    {
        if ($timestamp == '') {
            $timestamp = 'UNIX_TIMESTAMP()';
        }

        return static::$db->query(
            'DELETE FROM `{1}` WHERE `id` = {2} AND `timestamp` > {3}',
            $this->table,
            $id,
            $timestamp
        );
    }
}
