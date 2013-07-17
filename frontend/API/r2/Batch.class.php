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
class Batch extends Handler {

	/**
	 *
	 */
	public function PUT( &$request ) {
		$channel = \Channel::byGUID($this->GUID);

		if (isset($request['data'])) {

			$readings = array();
			foreach (explode(';', $request['data']) as $dataset) {
				if ($dataset == '') continue;

				$data = explode(',', $dataset);

				switch (count($data)) {
					case 2:
						// timestamp and data
						$readings[$data[0]] = $data[1];
						break;
					case 3:
						// date, time and data
						$timestamp = strtotime($data[0] . ' ' . $data[1]);
						if ($timestamp === false) {
							$this->send(400, 'Invalid timestamp in data: '.$dataset);
						}
						$readings[$timestamp] = $data[2];
						break;
					default:
						$this->send(400, 'Invalid batch data: '.$dataset);
						break;
				} // switch
			}

			$res = 0;
			foreach ($readings as $timestamp=>$data) {
				$res += $channel->write($data, $timestamp);
			}
			if ($res) {
				// Created
				$this->send(201, 'Rows inserted: '.$res);
			}

			// Created
			$this->send(201);
		}

		// Accepted but no data or not saved (inside update interval)
		return '';
	}

}
