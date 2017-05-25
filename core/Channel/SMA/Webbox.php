<?php
/**
 * Accept JSON data from SMA Webboxes
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel\SMA;

/**
 *
 */
use \Channel\MultiChannel;

/**
 *
 */
class Webbox extends MultiChannel
{
    /**
     *
     */
    public function write($request, $timestamp = null)
    {
        // Check for request errors
        if (!isset($request['result']['devices'][0]['channels'])) {
            throw new \Exception(
                "Invalid Webbox response:\n".print_r($request, true),
                400
            );
        }

        // Transform
        $channels = array();
        foreach ($request['result']['devices'][0]['channels'] as $channel) {
            $channels[$channel['meta']] = $channel['value'];
        }

        return parent::write(
            array('data' => $channels),
            // Request timestamp to webbox is saved as request id,
            // used also for reload of failed files!
            is_numeric($request['id']) ? $request['id'] : strtotime($request['id'])
        );
    }
}
