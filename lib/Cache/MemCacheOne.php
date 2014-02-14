<?php
/**
 * Cache class using MemCache server
 *
 * The following settings are supported:
 * - Token    : used to build unique cache Ids (optional)
 * - MemCache : <host>[:<port>] (optional) default: localhost:11211
 *
 * If MemCache is not installed, gMemCache is used,
 * a purely implementation of a MemCache client in PHP.
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2010-2013 Knut Kohl
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version    1.0.0
 */
namespace Cache;

/**
 *
 */
class MemCacheOne extends \Cache {

    /**
     * Default server
     */
    const HOST = '127.0.0.1';

    /**
     * Default port
     */
    const PORT = 11211;

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * The following additional settings are supported:
     * - @c host : MemCache host:port (optional)
     *
     * @copydoc Cache::__construct()
     */
    public function __construct( $settings=array() ) {
        parent::__construct($settings);

        $host = isset($this->settings['MemCache']) ? $this->settings['MemCache'] : self::HOST;

        if (strstr($host, ':')) {
            list($this->host, $this->port) = explode(':', $host, 2);
        } else {
            $this->host = $host;
            $this->port = self::PORT;
        }

        if (extension_loaded('memcache')) {
            $this->memcache = new \MemCache;
        } else {
            // use gMemCache
            include_once __DIR__ . DIRECTORY_SEPARATOR . 'gMemCache.php';
            $this->memcache = new \gMemCache;
        }
    }

    /**
     * @name Implemented abstract functions
     * @{
     */
    public function isAvailable() {
        if (!$this->memcache->connect($this->host, $this->port)) return FALSE;

        $data = $this->memcache->get($this->key(__FILE__));
        $this->data = is_array($data) ? $data : array();
        $this->modified = FALSE;

        return TRUE;
    }

    public function write( $key, $data ) {
        $key = strtolower($key);
        $this->data[$key] = $data;
        $this->modified = TRUE;
        return TRUE;
    }

    public function fetch( $key ) {
        $key = strtolower($key);
        return array_key_exists($key, $this->data) ? $this->data[$key] : NULL;
    }

    public function delete( $key ) {
        $key = strtolower($key);
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
            $this->modified = TRUE;
            return TRUE;
        }
        return FALSE;
    }

    public function flush() {
        $this->data = array();
        $this->modified = TRUE;
        return TRUE;
    }

    public function info() {
        return array_merge(parent::info(), $this->memcache->getStats());
    }

    /**
     * @name Overloaded functions
     * Use MemCache own functions
     * @{
     */
    public function inc( $key, $step=1 ) {
        return $this->memcache->increment($this->key($key), $step);
    } // function inc()

    public function dec( $key, $step=1 ) {
        return $this->memcache->decrement($this->key($key), $step);
    } // function dec()
    /** @} */

    /**
     * Class destructor
     *
     * Save changes to file if modified
     */
    public function __destruct() {
        // Save only if data was modified
        if ($this->modified) {
            $this->memcache->set($this->key(__FILE__), $this->data);
        }
        $this->memcache->close();
    } // function __destruct()

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * MemCache instance
     *
     * @var string $host
     */
    protected $host;

    /**
     * MemCache instance
     *
     * @var int $port
     */
    protected $port;

    /**
     * MemCache instance
     *
     * @var MemCache $memcache
     */
    protected $memcache;

    /**
     * Track cache mdification to detect need of save at the end
     *
     * @var bool $modified
     */
    protected $modified;

    /**
     * Data cache
     *
     * @var array $data
     */
    protected $data;

}
