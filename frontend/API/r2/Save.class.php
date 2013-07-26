<?php
/**
 *
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
class Save extends Handler {

	/**
	 *
	 */
	public function PUT( &$request ) {
		$channel = \Channel::byGUID($this->GUID);

		if ($channel->write($request)) {
			// Created
			$this->send(201);
		}

		// Accepted but no data or not saved (inside update interval)
		$this->send(200);
	}

}
