<?php
/**
 * Accept JSON data from several equipments, like SMA Webboxes, Fronius
 * inverters or SmartGrid
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 - Knut Kohl <github@knutkohl.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
use \Channel\JSON;

/**
 *
 */
class SolarNet extends JSON {

    /**
     *
     */
    public function write( $request, $timestamp=NULL ) {

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
        return parent::write($request, strtotime($request['Head']['Timestamp']));
    }
}
