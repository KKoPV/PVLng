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
use \Channel\MultiChannel;

/**
 *
 */
class Inverter extends MultiChannel
{
    /**
     * Have to detect if "overview" or "data" is 1st data key
     */
    public function write($request, $timestamp=null)
    {
        // /site/{siteId}/overview.json
        if (!empty($request['overview']) && is_array($request['overview'])) {
            return parent::write(
                array('data' => $request['overview']),
                strtotime($request['overview']['lastUpdateTime'])
            );
        }

        // /equipment/{siteId}/{serialNumber}/data.json
        if (!empty($request['data']['telemetries']) && is_array($request['data']['telemetries'])) {
            $ok = 0;
            foreach ($request['data']['telemetries'] as $data) {
                $ok += parent::write(
                    array('data' => $data),
                    strtotime($data['date'])
                );
            }
            return $ok;
        }

        throw new \Exception('Invalid data structure', 400);
    }
}
