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
class Plant extends Base {

    /**
     *
     */
    public function read( $request, $attributes=TRUE ) {

        $plant = new \YieldPlant;

        foreach ($this->getChilds() as $child) {
            $inverter = new \YieldInverter;
            $child->fetch($inverter, $request);
            $plant->addInverter($inverter);
        }

        $yield = new \Yield;
        $yield->setPlant($plant);

        return $this->finish($yield, $request);
    }

}
