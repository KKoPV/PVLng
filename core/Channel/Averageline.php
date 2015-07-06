<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 *
 * 1.0.0
 * - initial creation
 */
namespace Channel;

/**
 *
 */
class Averageline extends InternalCalc {

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function before_read( &$request ) {

        parent::before_read($request);

        if (!$this->dataExists()) {
            (new \ORM\ChannelType)->calcAverageLine($this->entity, $this->getChild(1)->entity, $this->extra);
#            $this->db->query('CALL `pvlng_model_averageline`({1}, {2}, {3})',
#                             $this->entity, $this->getChild(1)->entity, $this->extra);
            $this->dataCreated();
        }
    }
}
