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
class Performance extends PerformanceBase {

    /**
     *
     */
    public function __construct ( $id=NULL ) {
        parent::__construct($id);

        if (self::$create) {
            $this->app->db->query(self::$create);
            // Free some memory and use also as "done" marker...
            self::$create = FALSE;
        }
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * Create table on 1st call
     */
    protected static $create = '
        CREATE TABLE IF NOT EXISTS `pvlng_performance` (
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `action`    enum("read","write") NOT NULL,
          `time`      int(10) unsigned NOT NULL
        ) ENGINE=MEMORY
    ';

    /**
     *
     */
    protected $table = 'pvlng_performance';

}
