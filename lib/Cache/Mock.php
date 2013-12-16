<?php
/**
 * Mockup class with no functionality
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
class Mock extends \Cache {

	// -------------------------------------------------------------------------
	// PUBLIC
	// -------------------------------------------------------------------------

	/**
	 * Cache availability
	 *
	 * @return bool
	 */
	public function isAvailable() {
		return TRUE;
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
		return TRUE;
	}

	/**
	 * Retrieve raw data from cache
	 *
	 * @param string $key Unique cache Id
	 * @return string
	 */
	public function fetch( $key ) {
		return isset($this->data[$key]) ? $this->data[$key] : NULL;
	}

	/**
	 * Delete data from cache
	 *
	 * @param string $key Unique cache Id
	 * @return bool
	 */
	public function delete( $key ) {
		unset($this->data[$key]);
		return TRUE;
	}

	/**
	 * Clear cache
	 *
	 * @return bool
	 */
	public function flush() {
        $this->data = array();
		return TRUE;
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $data = array();

}
