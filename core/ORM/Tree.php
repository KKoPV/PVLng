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
    public function __construct ( $id=NULL ) {
        /* Build WITHOUT $id lookup, views have no primary key... */
        parent::__construct();
        if (isset($id)) $this->find('id', $id);
    }

    /**
     *
     */
    public function findByGUID( $guid ) {
        return $this->find('guid', $guid);
    }

    /**
     *
     */
    public function ModelClass() {
        return 'Channel\\'.$this->model;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_tree_view';

}
