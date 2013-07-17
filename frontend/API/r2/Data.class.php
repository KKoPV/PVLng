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

		$outfile = \Buffer::create(10);

		if (array_search('attributes', $request) !== FALSE) {
			$attributes = $channel->getAttributes();
			
			if (strstr($request[0], 'full')) {
				// Calculate consumption and costs
				rewind($datafile);
				while (\Buffer::read($datafile, $row, $id)) {
					$attributes['consumption'] += $row['consumption'];
				}
				$attributes['costs'] = $attributes['consumption'] * $attributes['cost'];
			}
			\Buffer::swrite($outfile, $attributes);
		}

		\Buffer::rewind($datafile);

		// optimized flow...
		switch ($request[0]) {
			// -------------------
			case 'full':

				// do nothing with $row
				while (\Buffer::read($datafile, $row, $id)) {
					\Buffer::swrite($outfile, array(
						'datetime'    => $row['datetime'],
						'timestamp'   => $row['timestamp'],
						'data'        => $row['data'],
						'min'         => $row['min'],
						'max'         => $row['max'],
						'count'       => $row['count'],
						'timediff'    => $row['timediff'],
						'consumption' => $row['consumption']
					));
				}
				break;

			// -------------------
			case 'short':

				while (\Buffer::read($datafile, $row, $id)) {
					// default mobile result: only timestamp and data
					\Buffer::swrite($outfile, array(
						/* 0 */ $row['timestamp'],
						/* 1 */ $row['data']
					));

				}
				break;

			// -------------------
			case 'fullshort':

				while (\Buffer::read($datafile, $row, $id)) {
					\Buffer::swrite($outfile, array(
						/* 0 */ $row['datetime'],
						/* 1 */ $row['timestamp'],
						/* 2 */ $row['data'],
						/* 3 */ $row['min'],
						/* 4 */ $row['max'],
						/* 5 */ $row['count'],
						/* 6 */ $row['timediff'],
						/* 7 */ $row['consumption']
					));

				}
				break;

			// -------------------
			default:

				while (\Buffer::read($datafile, $row, $id)) {
					// default result: only timestamp and data
					\Buffer::swrite($outfile, array(
						'timestamp' => $row['timestamp'],
						'data'      => $row['data']
					));
				}
				break;

		}
		\Buffer::close($datafile);

		$request['format'] = $request['format'] ?: 'csv';

		Header('X-Buffer-Size:' . \Buffer::size($outfile) . ' Bytes');

		return $outfile;
	}

}
