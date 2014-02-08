<?php
/**
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
return array(

    /**
     * Possible keys:
     * - type     : (text|textarea|numeric|integer|select), default text
     * - visible  : (TRUE|FALSE), default TRUE
     * - required : (TRUE|FALSE), default FALSE
     * - readonly : (TRUE|FALSE), default FALSE
     * - default  : Default value, works also for not visible attributes
     */

    'resolution' => array(
        'type'     => 'select;0:Marker;1:Curve',
        'required' => FALSE
    ),
    'unit' => array(
        'visible'  => FALSE
    ),
    'decimals' => array(
        'visible'  => FALSE,
        'default'  => 0
    ),
    'valid_from' => array(
        'visible'  => FALSE
    ),
    'valid_to' => array(
        'visible'  => FALSE
    ),
    'latitude' => array(
        'type'     => 'numeric',
        'required' => TRUE
    ),
    'longitude' => array(
        'type'     => 'numeric',
        'required' => TRUE
    ),
    'irradiation' => array(
        'type'     => 'guid'
    ),

);
