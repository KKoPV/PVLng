<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
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
    public function insertBulk($id, Array $data) {

        if (empty($data)) return 0;

        $values = array();

        foreach ($data as $timestamp=>$value) {
            $values[] = $id . ',' . $timestamp . ',' . $value;
        }

        $sql = sprintf(
            'INSERT INTO `%s` (`id`, `timestamp`, `data`) VALUES (%s)'.$this->_onDuplicateKey(),
            $this->table, implode('),(', $values)
        );

        try {
            $this->_query($sql);
            return (self::$db->affected_rows <= 0) ? 0 : self::$db->affected_rows;
        } catch (\Exception $e) {
            return 0;
        }
    }

}
