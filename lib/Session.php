<?php
/**
 * Session handling class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
abstract class Session {

    /**
     *
     * @var bool $debug
     */
    public static $debug = false;

    /**
     *
     * @var array $Messages
     */
    public static $Messages = array();

    /**
     *
     * @var mixed $NVL
     */
    public static $NVL = null;

    /**
     * Set session save path
     *
     * @param string $path
     * @return void
     */
    public static function setSavePath( $path ) {
        self::__dbg('Set save path to "%s"', $path);
        session_save_path($path);
    }

    /**
     * Set a signer for session data
     *
     * @param ISigner $signer
     * @return void
     */
    public static function setSigner( ISigner $signer ) {
        self::__dbg('Set signer to a instance of "%s"', get_class($signer));
        self::$__signer = $signer;
    }

    /**
     * Set functions to handle e.g. session file access
     *
     * @param string $open Function on open session
     * @param string $close Function on close session
     * @param string $read Function on read session data
     * @param string $write Function on write session data
     * @param string $destroy Function on destroying session
     * @param string $gc Function on garbage collection
     * @return void
     */
    public static function SetHandler( $open, $close, $read, $write, $destroy, $gc) {
        session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
    }

    /**
     * Is a session active
     *
     * @return bool
     */
    public static function active() {
        return (session_id() != '');
    }

    /**
     * Start session
     *
     * @param string $name New session name
     * @param int $ttl Time to live for session cookie
     * @param string $path Used to restrict where the browser sends the cookie
     * @param string $domain Used to allow subdomains access to the cookie
     * @param bool $secure If TRUE the browser only sends the cookie over https
     * @return void
     */
    public static function start( $name=null, $ttl=0, $path='/', $domain=NULL, $secure=NULL ) {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'cli';

        if ($name) {
            self::__dbg('Set name to "%s"', $name);
            $name = session_name($name);
            self::__dbg('Old name was "%s"', $name);
        }

        // Set SSL level
        $https = $secure ?: (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']);

        session_set_cookie_params($ttl, $path, $domain, $https, true);

        self::__start();

//         // Make sure the session hasn't expired and is valid ...
//         if (self::validate()) {
//             // Give a 5% chance of the session id changing on any request
//             if (rand(1, 100) <= 5) self::regenerate();
//         } else {
//             // ... and destroy it if it has
//             self::destroy();
//             self::__start();
//         }

        self::__dbg('Started "%s" = "%s"', session_name(), session_id());

        if (count(self::$__buffer)) {
            foreach(self::$__buffer as $key=>$value) {
                $key = strtolower($key);
                if (isset($_SESSION[$key]) AND is_array($_SESSION[$key])) {
                    $_SESSION[$key] = array_merge($_SESSION[$key], $value);
                } else {
                    $_SESSION[$key] = $value;
                }
            }
            self::$__buffer = array();
        }
    }

    /**
     * Update the current session id with a newly generated one
     *
     * @return bool Success
     */
    public static function regenerate() {
        return session_regenerate_id(true);
    }

    /**
     * Close the session
     *
     * Write the session data
     *
     * @return void
     */
    public static function close()
    {
        return @session_write_close();
    }

    /**
     * Destroy the session
     *
     * @see close()
     * @return void
     */
    public static function destroy()
    {
        self::__dbg('Destroy "%s" = "%s"', session_name(), session_id());
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-4200,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        $_SESSION = array(); // destroy all of the session variables
        self::close();
        return @session_destroy();
    }

    /**
     * checkRequest, set session var to requested value or to a default
     *
     * Check if $param is member of $_REQUEST, if not, set to $default and
     * save this param to $_SESSION
     *
     * @param string $param Request parameter
     * @param mixed $default Default value
     * @return mixed
     */
    public static function checkRequest( $param, $default=NULL ) {
        if (array_key_exists($param, $_REQUEST)) self::set($param, $_REQUEST[$param]);
        if (!self::is_set($param)) self::set($param, $default);
        return self::get($param);
    }

    /**
     * Set a variable value into $_SESSION
     *
     * Deletes variable from session if value is NULL
     *
     * @see add()
     * @see get()
     * @param string $key Varibale name
     * @param mixed $val Varibale value
     * @return void
     */
    public static function set( $key, $val=NULL ) {
        $key = self::__mapKey($key);
        $_val = isset(self::$__signer) ? self::$__signer->sign($val) : $val;
        if (!self::active()) {
            self::$__buffer[$key] = $_val;
        } else {
            if (is_null($val)) {
                unset($_SESSION[$key]);
            } else {
                $_SESSION[$key] = $_val;
            }
        }
    }

    /**
     * Set a bunch of variables at once into $_SESSION
     *
     * Deletes variable from session if value is NULL
     *
     * @see set()
     * @param array $array Array of Variable => Value
     * @return void
     */
    public static function setA( $array ) {
        foreach ((array)$array as $key => $value) self::set($key, $value);
    }

    /**
     * Add a value to $_SESSION
     *
     * @param string $key Varibale name
     * @param mixed $val Varibale value
     * @return void
     */
    public static function add( $key, $val ) {
        $key = self::__mapKey($key);
        if (isset(self::$__signer)) $val = self::$__signer->sign($val);
        if (!self::active()) {
            self::$__buffer[$key][] = $val;
        } else {
            if (!isset($_SESSION[$key])) {
                $_SESSION[$key] = array();
            } elseif (!is_array($_SESSION[$key])) {
                $_SESSION[$key] = array($_SESSION[$key]);
            }
            $_SESSION[$key][] = $val;
        }
    }

    /**
     * Get a value from a $_SESSION variable, return $default if not set
     *
     * @see set()
     * @param string $key Variable name
     * @param mixed $default Return if $var not set
     * @param bool $clear Remove data
     * @return mixed
     */
    public static function get( $key, $default=NULL, $clear=FALSE ) {
        $key = self::__mapKey($key);
        if (isset($_SESSION[$key])) {
            $val = $_SESSION[$key];
            if (isset(self::$__signer)) $val = self::$__signer->get($val);
        } elseif (isset($default)) {
            $val = $default;
        } else {
            $val = self::$NVL;
        }
        if ($clear) unset($_SESSION[$key]);
        return $val;
    }

    /**
     * Get a value from $_SESSION and deletes it
     *
     * @param string $key Varibale name
     * @return mixed
     */
    public static function takeout( $key ) {
        $val = self::get($key);
        self::delete($key);
        return $val;
    }

    /**
     * Remove a value from $_SESSION
     *
     * @param string $var Varibale name
     * @return void
     */
    public static function delete( $var ) {
        self::set($var);
    }

    /**
     * Check if a $_SESSION variable is set
     *
     * @param string $key Varibale name
     * @return bool
     */
    public static function is_set( $key ) {
        return array_key_exists(self::__mapKey($key), $_SESSION);
    }

    /**
     * Check if a $_SESSION variable is set
     *
     * @param string $key Varibale name
     * @return bool
     */
    public static function token() {
        if (!isset($_SESSION['_TOKEN']))
            $_SESSION['_TOKEN'] = md5($_SERVER['HTTP_USER_AGENT'].__FILE__);
        return $_SESSION['_TOKEN'];
    }

    //---------------------------------------------------------------------------
    // PRIVATE
    //---------------------------------------------------------------------------

    /**
     * Data container
     *
     * @var array $__buffer
     */
    private static $__buffer = array();

    /**
     * Data signer
     *
     * @var array $__signer
     */
    private static $__signer = NULL;

    /**
     * Transform $key for common use
     *
     * @param string $key
     */
    private static function __mapKey( $key ) {
        return strtolower($key);
    }

    /**
     * starts session and some statements to fix bugs in IE and PHP < 4.3.3
     */
    private static function __start() {
        session_start();
        // to overcome/fix a bug in IE 6.x
        Header('Cache-control: private');
        // from http://php.net/manual/function.session-regenerate-id.php
        // UCN from Gant at BleachEatingFreaks dot com, 24-Jan-2006 09:57
        if (version_compare(PHP_VERSION, '4.3.3', '<')) {
            setCookie( session_name(), session_id(), ini_get('session.cookie_lifetime'));
        }
        // random suffix + file location
        $_SESSION['_HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'].__FILE__);
    }

    /**
     * Collect debug infos
     */
    private static function __dbg() {
        if (!self::$debug) return;

        $params = func_get_args();
        $msg = array_shift($params);
        self::$Messages[] = vsprintf($msg, $params);
    }
}
