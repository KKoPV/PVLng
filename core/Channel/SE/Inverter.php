<?php
/**
 * Accept JSON data from Solar Edge API
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

    }
}
