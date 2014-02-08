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
class Tree extends \slimMVC\ORMTable {

    /**
     *
     */
    public function ModelClass() {
        return $this->model ? 'Channel\\'.$this->model : NULL;
    }

    /**
     *
     */
    public function findByGUID( $guid ) {
        return $this->find('guid', $guid);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_tree_view';

}
