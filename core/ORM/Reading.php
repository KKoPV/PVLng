<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
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
        $q = new \DBQuery($this->table);
        $q->get('data')->filter('id', $id)->order('timestamp', TRUE)->limit(1);

        if (!is_null($timestamp)) {
            $q->filter('timestamp', array('le'=>$timestamp));
        }

        return self::$db->queryOne($q);
    }
}
