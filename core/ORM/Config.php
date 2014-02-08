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
class Config extends \slimMVC\ORMTable {

    /**
     *
     */
    public function getAPIkey() {
        return $this->app->db->queryOne('SELECT `getAPIkey`()');
    }

    /**
     *
     */
    public function resetAPIkey() {
        return $this->app->db->query('DELETE FROM `'.$this->table.'` WHERE `key` = "APIKey" LIMIT 1');
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_config';

}
