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
	public function write( $request, $timestamp=NULL ) {
		// Get last state and ...
		$last = $this->getLastReading();

		$value = isset($request['data']) ? $request['data'] : NULL;

		// ... save only on changes
		if ($this->numeric AND (float) $last  != (float) $value OR
		    /* string */       (string) $last != (string) $value) {
			return parent::write($request, $timestamp);
		}
		return 0;
	}

}
