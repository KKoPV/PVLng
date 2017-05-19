<?php
/**
 * Calc data for arithmetic or harmonic average
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 */
namespace Channel;

/**
 *
 */
class Averageline extends InternalCalc
{
    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function before_read(&$request)
    {

        parent::before_read($request);

        if ($this->dataExists()) return;

        $this->db->call('pvlng_model_averageline', $this->entity, $this->getChild(1)->entity, $this->extra);

        $this->dataCreated();
    }
}
