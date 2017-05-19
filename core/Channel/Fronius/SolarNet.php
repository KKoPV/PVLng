<?php
/**
 * Accept JSON data from Fronius SolarNet
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel\Fronius;

/**
 *
 */
use \Channel\MultiChannel;

/**
 *
 */
class SolarNet extends MultiChannel
{
    /**
     *
     */
    public function write($request, $timestamp=null)
    {
        // Check for request errors
        if (!isset($request['Head']['Status']['Code'])) {
            throw new \Exception(
                "Unknown SolarNet response:\n".print_r($request, TRUE),
                400
            );
        }

        $s = $request['Head']['Status'];

        if ($s['Code'] != 0) {
            throw new \Exception(
                'SolarNet error ('.$s['Code'].') '.$s['Reason'].' ('.$s['UserMessage'].')',
                400
            );
        }

        // Use timestamp from file
        return parent::write(array('data' => $request), strtotime($request['Head']['Timestamp']));
    }
}
