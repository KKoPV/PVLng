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
class App {

	/**
	 *
	 */
	public static function Run() {

		try {

			$config = Config::getInstance()
			          // Load global config
			          ->load(ROOT_DIR . DS . 'config'. DS . 'config.app.php');

			$class = $config->get('Router.Class', 'Router');

			$router = Router::getInstance();

			if (!$router->Controller OR !$router->Action)
				throw new Exception('Missing route definition.');

			$class = $router->Controller . '_Controller';
			if (!class_exists($class)) $class = $config->get('Default.Controller');
			$controller = new $class;

			$controller->router = $router;

			// Model for this controller?
			$class = $router->Controller . '_Model';
			if (!class_exists($class)) $class = $config->get('Default.Model');
			$controller->model = new $class;

			// View for this controller
			$class = $router->Controller . '_View';
			if (!class_exists($class)) $class = $config->get('Default.View');
			$controller->view = new $class;

			$controller->view->router = $router;

			do {

				if ($router->isPost()) {
					if ($controller->before_POST() === FALSE) break;
				} else {
					if ($controller->before_GET() === FALSE) break;
				}

				// 1st Before
				if ($controller->before() === FALSE) break;

				do {

					$ActualAction = $router->Action;

					// 2nd Get or Post action method (optional)
					$method = $router->Action
									. ($router->isPost() ? '_Post' : '_Get')
									. '_Action';

#					\Messages::Info($method . ' (' .((int) method_exists($controller, $method)). ')');

					if (method_exists($controller, $method)) $controller->$method();

					if ($ActualAction != $router->Action) continue;

					// 3rd Common action method (optional)
					$method = $router->Action . '_Action';

#					\Messages::Info($method . ' (' .((int) method_exists($controller, $method)). ')');

					if (method_exists($controller, $method)) $controller->$method();

				} while ($ActualAction != $router->Action);

				if ($router->isPost()) {
					$controller->after_POST();
				} else {
					$controller->after_GET();
				}

				// 4th After
				$controller->after();

			} while (0);

			// Output view content
			$controller->view->output();

		} catch (Exception $e) {
			echo '<pre>', $e->getMessage();
		}
	}

}

/**
 *
 */
class Exception extends \Exception {}

defined('BASE_DIR') || define('BASE_DIR', $_SERVER['DOCUMENT_ROOT']);
