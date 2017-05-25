<?php
/**
 * Mockup class with no persistent functionality, cache only for
 * actual script run
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
class Mock extends Cache
{

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Cache availability
     *
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * Write raw data in cache
     *
     * @param string $key Unique cache Id
     * @param string $data
     * @return bool
     */
    public function write($key, $data, $ttl)
    {
        $this->data[$key] = $data;
        return true;
    }

    /**
     * Retrieve raw data from cache
     *
     * @param string $key Unique cache Id
     * @return string
     */
    public function fetch($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Delete data from cache
     *
     * @param string $key Unique cache Id
     * @return bool
     */
    public function delete($key)
    {
        unset($this->data[$key]);
        return true;
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function flush()
    {
        $this->data = array();
        return true;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $data = array();
}
