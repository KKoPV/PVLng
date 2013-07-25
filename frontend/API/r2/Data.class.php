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
	public function GET( &$request ) {
		$channel = \Channel::byGUID($this->GUID);

		// Special models can provide an own GET functionality
		// e.g. for special return formats like PVLog or Sonnenertrag
		if (method_exists($channel, 'GET')) {
		    return $channel->GET($request);
		}

		$datafile = $channel->read($request);

		$outfile = new \Buffer;

		if (array_search('attributes', $request) !== FALSE) {
			$attributes = $channel->getAttributes();

			if (strstr($request[0], 'full')) {
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
		switch ($request[0]) {
			// -------------------
			case 'full':

				// do nothing with $row
				while ($datafile->read($row, $id)) {
					$outfile->swrite($row);
				}
				break;

			// -------------------
			case 'short':

				while ($datafile->read($row, $id)) {
					// default mobile result: only timestamp and data
					$outfile->swrite(array(
						/* 0 */ $row['timestamp'],
						/* 1 */ $row['data']
					));

				}
				break;

			// -------------------
			case 'fullshort':

				while ($datafile->read($row, $id)) {
					$outfile->swrite(array_values($row));
				}
				break;

			// -------------------
			default:

				while ($datafile->read($row, $id)) {
					// default result: only timestamp and data
					$outfile->swrite(array(
						'timestamp' => $row['timestamp'],
						'data'      => $row['data']
					));
				}
				break;

		}
		$datafile->close();

		$request['format'] = $request['format'] ?: 'csv';

		Header('X-Buffer-Size:' . $outfile->size() . ' Bytes');

		return $outfile;
	}

}
