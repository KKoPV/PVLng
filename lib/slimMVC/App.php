<?php
/**
 * Main program file
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
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
     *
     */
    public function foreward( $action ) {
        $this->action = $action;
    }

    /**
     * Process controller action
     *
     * @param  string $class Controller class
     * @param  string $action Controller action
     * @return void
     */
    public function process( $class='Index', $action='Index' ) {

        $class = '\Controller\\' . $class;

        // Don't check existance, app. error is ok here
        $controller = new $class;

        $reqMethod = strtoupper($this->request->getMethod());

        $method = 'before' . $reqMethod;
        if (method_exists($controller, $method)) $controller->$method();

        $method = 'before';
        if (method_exists($controller, $method)) $controller->$method();

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
        if (method_exists($controller, $method)) $controller->$method();

        $method = 'after';
        if (method_exists($controller, $method)) $controller->$method();

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
