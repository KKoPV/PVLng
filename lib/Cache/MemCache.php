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
class MemCache extends \Cache {

    /**
     * Default server
     */
    const HOST = 'localhost';

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
            require_once dirname(__FILE__).'/gMemCache.php';
            $this->memcache = new \gMemCache;
        }
    }

    /**
     * @name Implemented abstract functions
     * @{
     */
    public function isAvailable() {
        return $this->memcache->connect($this->host, $this->port);
    }

    public function write( $key, $data ) {
        return $this->memcache->set($this->key($key), $data);
    }

    public function fetch( $key ) {
        return $this->memcache->get($this->key($key));
    }

    public function delete( $key ) {
        return $this->memcache->delete($this->key($key));
    }

    public function flush() {
        return $this->memcache->flush();
    }

    public function info() {
        return array_merge(parrent::info(), $this->memcache->getStats());
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
     * Close connection
     */
    public function __destruct(){
        $this->memcache->close();
    }

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

}
