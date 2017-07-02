<?php
/**
 * Accept JSON data from WS-2300 weather station
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class WS2300 extends MultiChannel
{
    /**
     * Accept JSON fron WS-2300
     */
    public function write($request, $timestamp = null)
    {
        // Check for valid request
        if (!isset($request['Timestamp'])) {
            throw new Exception(
                "Invalid WS2300 response:\n".print_r($request, true),
                400
            );
        }

        return parent::write(array('data' => $request), $request['Timestamp']);
    }
}
