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
	public static function help() {
	    return array(
			'[GET] /api/r2/attributes/:guid' => array(
				'description' => 'Read channel attributes',
				'parameters'  => array(
					'attribute' => 'Get only the requested attribute'
				),
			),
		);
	}

	/**
	 *
	 */
	public function GET( &$request ) {
		$channel = \Channel::byGUID($this->GUID);
		$attribute = isset($request['attribute']) ? $request['attribute'] : NULL;
		return $channel->getAttributes($attribute);
	}

}