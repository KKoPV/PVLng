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
abstract class InternalCalc extends \Channel {

	/**
	 *
	 */
	protected function __construct( \ORM\Tree $channel ) {
		parent::__construct($channel);
		$this->data = $this->numeric ? new \ORM\ReadingNumMemory :  new \ORM\ReadingStrMemory;
		/* Clean up */
		$this->data->deleteById($this->entity);
	}

	/**
	 * Readings table object
	 */
	protected $data;

	/**
	 * Overwrite default channel tables
	 */
	protected $table = array(
		'pvlng_reading_str_tmp', // numeric == 0
		'pvlng_reading_num_tmp'  // numeric == 1
	);

	/**
	 *
	 */
	protected function saveValues( $values ) {
		$cnt = 0;
		$this->data->id = $this->entity;
		foreach ($values as $this->data->timestamp=>$this->data->data) {
			$cnt += $this->data->insert();
		}
		return $cnt;
	}

	/**
	 *
	 */
	protected function after_read( \Buffer $buffer, $attributes ) {
		/* Clean up */
		$this->data->deleteById($this->entity);
		return parent::after_read($buffer, $attributes);
	}

}
