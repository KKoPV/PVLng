<?php
/**
 * Class File
 *
 * Store all data into one file
 * All data will be held in memeory during the script runs
 *
 * The following settings are supported:
 * - Token     : used to build unique cache file (optional)
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
class File extends AbstractFile {

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Cache availability
     *
     * @return bool
     */
    public function isAvailable() {
        if (parent::isAvailable()) {
            $this->filename = $this->FileName(__FILE__);
            // Load cached data
            if ($data = $this->ReadFile($this->filename)) {
                $this->data = $data;
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Write raw data in cache
     *
     * @param string $key Unique cache Id
     * @param string $data
     * @return bool
     */
    public function write( $key, $data ) {
        $this->data[$key] = $data;
        $this->modified = TRUE;
    } // function write()

    /**
     * Retrieve raw data from cache
     *
     * @param string $key Unique cache Id
     * @return string
     */
    public function fetch( $key, $expire=0 ) {
        return array_key_exists($key, $this->data) ? $this->data[$key] : NULL;
    } // function fetch()

    /**
     * Delete data from cache
     *
     * @param string $key Unique cache Id
     * @return bool
     */
    public function delete($key) {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
            $this->modified = TRUE;
        }
    } // function delete()

    /**
     * Clear cache
     *
     * @return bool
     */
    public function flush() {
        parent::flush();
        return $this->RemoveFile($this->filename);
    } // function flush()

    /**
     * Class destructor
     *
     * Save changes to file if modified
     */
    public function __destruct() {
        // Save only if data was modified
        if ($this->modified) {
            $this->WriteFile($this->filename, $this->data);
        }
    } // function __destruct()

    /**
     *
     */
    public function info() {
        $info = parent::info();
        $info['FileName'] = $this->filename;
        $info['Count'] = count($this->data);
        return $info;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * File name
     *
     * @var string $filename
     */
    protected $filename;

    /**
     * Save whole cache file only if at least one id was changed/deleted
     *
     * @var bool $modified
     */
    protected $modified = FALSE;

}
