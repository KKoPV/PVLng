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
		$log = new \PVLng\Log;

		$log->scope = !empty($request['scope']) ? $request['scope'] : 'API r2';
		$log->data  = !empty($request['message']) ? trim($request['message']) : '';

		$this->send($log->insert() ? 201 : 400);
	}

	/**
	 *
	 */
	public function GET( &$request ) {

		$result = new \Buffer;

		if ($request[0] != '') {
			// Read one entry

			$log = new \PVLng\Log($request[0]);

			if ($log->id == '') $this->send(404);

			$result->swrite(array(
				'id'        => $log->id,
				'timestamp' => strtotime($log->timestamp),
				'scope'     => $log->scope,
				'message'   => $log->data
			));

		} else {
			// Read all entries

			$q = new \DBQuery('pvlng_log');
			$q->order('id');

			if ($res = \yMVC\MySQLi::getInstance()->query($q)) {
				while ($log = $res->fetch_object()) {
					$result->swrite(array(
						'id'        => $log->id,
						'timestamp' => strtotime($log->timestamp),
						'scope'     => $log->scope,
						'message'   => $log->data
					));
				}
			}
		} 

		return $result;
	}

	/**
	 *
	 */
	public function POST( &$request ) {

		// Check for entry Id
		if ($request[0] == '') $this->send(400);

		$log = new \PVLng\Log($request[0]);

		if ($log->id == '') $this->send(404);

		$log->scope = !empty($request['scope']) ? $request['scope'] : 'API r2';
		$log->data  = !empty($request['message']) ? trim($request['message']) : '';

		$this->send($log->replace() ? 204 : 400);
	}

	/**
	 *
	 */
	public function DELETE( &$request ) {

		$log = new \PVLng\Log;

		// Check for entry Id
		if ($request[0] != '') {
			$log->find($request[0]);
			if ($log->id == '') $this->send(404);
			$this->send($log->delete() ? 204 : 400);
		} else {
			$this->send($log->truncate() ? 204 : 400);
		}
	}

}
