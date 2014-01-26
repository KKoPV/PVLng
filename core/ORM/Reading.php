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
class Reading extends \slimMVC\ORMTable {

    /**
     *
     */
    public static function factory( $numeric ) {
        return $numeric ? new ReadingNum : new ReadingStr;
    }

    /**
     *
     */
    public function getLastReading( $id ) {
        return $this->app->db->queryOne('
            SELECT `data` FROM `'.$this->table.'`
             WHERE `id` = '.$id.'
             ORDER BY `timestamp` DESC
             LIMIT 1
        ');
    }
}
