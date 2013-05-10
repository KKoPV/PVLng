<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-24-gffc9108 2013-05-05 22:20:01 +0200 Knut Kohl $
 */
namespace yMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-24-gffc9108 2013-05-05 22:20:01 +0200 Knut Kohl $
 */
class Router {

	/**
	 *
	 */
	public $Route;

	/**
	 *
	 */
	public $Format;

	/**
	 *
	 */
	public $Controller;

	/**
	 *
	 */
	public $Action;

	/**
	 *
	 */
	public static function getInstance() {
		if (!self::$Instance) {
			self::$Instance = new Router;
		}
		return self::$Instance;
	}

	/**
	 *
	 */
	public final function request( $key=NULL, $default=NULL ) {
		return isset($key)
		     ? ( (isset($this->request[$key]) AND $this->request[$key] != '')
		       ? $this->request[$key]
		       : $default
			   )
		     : $this->request;
	}

	/**
	 *
	 */
	public function URL( $controller='', $action='', $params=array() ) {
		$url = '/' . ($controller?:'index') . '/' . $action;
        if (!empty($params)) {
			if (array_key_exists('#', $params)) {
				$anchor = '#' . $params['#'];
				unset($params['#']);
			} else {
				$anchor = '';
			}
			$url .= '?'.http_build_query($params).$anchor;
		}
		return $url;
	}

	/**
	 *
	 */
	public function isPost() {
		return (isset($_SERVER['REQUEST_METHOD']) AND
		        strtoupper($_SERVER['REQUEST_METHOD']) == 'POST');
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $request = array();

	/**
	 *
	 */
	protected function getMatch( $route ) {
		foreach ($this->Routes as $regex=>$data) {
			if (preg_match($regex, $route, $matches)) {
				// Defaults
				$handler = array(
					'Controller' => $data[0][0],
					'Action'     => $data[0][1],
				);
				// Named parameters
				foreach ($data[1] as $id=>$param) {
					if (isset($matches[$id+1])) $handler[$param] = $matches[$id+1];
				}
				return $handler;
			}
		}
	}

	// -------------------------------------------------------------------------
	// PRIVATE
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	private static $Instance;

	/**
	 *
	 */
	private $config;

	/**
	 *
	 */
	private function __construct() {

		$this->Routes = array();

		$this->config = Config::getInstance();

		$routes = array();

		foreach (glob(APP_DIR . DS . '*' . DS . 'routes.php') as $file) {
			$routes = array_merge($routes, include $file);
		}

		foreach ($routes as $route=>$data) {

			$parameters = $matchUrl = array();

			foreach (explode('/', $route) as $expl) {
				if (preg_match('~:(\w+)(\?)?~', $expl, $args)) {
					// Named (string) parameter
					if (!isset($args[2])) $args[2] = '';
					$expl = $args[2].'([^/]+)'.$args[2];
					$parameters[] = $args[1];
				} elseif (preg_match('~#(\w+)(\?)?~', $expl, $args)) {
					// Named (numeric) parameter
					if (!isset($args[2])) $args[2] = '';
					$expl = $args[2].'(\d+)'.$args[2];
					$parameters[] = $args[1];
				} elseif (preg_match('~\*~', $expl)) {
					// Catch all parts
					$expl = '?(.*)';
					$parameters[] = '*';
				}
				$matchUrl[] = $expl;
			}
			$matchUrl = '~^'.implode('/', $matchUrl).'$~';
			$this->Routes[$matchUrl] = array($data, $parameters);
		}

		$this->Route = isset($_SERVER['PATH_INFO'])
		             ? trim($_SERVER['PATH_INFO'], '/')
		             : '';

		if (preg_match('~^(.+)\.([^.]+)$~', $this->Route, $args)) {
			$this->Route = $args[1];
			$this->Format = strtoupper($args[2]);
		} else {
			$this->Format = 'HTML';
		}
		//	$this->Route = preg_replace('~\.[^.]*$~', '', $this->Route);

		if ($data = $this->getMatch($this->Route)) {
			$this->Controller = $data['Controller'];
			$this->Action		 = $data['Action'];
			foreach ($data as $key=>$value) {
				$this->request[$key] = $value;
			}
		} elseif ($e = $this->config->get('Router.ErrorRoute') AND
		          $data = $this->getMatch($e)) {
			$this->Controller = $data['Controller'];
			$this->Action		 = $data['Action'];
		}

		$this->request = array_merge($this->request, $_GET, $_POST);
	}

	/**
	 *
	 */
	private function __clone() {}

}