<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace API\r2;

/**
 *
 */
class Attributes extends Handler {

	/**
	 *
	 */
	public function GET( &$request ) {
		$channel = \Channel::byGUID($this->GUID);
		$attributes = $channel->getAttributes(isset($request[0]) ? $request[0] : '');
#		unset($attributes['consumption'], $attributes['costs']);
		return $attributes;
	}

}
