<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-28-g4d7f5c3 2013-05-10 14:29:24 +0200 Knut Kohl $
 */
namespace Channel\PVLog2;

/**
 *
 */
class Plant extends Base {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$this->before_read($request);

		$date = !empty($request['p1']) ? $request['p1'] : date('Y-m-d');

		$request['start'] = $date;
		$request['end']   = $date . '+1day';

		// Set csv format for internal reads
		$request['format'] = 'csv';

		$plant = new \PVLog\JSON2\Plant;
		$plant->setCreator('PVLng ' . PVLNG_VERSION)
		      ->setDeleteDayBeforeImport(($date != date('Y-m-d')));

		// 1st extra channel
		$extra = 1;
		$custom = array();

		foreach ($this->getChilds() as $child) {

			if ($child instanceof Inverter) {
				$plant->addInverter($child->read($request));
			} else {

				$data = $this->getChildData($child, $request);
				if (empty($data)) continue;

				// Analyse childs:
				// - 1st with unit Wh    => consumption
				// - 1st with unit W/m²  => irradiation
				// - ALL other           => extra_?
				$bConsumption = $bIrradiation = FALSE;

				$attr = $child->getAttributes();

				if ($attr['unit'] == 'W' AND !$bConsumption) {
					$plant->setConsumption(
						new \PVLog\JSON2\Consumption(array('actual'=>$data))
					);
					$bConsumption = TRUE;
				} elseif (($attr['unit'] == 'W/m²' OR   // several styles
				           $attr['unit'] == 'W/qm' OR
				           $attr['unit'] == 'W/m2') AND !$bIrradiation) {
					$plant->setIrradiation(
						new \PVLog\JSON2\Irradiation(array('actual'=>$data))
					);
					$bIrradiation = TRUE;
				} else {
					$custom['extra'][$extra]['name']  = $attr['name'];
					$custom['extra'][$extra]['meter'] = +$attr['meter'];
					$custom['extra'][$extra]['unit']  = $attr['unit'];
					$plant->setExtra(
						$extra++,
						$attr['meter']
						? new \PVLog\JSON2\Extra\Meter(array('actual'=>$data))
						: new \PVLog\JSON2\Extra\Sensor(array('actual'=>$data))
					);
				}
			}
		}

		$custom['query_time'] = sprintf('%.0fms', (microtime(TRUE) - $this->ts) * 1000);

		$plant->setCustom($custom);

		return $plant->asArray(\PVLog\JSON2\JSON2::DATETIME);
	}
}
