<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace Channel;

/**
 *
 */
class Random extends \Channel {

	/**
	 * Timestamp delta 5 minutes
	 */
	const DELTA = 5;

	/**
	 *
	 */
	protected $table = array(
		'pvlng_reading_str_tmp', // numeric == 0
		'pvlng_reading_num_tmp'  // numeric == 1
	);

	/**
	 *
	 */
	protected $create = array(
		'CREATE TABLE IF NOT EXISTS `pvlng_reading_str_tmp` (
		   `id` int(10) unsigned NOT NULL,
		   `timestamp` int(10) unsigned NOT NULL,
		   `data` varchar(50) NOT NULL,
		   PRIMARY KEY (`id`,`timestamp`)
		 ) ENGINE=MEMORY',
		'CREATE TABLE IF NOT EXISTS `pvlng_reading_num_tmp` (
		   `id` int(10) unsigned NOT NULL,
		   `timestamp` int(10) unsigned NOT NULL,
		   `data` decimal(13,4) NOT NULL,
		   PRIMARY KEY (`id`, `timestamp`)
		 ) ENGINE=MEMORY'
	);

	/**
	 *
	 */
	protected function before_read( $request ) {

		parent::before_read($request);

		// make sure, only until now :-)
		$this->end = min($this->end, time());

		$timestamp = $this->start;

		if ($this->meter) {
			$value = is_null($this->valid_from) ? 0 : $this->valid_from;
			$minRand = 0;
		} else {
			// Init value in middle of valid range
			$value = ((is_null($this->valid_from) ? 0 : $this->valid_from) +
					  (is_null($this->valid_to) ? 100 : $this->valid_to)) / 2;
			$minRand = -1; // to get negative steps
		}
		// max. change +- 5
		$threshold = $this->threshold ?: 5;
		// buffer once
		$randMax = mt_getrandmax();

		$this->db->query($this->create[$this->numeric]);
		$this->db->query('TRUNCATE `'.$this->table[$this->numeric].'`');

		$values = array();
		while ($timestamp <= $this->end) {
			$values[] = '(' . $this->entity . ',' . $timestamp . ',' . $value . ')';

			// calc next value;
			$timestamp += self::DELTA * 60;
			$value += mt_rand() / $randMax * $threshold * mt_rand($minRand, 1);
			$value = $this->valid($value);
		}

		if (count($values)) {
			$this->db->query('INSERT INTO `'.$this->table[$this->numeric].'`'
			                .'VALUES '. implode(',', $values));
		}

	}

	/**
	 *
	 */
	protected function after_read( $tmpfile, $attributes ) {

		$this->db->query('TRUNCATE `'.$this->table[$this->numeric].'`');

		return parent::after_read($tmpfile, $attributes);
	}

}
