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
		$org->find('guid', $channel->channel);

		if ($org->id != '') {
			parent::__construct($org);
			return;
		}

		throw new Exception('No channel found for GUID: '.$channel->channel, 400);
	}

	/**
	 *
	 */
	public function read( $request, $attributes=FALSE ) {
		// Simply pass-through
		return $this->after_read(\Channel::byGUID($this->guid)->read($request), $attributes);
	}

}
