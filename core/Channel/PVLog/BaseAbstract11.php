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
use PVLog\Classes\Json\Json;
use PVLog\Classes\Json\Set;
use PVLog\Classes\Json\Strings;

/**
 *
 */
abstract class BaseAbstract11 extends \Channel {

    /**
     *
     */
    protected function getTaggedChildData( $parent, $child, $request ) {
        if ($property = $child->getTag('PV-Log JSON 1.1')) {
            if ($property == 'string') {
                $data = new Strings;
            } else {
                $data = Json::factory($property);
            }

            if ($data instanceof Set) {
                foreach ($child->read($request) as $row) {
                    $data[$row['timestamp']] = $row['data'];
                }
            } elseif ($child->unit == 'Wh') {
                foreach ($child->read($request) as $row) {
                    $data->addTotalWattHours($row['timestamp'], $row['data']);
                }
            } elseif ($child->unit == 'W') {
                foreach ($child->read($request) as $row) {
                    $data->addPowerAcWatts($row['timestamp'], $row['data']);
                }
            }

            if ($property == 'string') {
                $parent->addString($data);
            } else {
                $parent->set($property, $data);
            }
        } else {
            throw new \Exception('Unkown/untagged channel in '.$this->name.': '.$child->name);
        }
    }

}
