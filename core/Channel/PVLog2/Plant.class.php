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

		$plant = new \PVLog\JSON2\Plant;
		$plant->setCreator('PVLng ' . PVLNG_VERSION);

		$date = isset($request['p1']) ? $request['p1'] : date('Y-m-d');
		$plant->setDeleteDayBeforeImport(($date != date('Y-m-d')));

		$request['start'] = $date;
		$request['end']   = $date . '+1day';

		// Set csv format for internal reads
		$request['format'] = 'csv';

		// 1st extra channel
		$extra = 1;
		$custom = array();

		foreach ($this->getChilds() as $child) {

			if ($child instanceof Inverter) {
				$plant->addInverter($child->read($request));
			} else {

				$data = $this->csv2data($child->read($request));
				if (empty($data)) continue;

				// Analyse childs:
				// - One with unit Wh    => consumption
				// - One with unit W/m²  => irradiation
				// - ALL other           => extra_?
				$attr = $child->getAttributes();

				switch ($attr['unit']) {
					// -------------------------------
					case 'W': // consumption
						$plant->setConsumption(
							new \PVLog\JSON2\Consumption(array('data'=>$data))
						);
						break;
					// -------------------------------
					case 'W/m²': // irradiation, several styles
					case 'W/qm':
					case 'W/m2':
						$plant->setIrradiation(
							new \PVLog\JSON2\Irradiation(array('data'=>$data))
						);
						break;
					// -------------------------------
					default:
						$custom['extra'][$extra]['name'] = $attr['name'];
						$custom['extra'][$extra]['unit'] = $attr['unit'];
						$plant->setExtra(
							$extra++, new \PVLog\JSON2\Extra\Sensor(array('data'=>$data))
						);
				}
			}
		}

		$custom['query_time'] = sprintf('%.0fms', (microtime(TRUE) - $this->ts) * 1000);

		$plant->setCustom($custom);

		return $plant->asArray(\PVLog\JSON2\JSON2::DATETIME);
	}
}
