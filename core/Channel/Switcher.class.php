<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace Channel;

/**
 *
 */
class Switcher extends \Channel {

	/**
	 *
	 */
	public function write( $value, $timestamp=NULL ) {
		// Get last state and ...
		$last = $this->getLastReading();

		// ... save only on changes
		if ($this->numeric AND (float) $last != (float) $value OR
		    (string) $last != (string) $value) {
			return parent::write($value, $timestamp);
		} else {
			return 0;
		}
	}

}
