<?php
/**
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
    public function before() {}

    /**
     *
     */
    public function beforeGET() {}

    /**
     *
     */
    public function beforePOST() {}

    /**
     *
     */
    public function afterGET() {}

    /**
     *
     */
    public function afterPOST() {}

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
        $value = trim($this->app->Request()->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     *
     */
    protected function intParam( $name, $default ) {
        $value = trim($this->app->Request()->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     *
     */
    protected function boolParam( $name, $default ) {
        $value = strtolower(trim($this->app->Request()->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }
}
