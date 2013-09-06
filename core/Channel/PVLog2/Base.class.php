<?php
/**
 * Base class for generation PV-Log JSON response
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-28-g4d7f5c3 2013-05-10 14:29:24 +0200 Knut Kohl $
 */
namespace Channel\PVLog2;

/**
 * Load basic PV-Log class
 */
require_once LIB_DIR . '/PVLog/PVLog.php';

/**
 * Register PSR-1 autoloader
 */
\PVLog\PVLog::registerAutoloader();

/**
 *
 */
use Channel;

/**
 * Base class for generation PV-Log JSON 2.0 response
 *
 * Fetch data for one inverter and transform many inverter data into correct
 * PV-Log JSON 2.0
 */
class Base extends Channel {

	/**
	 *
	 */
	public $settings;

	/**
	 * r2
	 */
	public function GET( $request ) {
		return $this->read($request);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $ts;

	/**
	 *
	 */
	protected function __construct( $guid ) {
		parent::__construct($guid);

		$this->ts = microtime(TRUE);
		$this->settings = include __DIR__ . DS . 'config.php';

		$offset = $this->settings['offset'];
		if ($this->settings['dst']) $offset++;
		\PVLog\JSON2\Helper::set('utc_offset', $offset);
	}

	/**
	 *
	 */
	protected function csv2data( $buffer ) {
		$buffer->rewind();
		$data = array();
		while ($buffer->read($row, $id)) {
			$data[\PVLog\JSON2\Helper::localTimestamp($row['timestamp'])] = $row['data'];
		}
		return $data;
	}

}