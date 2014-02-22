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
class Performance extends \slimMVC\ORMTable {

    /**
     *
     */
    public function __construct ( $id=NULL ) {
        parent::__construct($id);

        $this->app->db->query('
            CREATE TABLE IF NOT EXISTS `pvlng_performance` (
              `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `action` enum("read","write") NOT NULL,
              `time` int(10) unsigned NOT NULL
            ) ENGINE=MEMORY
        ');
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_performance';

}
