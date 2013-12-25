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
		if (!self::$save) self::$save = new Log;
		self::$save->scope = $scope;
		self::$save->data = (string) $data;
		self::$save->insert();
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $table = 'pvlng_log';

	/**
	 *
	 */
	protected static $save;

}
