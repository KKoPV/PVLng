<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-28-g4d7f5c3 2013-05-10 14:29:24 +0200 Knut Kohl $
 */
namespace Channel\PVLog;

/**
 *
 */
class Inverter extends Base {

	/**
	 *
	 */
	public function read( $request, $attributes=TRUE ) {

		$inverter = new \YieldInverter;
		$this->fetch($inverter, $request);

		$plant = new \YieldPlant;
		$plant->addInverter($inverter);

		$yield = new \Yield;
		$yield->setPlant($plant);

		return $this->finish($yield, $request);
	}

}
