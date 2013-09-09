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
class Config extends Handler {

	/**
	 *
	 */
	public static function help() {
	    return array(
			'PUT /api/r2/config' => array(
				'description' => 'Store new configuration value',
				'payload'     => array(
					'{"<key>":"<value>"}',
				),
			),
			'GET /api/r2/config/:key' => array(
				'description' => 'Read a configuration value',
			),
			'POST /api/r2/config' => array(
				'description' => 'Update a configuration value',
				'payload'     => array(
					'{"<key>":"<value>"}',
				),
			),
			'DELETE /api/r2/config/:key' => array(
				'description' => 'Delete a configuration value',
			),
		);
	}

	/**
	 *
	 */
	public function GET( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request['key'])) return;

		$cfg = new \PVLng\Config($request['key']);
		return $cfg->value;
	}

	/**
	 *
	 */
	public function PUT( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request['key'])) return;

		$cfg = new \PVLng\Config($request['key']);

		if ($cfg->key) $this->send(400, 'Key "'.$request['key'].'" still exists!');

		$cfg->key = $request['key'];
		if (isset($request['data'])) $cfg->value = $request['data'];
		$this->send($cfg->insert() ? 201 : 400);
	}

	/**
	 *
	 */
	public function POST( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request['key'])) return;

		$cfg = new \PVLng\Config($request['key']);

		if (!$cfg->key) $this->send(400, 'Key "'.$request['key'].'" not exists!');

		if (isset($request['data'])) $cfg->value = $request['data'];
		$this->send($cfg->replace() ? 201 : 400);
	}

	/**
	 *
	 */
	public function DELETE( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request['key'])) return;

		$cfg = new \PVLng\Config($request['key']);

		if (!$cfg->key) $this->send(204, 'Key "'.$request['key'].'" not exists!');

		$this->send($cfg->delete() ? 200 : 400);
	}

}
