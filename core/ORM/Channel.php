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
class Channel extends \slimMVC\ORMTable {

    /**
     * Setter for 'extra'
     */
    public function setExtra( $extra ) {
        $this->set('extra', str_replace('\r', '', json_encode($extra)));
    }

    /**
     * Getter for 'extra'
     */
    public function getExtra() {
        return json_decode($this->get('extra'));
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $table = 'pvlng_channel';

}
