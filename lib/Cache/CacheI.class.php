<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace Cache;

/**
 *
 * @ingroup     Cache
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
interface CacheI {

	/**
	 * @name Abstract functions
	 * @{
	 */

	/**
	 * Cache availability
	 *
	 * Returns TRUE by default, reimplement if required
	 *
	 * @return bool
	 */
	public function isAvailable();

	/**
	 * Store data in cache
	 *
	 * @param string $id Unique cache Id
	 * @param mixed $data
	 * @param int $ttl Time to live or timestamp
	 *                 - = 0 - expire never
	 *                 - > 0 - Time to live
	 *                 - < 0 - Timestamp of expiration
	 * @return bool
	 */
	public function set( $id, $data, $ttl=0 );

	/**
	 * Retrieve data from cache
	 *
	 * @param string $id Unique cache Id
	 * @return mixed
	 */
	public function get( $id );

	/**
	 * Delete data from cache
	 *
	 * @param string $id Unique cache Id
	 * @return bool
	 */
	public function delete( $id );

	/**
	 * Clear cache
	 *
	 * @return bool
	 */
	public function flush();

	/**
	 * Garbage collection
	 *
	 * @return bool
	 */
	public function gc();
	/** @} */

}