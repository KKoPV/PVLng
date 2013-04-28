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
class ViewHelper {

	/**
	 *
	 */
	public function callable($method) {
		return isset($this->closures[$method]);
	}

	/**
	 *
	 */
	public function __set($method, $closure) {
		$this->closures[$method] = $closure;
	}

	/**
	 *
	 */
	public function __call($method, $args) {
		if (isset($this->closures[$method])) {
			return call_user_func_array($this->closures[$method], $args);
		}
	}

	/**
	 *
	 */
	protected $closures = array();

}
