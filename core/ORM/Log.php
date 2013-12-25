<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

/**
 *
 */
namespace ORM;

/**
 *
 */
class Log extends \slimMVC\ORMTable {

	public static function save( $scope, $data ) {
		$log = new Log;
		$log->scope = $scope;
		$log->data = (string) $data;
		$log->insert();
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $table = 'pvlng_log';

}
