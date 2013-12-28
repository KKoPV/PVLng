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
class ReadingNumMemory extends \slimMVC\ORMTable {

    /**
     *
     * @param mixed $id Key describing one row, on primary keys
     *                  with more than field, provide an array
     */
    public function __construct ( $id=NULL ) {
        /* Build WITHOUT $id lookup, must be done later, if table not exists yet */
        parent::__construct();
        $this->app->db->query('
            CREATE TABLE IF NOT EXISTS `pvlng_reading_num_tmp` (
                `id` int(10) unsigned NOT NULL,
                `timestamp` int(10) unsigned NOT NULL,
                `data` decimal(13,4) NOT NULL,
                PRIMARY KEY (`id`, `timestamp`)
            ) ENGINE=MEMORY
        ');
        if (isset($id)) $this->findPrimary($id);
    }

    /**
     *
     */
    public function deleteById( $id ) {
        $this->app->db->query('DELETE FROM `pvlng_reading_num_tmp` WHERE `id` = {1}', $id);
        return $this->app->db->affected_rows;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_reading_num_tmp';

    /**
     *
     */
    protected $fields = array (
        'id'        => '',
        'timestamp' => '',
        'data'      => '',
    );

    /**
     *
     */
    protected $nullable = array (
        'id'        => false,
        'timestamp' => false,
        'data'      => false,
    );

    /**
     *
     */
    protected $primary = array( 'id', 'timestamp' );

}
