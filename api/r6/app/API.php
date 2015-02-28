<?php
/**
 * API class
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
class API extends Slim\Slim {

    /**
     * Get named parameter as string
     */
    public function strParam( $name, $default='' ) {
        $value = trim($this->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     * Get named parameter as integer
     */
    public function intParam( $name, $default=0 ) {
        $value = trim($this->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     * Get named parameter as boolean, all of (true|on|yes|1) interpreted as TRUE
     */
    public function boolParam( $name, $default=FALSE ) {
        $value = strtolower(trim($this->request->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }

    /**
     *
     */
    public function stopAPI( $message, $code=400 ) {
        $this->status($code);
        $this->response()->header('X-Status-Reason', $message);
        $this->render(array( 'status'=>$code<400?'success':'error', 'message'=>$message ));
        $this->stop();
    }
}
