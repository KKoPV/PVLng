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
class Help extends Handler {

	/**
	 *
	 */
	public function GET( &$request ) {
		$request['format'] = 'json';

		return json_decode(file_get_contents(__DIR__ . DS . 'Help.json'));
	}

}