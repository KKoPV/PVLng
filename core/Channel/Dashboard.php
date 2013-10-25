<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-14-g2a8e482 2013-05-01 20:44:21 +0200 Knut Kohl $
 */
namespace Channel;

/**
 *
 */
class Dashboard extends Calculator {

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {

		$child = $this->getChild(1);

		// Get some properties from child
		$this->meter = $child->meter;

		// Set some proerties of child
		// If valid range is smaller than dashbord should display,
		// extend to at least dashboard range
		// e.g. performance ratio for graphs 95 .. 100, but dashboard 50 .. 100
		if (!is_null($child->valid_from) AND $child->valid_from > $this->valid_from) {
			$child->valid_from = $this->valid_from;
		}
		if (!is_null($child->valid_to) AND $child->valid_to < $this->valid_to) {
			$child->valid_to = $this->valid_to;
		}

		// Simply pass-through
		return $this->after_read($child->read($request), $attributes);
	}

}
