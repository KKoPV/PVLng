<?php
/**
 * Logger interface
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace API\r2;

/**
 *
 */
class Log extends Handler {

	/**
	 *
	 */
	public function PUT( &$request ) {

		// pre trim for test if empty
		$message = !empty($request['message']) ? trim($request['message']) : '';

		if ($message == '') return;

		$log = new \PVLng\Log;

		$log->scope = !empty($request['scope']) ? $request['scope'] : 'API r2';
		$log->data = $message;

		$this->send($log->insert() ? 201 : 400);
	}
}
