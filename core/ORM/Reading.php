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
abstract class Reading {

    /**
     *
     */
    public static function factory( $numeric ) {
        return $numeric ? new ReadingNum : new ReadingStr;
    }

}
