<?php
/**
 * Main program file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace slimMVC;

/**
 *
 */
use Core\Hook;
use Core\Session;
use Slim\Helper\Set;
use Slim\Slim;

/**
 *
 */
class App extends Slim
{

    /**
     *
     */
    public $params;

    /**
     * Constructor
     * @param  array $userSettings Associative array of application settings
     */
    public function __construct(array $userSettings = array())
    {
        parent::__construct($userSettings);

        $this->params = new Set;
        $this->view(new View);
    }

    /**
     * Overwrite
     */
    public function foreward($action)
    {
        Hook::run('frontend.foreward', $this);
        $this->action = $action;
    }

    /**
     * Overwrite
     */
    public function redirect($url = '/', $status = 302)
    {
        Hook::run('frontend.redirect', $this);
        Session::close();
        parent::redirect($url, $status);
    }

    /**
     * Process controller action
     *
     * @param  string $controller Controller class
     * @param  string $action     Controller action
     * @return void
     */
    public function process($controller = 'Index', $action = 'Index', $params = array())
    {
        $this->params->replace($params);

        Hook::run(['Controller', $controller, 'before'], $this);

        // Don't check existance, app. error is ok here
        $class = '\Frontend\Controller\\' . $controller;
        $oController = new $class;

        $requestMethod = strtoupper($this->request->getMethod());

        $oController->before();

        $method = 'before' . $requestMethod;
        $oController->$method();

        $this->action = $action;

        do {
            // Remember actual action to detect forwarding
            $action = $this->action;

            Hook::run(['Controller', $controller, $action, 'before'], $this);
            Hook::run(['Controller', $controller, $action, 'before', $requestMethod], $this);

            $method = $action . $requestMethod . 'Action';
            if (method_exists($oController, $method)) {
                $oController->$method();
            }

            // Display only actions
            $method = $action . 'Action';
            if (method_exists($oController, $method)) {
                $oController->$method();
            }
        } while ($action != $this->action);

        $method = 'after' . $requestMethod;
        $oController->$method();

        Hook::run(['Controller', $controller, $action, 'after', $requestMethod], $this);

        $oController->after();

        Hook::run(['Controller', $controller, $action, 'after'], $this);
        Hook::run(['Controller', $controller, 'after'], $this);

        $oController->finalize($action);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $action;
}
