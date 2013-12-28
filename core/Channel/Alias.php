<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class Alias extends \Channel {

	/**
	 *
	 */
	protected function __construct( \ORM\Tree $channel ) {

		$org = new \ORM\Tree;
		$org->find('id', $channel->alias_of);

		if ($org->id) return parent::__construct($org);

		throw new \Exception('No aliased channel found', 400);
	}

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {
		// Simply pass-through
		return $this->after_read(\Channel::byGUID($this->guid)->read($request), $attributes);
	}

}
