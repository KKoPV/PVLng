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
class ReadingNum extends ReadingNumBase {

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
