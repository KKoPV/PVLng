<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
abstract class Controller extends Base {

	/**
	 *
	 */
	public $model;

	/**
	 *
	 */
	public $view;

	/**
	 *
	 */
	public $router;

	/**
	 *
	 * /
	public function __construct() {
		parent::__construct();
	}

	/**
	 *
	 */
	public function request( $key=NULL, $default=NULL ) {
		return $this->router->request($key, $default);
	}

	/**
	 *
	 */
	public function foreward( $action='Index' ) {
		$this->router->Action = $action;
	}

	/**
	 *
	 */
	public function redirect( $controller='', $action='', $params=array() ) {
		header('Location: '.$this->router->URL($controller?:'index', $action, $params));
		exit;
	}

	/**
	 * Overwrite if required
	 */
	public function before_GET() {}

	/**
	 * Overwrite if required
	 */
	public function before_POST() {}

	/**
	 * Overwrite if required
	 */
	public function before() {}

	/**
	 * Overwrite if required
	 */
	public function after_GET() {}

	/**
	 * Overwrite if required
	 */
	public function after_POST() {}

	/**
	 * Overwrite if required
	 */
	public function after() {}

}
