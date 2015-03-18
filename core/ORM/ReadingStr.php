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
class ReadingStr extends ReadingStrBase {

    /**
     *
     */
    public function getLastReading( $id, $timestamp=NULL ) {
        $q = new \DBQuery($this->table);
        $q->get('data')->filter('id', $id)->order('timestamp', TRUE)->limit(1);

        if (!is_null($timestamp)) {
            $q->filter('timestamp', array('le'=>$timestamp));
        }

        return self::$db->queryOne($q);
    }

}
