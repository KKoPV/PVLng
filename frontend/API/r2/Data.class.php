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
class Data extends Handler {

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

	/**
	 *
	 */
	public function GET( &$request ) {
		$channel = \Channel::byGUID($this->GUID);

		// Special models can provide an own GET functionality
		// e.g. for special return formats like PVLog or Sonnenertrag
		if (method_exists($channel, 'GET')) {
		    return $channel->GET($request);
		}

		$mode = array_key_exists('mode', $request) ? $request['mode'] : '';
		$attr = array_key_exists('attributes', $request) ? $request['attributes'] : FALSE;

		$datafile = $channel->read($request);

		$outfile = new \Buffer;

		if ($attr) {
			$attributes = $channel->getAttributes();

			if (strstr($mode, 'full')) {
				// Calculate consumption and costs
				$datafile->rewind();
				while ($datafile->read($row, $id)) {
					$attributes['consumption'] += $row['consumption'];
				}
				$attributes['costs'] = $attributes['consumption'] * $attributes['cost'];
			}
			$outfile->swrite($attributes);
		}

		$datafile->rewind();

		// optimized flow...
		switch ($mode) {
			// -------------------
			case 'full':

				// do nothing with $row, passtrough
				while ($datafile->read($row, $id)) {
					$outfile->swrite($row);
				}
				break;

			// -------------------
			case 'short':

				// default mobile result: only timestamp and data
				while ($datafile->read($row, $id)) {
					$outfile->swrite(array(
						/* 0 */ $row['timestamp'],
						/* 1 */ $row['data']
					));

				}
				break;

			// -------------------
			case 'fullshort':

				// passthrough all values
				while ($datafile->read($row, $id)) {
					$outfile->swrite(array_values($row));
				}
				break;

			// -------------------
			default:

				// default result: only timestamp and data
				while ($datafile->read($row, $id)) {
					$outfile->swrite(array(
						'timestamp' => $row['timestamp'],
						'data'      => $row['data']
					));
				}
				break;

		}
		$datafile->close();

		Header('X-Buffer-Size:' . $outfile->size() . ' Bytes');

		return $outfile;
	}

}
