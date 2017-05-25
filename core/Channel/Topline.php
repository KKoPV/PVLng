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
class Topline extends InternalCalc
{

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function beforeRead(&$request)
    {

        parent::beforeRead($request);

        if (!$this->dataExists()) {
            // Calc direct inside database
            $this->db->query('CALL pvlng_model_topline({1}, {2})', $this->entity, $this->getChild(1)->entity);
            $this->dataCreated();
        }
    }
}
