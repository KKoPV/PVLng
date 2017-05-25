<?php
/**
 * Session handling class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2016 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Core;

/**
 *
 */
abstract class Session
{
    /**
     *
     * @var string
     */
    const LOGIN = '_session_login';

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
     *
     */
    public static $sessionName = 'PHPSESSID';

    /**
     *
     * @var string Remember cookie name
     */
    public static $tokenName = 'token';

    /**
     * Set session save path
     *
     * @param string $path
     * @return void
     */
    public static function setSavePath($path)
    {
        self::debug('Set save path to "%s"', $path);
        session_save_path($path);
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
    public static function setHandler($open, $close, $read, $write, $destroy, $gc)
    {
        session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
    }

    /**
     * Is a session active
     *
     * @return bool
     */
    public static function active()
    {
        return (session_status() !== PHP_SESSION_NONE);
    }

    /**
     * Start session
     *
     * @param bool $regenerate Regenerate session id
     * @return void
     */
    public static function start($regenerate = true)
    {
        if (session_status() === PHP_SESSION_NONE) {
            // to overcome/fix a bug in IE 6.x
            Header('Cache-control: private');
            // from http://php.net/manual/function.session-regenerate-id.php
            // UCN from Gant at BleachEatingFreaks dot com, 24-Jan-2006 09:57
            if (version_compare(PHP_VERSION, '4.3.3', '<')) {
                setCookie( session_name(), session_id(), ini_get('session.cookie_lifetime'));
            }

            session_name(self::$sessionName);

            session_start();

            if (!isset($_SESSION['HTTP_USER_AGENT'])) {
                $_SESSION['HTTP_USER_AGENT'] = @$_SERVER['HTTP_USER_AGENT'] ?: 'cli';
            }

            // Check for valid Session;
            if (!isset($_SESSION['_token'])) {
                $_SESSION['_token'] = self::token();
            }

            self::debug('Started "%s" = "%s"', session_name(), session_id());

            if (count(self::$bufferedData)) {
                foreach (self::$bufferedData as $key => $value) {
                    $key = strtolower($key);
                    if (isset($_SESSION[$key]) && is_array($_SESSION[$key])) {
                        $_SESSION[$key] = array_merge($_SESSION[$key], $value);
                    } else {
                        $_SESSION[$key] = $value;
                    }
                }
                self::$bufferedData = array();
            }

            register_shutdown_function([__CLASS__, 'close']);
        }

        $regenerate && self::regenerate();
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function remember($lifetime)
    {
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(self::$tokenName, self::token(), time()+$lifetime,
                      $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
    }

    /**
     * Get onetime value
     *
     * @return bool
     */
    public static function remembered()
    {
        return isset($_COOKIE[self::$tokenName]) && ($_COOKIE[self::$tokenName] == self::token());
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function forget()
    {
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(self::$tokenName, '', time()-4200,
                      $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            unset($_COOKIE[self::$tokenName]);
        }
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function login($user)
    {
        return self::set(self::LOGIN, $user);
    }

    /**
     * Check if user is logged in, if remembered re-login
     *
     * @param string $user
     */
    public static function loggedIn($user)
    {
        if (self::get(self::LOGIN) === $user) {
            return true;
        }

        if (self::remembered()) {
            self::login($user);
            return true;
        }

        self::forget();
        return false;
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function logout()
    {
        self::delete(self::LOGIN);
        self::forget();
    }

    /**
     * Update the current session id with a newly generated one
     *
     * @return bool Success
     */
    public static function regenerate()
    {
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
        self::debug('Destroy "%s" = "%s"', session_name(), session_id());
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
    public static function checkRequest($param, $default = null)
    {
        if (array_key_exists($param, $_REQUEST)) {
            self::set($param, $_REQUEST[$param]);
        }
        if (!self::has($param)) {
            self::set($param, $default);
        }
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
    public static function set($key, $val = null)
    {
        $key = self::mapKey($key);
        if (!self::active()) {
            self::$bufferedData[$key] = $val;
        } else {
            $_SESSION[$key] = $val;
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
    public static function setA(array $array)
    {
        foreach ($array as $key => $value) {
            self::set($key, $value);
        }
    }

    /**
     * Add a value to $_SESSION
     *
     * @param string $key Varibale name
     * @param mixed $val Varibale value
     * @return void
     */
    public static function add($key, $val)
    {
        $key = self::mapKey($key);
        if (!self::active()) {
            self::$bufferedData[$key][] = $val;
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
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $key = self::mapKey($key);
        if (isset($_SESSION[$key])) {
            $val = $_SESSION[$key];
        } elseif (isset($default)) {
            $val = $default;
        } else {
            $val = self::$NVL;
        }
        return $val;
    }

    /**
     * Get a value from $_SESSION and deletes it
     *
     * @param string $key Varibale name
     * @return mixed
     */
    public static function takeout($key)
    {
        $val = self::get($key);
        self::delete($key);
        return $val;
    }

    /**
     * Remove a value from $_SESSION
     *
     * @param string $key Varibale name
     * @return void
     */
    public static function delete($key)
    {
        self::set($key);
    }

    /**
     * Check if a $_SESSION variable is set
     *
     * @param string $key Varibale name
     * @return bool
     */
    public static function has($key)
    {
        return array_key_exists(self::mapKey($key), $_SESSION);
    }

    /**
     * Check for valid session, came from correct origin
     *
     * @return bool
     */
    public static function valid()
    {
        return ($_SESSION['_token'] == self::token());
    }

    /**
     * Unique session token based on IP and user agent
     *
     * @return string
     */
    public static function token()
    {
        return md5($_SERVER['REMOTE_ADDR'].':'.@$_SERVER['HTTP_USER_AGENT']);
    }

    //---------------------------------------------------------------------------
    // PRIVATE
    //---------------------------------------------------------------------------

    /**
     * Data container
     *
     * @var array $bufferedData
     */
    private static $bufferedData = array();

    /**
     * Transform $key for common use
     *
     * @param string $key
     */
    private static function mapKey($key)
    {
        return strtolower($key);
    }

    /**
     * Collect debug infos
     */
    private static function debug()
    {
        if (!self::$debug) {
            return;
        }

        $params = func_get_args();
        $msg = array_shift($params);
        self::$Messages[] = vsprintf($msg, $params);
    }
}
