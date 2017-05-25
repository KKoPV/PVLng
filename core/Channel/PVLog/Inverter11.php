<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel\PVLog;

/**
 * PV-Log generator classes
 */
use PVLog\Classes\Json\Inverter;

/**
 *
 */
class Inverter11 extends BaseAbstract11
{

    /**
     *
     */
    public function read($request, $attributes = true)
    {
        $inverter = new Inverter;

        $childs = $this->getChilds();

        if (!count($childs)) {
            return $inverter;
        }

        foreach ($childs as $id => $child) {
            $this->getTaggedChildData($inverter, $child, $request);
        }

        return $inverter;
    }
}
