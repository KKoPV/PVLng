<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace slimMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
abstract class Controller {

    /**
     *
     */
    public function __construct() {
        $this->app = App::getInstance();
    }

    /**
     *
     */
    public function before() {
    }

    /**
     *
     */
    public function after() {
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $app;

    /**
     *
     */
    protected function strParam( $name, $default ) {
        $value = trim($this->app->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     *
     */
    protected function intParam( $name, $default ) {
        $value = trim($this->app->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     *
     */
    protected function boolParam( $name, $default ) {
        $value = strtolower(trim($this->app->request->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }
}
