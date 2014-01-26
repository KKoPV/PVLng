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
class ChannelView extends \slimMVC\ORMTable {

    /**
     *
     */
    public function __construct ( $id=NULL ) {
        /* Build WITHOUT $id lookup, views have no primary key... */
        parent::__construct();
        if (isset($id)) $this->find('id', $id);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_channel_view';

}
