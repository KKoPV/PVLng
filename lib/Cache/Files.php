<?php
/**
 * Class Files
 *
 * Store data for each id in a separate file, recommended for large data sets
 * The data are not in memory, they will read each time they are required.
 *
 * The following settings are supported:
 * - Token     : used to build unique cache files (optional)
 * - Directory : Where to store the file with the cached data (optional)
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
class Files extends AbstractFile {

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Write raw data in cache
     *
     * @param string $key Unique cache Id
     * @param string $data
     * @return bool
     */
    public function write( $key, $data, $ttl ) {
        // Buffer for next read
        $this->data[$key] = $data;
        return $this->WriteFile($this->FileName($key, '.single.cache'), $data);
    } // function write()

    /**
     * Retrieve raw data from cache
     *
     * @param string $key Unique cache Id
     * @return string
     */
    public function fetch( $key ) {
        if (array_key_exists($key, $this->data)) {
            $this->hits++;
        } else {
            $this->misses++;
            // Buffer for more reads in this session
            $this->data[$key] = $this->ReadFile($this->FileName($key, '.single.cache'));
        }
        return $this->data[$key];
    } // function fetch()

    /**
     * Delete data from cache
     *
     * @param string $key Unique cache Id
     * @return bool
     */
    public function delete( $key ) {
        return $this->RemoveFile($this->FileName($key, '.single.cache'));
    } // function delete()

    /**
     * Clear cache
     *
     * @return bool
     */
    public function flush() {
        parent::flush();

        $ok = TRUE;
        foreach (glob($this->FileName('*', '.single.cache')) as $file) {
            $ok = ($ok AND $this->RemoveFile($file));
        }
        return $ok;
    } // function flush()

    /**
     *
     */
    public function info() {
        $info = parent::info();
        $info['buffered'] = count($this->data);
        return $info;
    }

    public function getHits() {
        return $this->hits;
    }

    public function getMisses() {
        return $this->misses;
    }

    protected $hits   = 0;
    protected $misses = 0;
}
