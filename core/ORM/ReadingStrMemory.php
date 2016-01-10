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
class ReadingStrMemory extends ReadingStrMemoryBase {

    /**
     *
     * @param mixed $id Key describing one row, on primary keys
     *                  with more than field, provide an array
     */
    public function __construct ( $id=NULL ) {
        if (self::$first) {
            self::$db->query('
                CREATE TABLE IF NOT EXISTS `pvlng_reading_str_tmp` (
                    `id`        smallint unsigned NOT NULL DEFAULT 0,
                    `timestamp` int               NOT NULL DEFAULT 0,
                    `data`      varchar(50)       NOT NULL DEFAULT "",
                    PRIMARY KEY (`id`, `timestamp`)
                ) ENGINE=Memory PARTITION BY LINEAR KEY(`id`) PARTITIONS 10
            ');

            self::$first = FALSE;
        }

        parent::__construct($id);
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     * First call
     */
    private static $first = TRUE;

}
