<?php
/**
 * Real access class for table 'pvlng_log'
 *
 * To extend the functionallity, edit here
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Initial creation
 */
namespace ORM;

/**
 *
 */
class Log extends LogBase {

    /**
     * Bulk save at once
     */
    public static function save( $scope, $data ) {
        $log = new Log;
        $log->setScope((string) $scope)
            ->setData((string) $data)
            ->insert();
    }

}
