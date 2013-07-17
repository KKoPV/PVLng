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
	public function GET( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request[0])) return;

		$cfg = new \PVLng\Config($request[0]);
		return $cfg->value;
	}

	/**
	 *
	 */
	public function PUT( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request[0])) return;

		$cfg = new \PVLng\Config($request[0]);

		if ($cfg->key) $this->send(400, 'Key "'.$request[0].'" still exists!');

		$cfg->key = $request[0];
		if (isset($request['data'])) $cfg->value = $request['data'];
		$this->send($cfg->insert() ? 201 : 400);
	}

	/**
	 *
	 */
	public function POST( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request[0])) return;

		$cfg = new \PVLng\Config($request[0]);

		if (!$cfg->key) $this->send(400, 'Key "'.$request[0].'" not exists!');

		if (isset($request['data'])) $cfg->value = $request['data'];
		$this->send($cfg->replace() ? 201 : 400);
	}

	/**
	 *
	 */
	public function DELETE( &$request ) {
		// Return the one value as text
		$request['format'] = 'text';

		if (empty($request[0])) return;

		$cfg = new \PVLng\Config($request[0]);

		if (!$cfg->key) $this->send(204, 'Key "'.$request[0].'" not exists!');

		$this->send($cfg->delete() ? 200 : 400);
	}

}
