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
 *
 */
class Inverter extends Base
{

    /**
     *
     */
    public function read($request, $attributes = true)
    {

        $inverter = new \YieldInverter;
        $this->fetch($inverter, $request);

        $plant = new \YieldPlant;
        $plant->addInverter($inverter);

        $yield = new \YieldOverall;
        $yield->setPlant($plant);

        return $this->finish($yield, $request);
    }
}
