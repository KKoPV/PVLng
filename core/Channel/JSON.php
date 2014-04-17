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
namespace Channel;

/**
 *
 */
use Channel;

/**
 *
 */
class JSON extends Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     * GROUP_CHANNEL     - generic group
     */
    const TYPE = GROUP_CHANNEL;

    /**
     * Path separator in channel definition
     *
     * section->subsection->subsubsection
     */
    const SEPARATOR = '->';

    /**
     *
     */
    public function write( $request, $timestamp=NULL ) {

        $ok = 0;

        // find valid child channels
        foreach ($this->getChilds() as $child) {

            // Find only writable channels with filled "channel" attribute
            if (!$child->write OR $child->channel == '') continue;

            $path = explode(self::SEPARATOR, $child->channel);

            // Root pointer
            $value = &$request;
            $found = TRUE; // optimistic search :-)

            // To handle [0] array keys use all as strings and array_key_exists
            while (($key = array_shift($path)) != '') {
                if (array_key_exists($key, $value)) {
                    $value = &$value[$key];
                    #print_r($value);
                    #echo ' - ';
                } else {
                    $found = FALSE;
                    break;
                }
            }
            if (!$found) continue;

            // Interpret empty numeric value as invalid
            if ($child->numeric AND $value == '') continue;

            try { //                 Simulate $request['data']
                $ok += $child->write(array('data' => $value), $timestamp);
            } catch (\Exception $e) {
                $code = $e->getCode();
                if ($code != 200 AND $code != 201 AND $code != 422) throw $e;
            }
        }

        return $ok;
    }
}
