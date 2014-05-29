<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

/**
 *
 */
namespace ORM;

/**
 *
 */
abstract class Reading extends ReadingBase {

    /**
     *
     */
    public static function factory( $numeric ) {
        return $numeric ? new ReadingNum : new ReadingStr;
    }

    /**
     *
     */
    public function getLastReading( $id, $timestamp=NULL ) {
        if (isset($timestamp)) $timestamp = ' AND `timestamp` <= '.$timestamp;
        return $this->app->db->queryOne('
            SELECT `data` FROM `'.$this->table.'`
             WHERE `id` = '.$id.$timestamp.'
             ORDER BY `timestamp` DESC
             LIMIT 1
        ');
    }
}
