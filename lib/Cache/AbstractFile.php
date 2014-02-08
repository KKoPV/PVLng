<?php
/**
 * Class Cache_File
 *
 * Store all data into file
 * All data will be held in memeory during the script runs
 *
 * The following settings are supported:
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
abstract class AbstractFile extends \Cache {

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @param array $settings
     * @return void
     */
    public function __construct( $settings=array() ) {
        parent::__construct($settings);

        $this->cachedir = $this->settings['Directory'];

        // Auto detect cache directory
        // 1st use system temp. directory
        if ($this->cachedir == '') {
            $this->cachedir = sys_get_temp_dir();
        }
        // 2nd use upload temp. directory
        if ($this->cachedir == '') {
            $this->cachedir = ini_get('upload_tmp_dir');
        }

        $this->data = array();
    }

    /**
     * Cache availability
     *
     * @return bool
     */
    public function isAvailable() {
        return is_writable($this->cachedir);
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function flush() {
        $this->data = array();
    }

    /**
     * Some infos about the cache
     *
     * @return array
     */
    public function info() {
        $info = parent::info();
        $info['CacheDir'] = $this->cachedir;
        if (function_exists('memory_get_usage')) {
            $size = memory_get_usage();
            $a = array_merge($this->data);
            $info['Size'] = memory_get_usage() - $size;
        }
        return $info;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * Data storage
     *
     * @var array $data
     */
    protected $data;

    /**
     * Caching directory
     *
     * @var string $cachedir
     */
    protected $cachedir;

    /**
     * Bulid cache file name
     *
     * @param  string $key
     * @return string
     */
    protected function FileName( $key, $suffix='.cache' ) {
        return $this->cachedir . DIRECTORY_SEPARATOR . $this->key($key) . $suffix;
    } // function FileName()

    /**
     * Read data from cache file
     *
     * @param  string $file
     * @return string
     */
    protected function ReadFile( $file ) {
        // php.net suggested 'rb' to make it work under Windows
        if (!file_exists($file) OR !$fh = @fopen($file, 'rb')) return;
        // Get a shared lock
        @flock($fh, LOCK_SH);
        $data = '';
        // Be gentle, so read in 4k blocks
        while ($tmp = @fread($fh, 4096)) $data .= $tmp;
        // Release lock
        @flock($fh, LOCK_UN);
        @fclose($fh);
        // Return
        return $this->unserialize($data);
    } // function ReadFile()

    /**
     * Write data to cache file
     *
     * @param string $file
     * @param string $data
     * @return bool
     */
    protected function WriteFile( $file, $data ) {
        // Remove file for empty data
        if ($data == '' AND $this->RemoveFile($file)) return TRUE;

        // Lock file, ignore warnings as we might be creating this file
        if (file_exists($file) AND $fh = @fopen($file, 'rb')) {
            @flock($fh, LOCK_EX);
        }

        // php.net suggested 'wb' to make it work under Windows
        if ($fh = @fopen($file, 'wb')) {
            // Lock file exclusive for write
            @flock($fh, LOCK_EX);
            // Write data and check success
            $data = $this->serialize($data);
            $ok = (@fwrite($fh, $data, strlen($data)) !== FALSE);
            // Release lock
            @flock($fh, LOCK_UN);
            // Close file handle
            @fclose($fh);
            return $ok;
        }

        return FALSE;
    } // function WriteFile()

    /**
     * Delete cache file
     *
     * @param string $file
     * @return bool
     */
    protected function RemoveFile( $file ) {
        return (file_exists($file) AND unlink($file));
    } // function RemoveFile()

}
