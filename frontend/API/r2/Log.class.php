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
	public static function help() {
	    return array(
			'[PUT] /api/r2/log' => array(
				'description' => 'Store new log entry, scope defaults to \'API r2\'',
				'payload'     => array(
					'{"scope":"...", "message":"..."}',
				),
			),
			'[GET] /api/r2/log/:key' => array(
				'description' => 'Read a log entry',
			),
			'[POST] /api/r2/log' => array(
				'description' => 'Update a log entry',
				'payload'     => array(
					'{"scope":"...", "message":"..."}',
				),
			),
			'[DELETE] /api/r2/log/:key' => array(
				'description' => 'Delete a log entry',
			),
			'[DELETE] /api/r2/log/' => array(
				'description' => 'Delete all log entries of scope or all if no scope submitted',
				'payload'     => array(
					'{"scope":"..."}',
				),
			),
		);
	}

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

		if ($request['id'] != '') {
			// Read one entry

			$log = new \PVLng\Log($request['id']);

			if ($log->id == '') {
				$this->send(404, 'No log entry found for Id: '.$request['id']);
			}

			$result->swrite(array(
				'id'        => $log->id,
				'timestamp' => strtotime($log->timestamp),
				'datetime'  => $log->timestamp,
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
						'datetime'  => $log->timestamp,
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
		if (!isset($request['id']) OR $request['id'] == '') {
			$this->send(400, 'Missing log Id parameter');
		}

		$log = new \PVLng\Log($request['id']);

		if ($log->id == '') {
			$this->send(404, 'No log entry found for Id: '.$request['id']);
		}

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
		if (isset($request['id'])) {
			$log->find($request['id']);
			if ($log->id == '') {
				$this->send(404, 'No log entry found for Id: '.$request['id']);
			}
			$this->send($log->delete() ? 204 : 400);
		} elseif (isset($request['scope'])) {

		} elseif (!isset($request['scope']) OR $request['scope'] == '') {
			$this->send($log->truncate() ? 204 : 400);
		}
	}
}
