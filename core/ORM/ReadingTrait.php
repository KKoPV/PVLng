<?php

namespace ORM;

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
            $ts = is_numeric($timestamp)
                ? $timestamp
                : strtotime($timestamp);

            if ($ts <= 0) {
                // Throw away invalid timestamps
                throw new \Exception('Ignore invalid timestamp: '.$timestamp, 200);
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
            $numeric, $this->fields['id'], $this->fields['timestamp'] ?: time(), $this->fields['data']
        );

        try {
            $this->runQuery($sql);
            return (self::$db->affected_rows <= 0) ? 0 : self::$db->affected_rows;
        } catch (\Exception $e) {
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

        $sql = sprintf('
            SELECT `data`
              FROM `%1$s`
             WHERE `id` = %2$d
               AND `timestamp` = (SELECT MAX(`timestamp`) FROM `%1$s` WHERE `id` = %2$d)',
            $this->table, $id
        );

        return self::$db->queryOne($sql);
    }
}
