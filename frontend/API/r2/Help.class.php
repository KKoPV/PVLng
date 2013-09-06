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
	public static function formats() {
	    return array( 'json', 'xml' );
	}

	/**
	 *
	 */
	public static function help() {
	    return array(
			'[GET] /api/r2/help' => array(
				'description' => 'This help, only JSON or XML supported',
			),
		);
	}

	/**
	 *
	 */
	public function GET( &$request ) {
		$help = array();

		foreach (glob(__DIR__ . DS . '*.class.php') as $file) {
			require_once $file;
			preg_match('~'.DS.'([^'.DS.']+).class.php~', $file, $args);
			if ($args[1] == 'Handler') continue;
			$class = __NAMESPACE__ . '\\' . $args[1];
			$help = array_merge($help, $class::help());
		}

		return $help;
	}

}
