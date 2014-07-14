<?php
/**
 * Cache class using APC opcode cache
 *
 * For more information see http://www.php.net/manual/book.apc.php
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
class APC extends \Cache {

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Cache availability
     *
     * @return bool
     */
    public function isAvailable() {
        return (extension_loaded('apc') AND ini_get('apc.enabled'));
    }

    /**
     * Write raw data in cache
     *
     * @param string $key Unique cache Id
     * @param string $data
     * @return bool
     */
    public function write( $key, $data ) {
        return apc_store($this->key($key), $this->serialize($data), $data[1]);
    }

    /**
     * Retrieve raw data from cache
     *
     * @param string $key Unique cache Id
     * @return string
     */
    public function fetch( $key ) {
        $data = apc_fetch($this->key($key));

        return ($data === FALSE) ? NULL : $this->unserialize($data);
    }

    /**
     * Delete data from cache
     *
     * @param string $key Unique cache Id
     * @return bool
     */
    public function delete( $key ) {
        return apc_delete($this->key($key));
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function flush() {
        return (apc_clear_cache() && apc_clear_cache('user'));
    }

    /**
     * Increments value of an item by the specified value.
     *
     * If item specified by key was not numeric and cannot be converted to a
     * number, it will change its value to value.
     *
     * inc() does not create an item if it doesn't already exist.
     *
     * @param string $key Unique cache Id
     * @param numeric $step
     * @return numeric|bool New items value on success or FALSE on failure.
     */
    public function inc( $key, $step=1 ) {
        return apc_inc($this->key($key), $step);
    } // function inc()

    /**
     * Decrements value of the item by value.
     *
     * If item specified by key was not numeric and cannot be converted to a
     * number, it will change its value to value.
     *
     * dec() does not create an item if it doesn't already exist.
     *
     * Similarly to inc(), current value of the item is being converted to
     * numerical and after that value is substracted.
     *
     * @param string $key Unique cache Id
     * @param numeric $step
     * @return numeric|bool New items value on success or FALSE on failure.
     */
    public function dec( $key, $step=1 ) {
        return apc_dec($this->key($key), $step);
    } // function dec()

    public function info( $full=FALSE ) {
        $return = parent::info();
        if ($full) $return = array_merge($return, apc_cache_info());
        return array_merge($return, apc_sma_info());
    } // function info()

    public function getHits() {
        $stats = apc_cache_info();
        return $stats['num_hits'];
    }

    public function getMisses() {
        $stats = apc_cache_info();
        return $stats['num_misses'];
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * Build internal Id from external Id and the cache token
     *
     * @param string $key Unique cache Id
     * @return string
     */
    protected function key( $key ) {
        return $this->settings['Token'].'.'.$key;
    } // function key()

}
