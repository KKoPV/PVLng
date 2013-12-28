<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel\PVLog2;

/**
 *
 */
class Inverter extends Base {

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        $childs = $this->getChilds();

        if (count($childs) < 1) {
            throw new \Exception('"'.$this->name.'" needs at least 1 child: Pac', 400);
        }

        // Analyse childs:
        // - 1st with unit W           => actual (Pac)
        // - 1st with unit Wh          => total
        // - 2nd and more with unit W  => string[]->data (Pdc)
        // - 1st with unit °C or F     => temperature
        $bPac = FALSE;

        $inverter = new \PVLog\JSON2\Inverter;

        foreach ($childs as $child) {

            $data = $this->getChildData($child, $request);
            if (empty($data)) continue;

            $attr = $child->getAttributes();

            switch ($attr['unit']) {
                // -------------------------------
                case 'W': // Pac or Pdc
                    if (!$bPac) {
                        // 1st child with unit W
                        $inverter->setActual($data);
                        $bPac = TRUE;
                    } else {
                        // Additional child with unit W
                        $inverter->addString(
                            new \PVLog\JSON2\String(array('actual'=>$data))
                        );
                    }
                    break;

                // -------------------------------
                case 'Wh': // total
                    $inverter->setTotal(
                        new \PVLog\JSON2\Total($data)
                    );
                    break;

                // -------------------------------
                case '°C': // Inverter temperature
                    $inverter->setTemperature(
                        new \PVLog\JSON2\Temperature(array('actual'=>$data))
                    );
                    break;

                // -------------------------------
                case 'F': // Inverter temperature
                    // Convert to °C
                    $data = \PVLog\JSON2\Helper::F2C($data);
                    $inverter->setTemperature(
                        new \PVLog\JSON2\Temperature(array('actual'=>$data))
                    );
                    break;

                // -------------------------------
                default: // silently ignore all other childs
                    break;
            }
        }

        return $inverter;
    }

}
