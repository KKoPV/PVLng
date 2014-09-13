<?php
/**
 * Accept JSON data from Solar Edge API calls
 *
 * /site/{siteId}/overview.json
 * /equipment/{siteId}/{serialNumber}/data.json
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel\SE;

/**
 *
 */
use \Channel\JSON;

/**
 *
 */
class Inverter extends JSON {

    /**
     * Have to detect if "overview" or "data" is 1st data key
     */
    public function write( $request, $timestamp=NULL ) {

        // /site/{siteId}/overview.json
        if (isset($request['overview'])) {
            $o = $request['overview'];
            return parent::write($o, strtotime($o['lastUpdateTime']));
        }

        // /equipment/{siteId}/{serialNumber}/data.json
        if (isset($request['data']['telemetries']) AND
                  is_array($request['data']['telemetries'])) {
            $cnt = 0;
            foreach ($request['data']['telemetries'] as $t) {
                $cnt += parent::write($t, strtotime($t['date']));
            }
            return $cnt;
        }

        throw new \Exception('Invalid data structure', 400);
    }
}
