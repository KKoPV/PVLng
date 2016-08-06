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
class App extends \Slim\Slim {

    /**
     *
     */
    public $params;

    /**
     * Constructor
     * @param  array $userSettings Associative array of application settings
     */
    public function __construct( Array $userSettings=array() ) {
        parent::__construct($userSettings);

        $this->params = new \Slim\Helper\Set;
        $this->view(new View);
    }

    /**
     * Overwrite
     */
    public function foreward( $action ) {
        $this->action = $action;
    }

    /**
     * Overwrite
     */
    public function redirect($url='/', $status=302)
    {
        \Session::close();
        parent::redirect($url, $status);
    }

    /**
     * Process controller action
     *
     * @param  string $class Controller class
     * @param  string $action Controller action
     * @return void
     */
    public function process( $class='Index', $action='Index', $params=array() ) {

        $this->params->replace($params);

        $class = '\Controller\\' . $class;

        // Don't check existance, app. error is ok here
        $controller = new $class;

        $reqMethod = strtoupper($this->request->getMethod());

        $controller->before();

        $method = 'before' . $reqMethod;
        $controller->$method();

        $this->action = $action;

        do {

            $action = $this->action;

            $method = $action . $reqMethod . '_Action';
            if (method_exists($controller, $method)) $controller->$method();

            // Display only actions
            $method = $action . '_Action';
            if (method_exists($controller, $method)) $controller->$method();

        } while ($action != $this->action);

        $method = 'after' . $reqMethod;
        $controller->$method();

        $controller->after();

        $controller->finalize($action);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $action;

}
