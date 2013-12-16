<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace Channel\SMA;

/**
 *
 */
class Webbox extends \Channel {

	/**
	 *
	 */
	public function write( $request, $timestamp=NULL ) {

		if (!isset($request['result']['devices'][0]['channels']))
			throw new \Exception('Invalid Webbox response:'."\n\n".print_r($request, TRUE), 400);

		// find valid child channels
		$channels = array();
		foreach ($this->getChilds() as $child) {
			if ($child->write AND $child->channel != '') {
				$channels[$child->channel] = $child;
			}
		}

		$ok = 0;

		// check for a suitable channel object
		foreach ($request['result']['devices'][0]['channels'] as $channel) {
			// Look for a suitable child channel
			$name = $channel['meta'];
			if (isset($channels[$name])) {
				try {                              // Simulate $request['data']
					$ok += $channels[$name]->write(array('data'=>$channel['value']), $timestamp);
				} catch (\Exception $e) {
					$code = $e->getCode();
					if ($code != 200 AND $code != 201 AND $code != 422) throw $e;
				}
			}
		}

		return $ok;
	}

}
