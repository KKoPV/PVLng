<?php
/**
 *
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
