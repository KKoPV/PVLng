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
class ReadingNum extends ReadingNumBase
{
    /**
     * getLastReading()
     */
    use ReadingTrait;

    /**
     * Mass insert/replace measuring data
     *
     * @param integer $id Channel Id
     * @param array $data Array of Array($timestamp => $value)
     */
    public function insertBulk($id, array $data)
    {

        if (empty($data)) {
            return 0;
        }

        $values = array();

        foreach ($data as $timestamp => $value) {
            $values[] = $id . ',' . $timestamp . ',' . $value;
        }

        $sql = sprintf(
            'INSERT INTO `%s` (`id`, `timestamp`, `data`) VALUES (%s) %s',
            $this->table,
            implode('),(', $values),
            $this->buildOnDuplicateKey()
        );

        try {
            $this->runQuery($sql);
            return (static::$db->affected_rows <= 0) ? 0 : static::$db->affected_rows;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
