<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
return array(

    /**
     * Possible keys:
     * - type     : (text|textarea|numeric|integer|radio), default text
     *              radio results in (0|1)
     * - visible  : (TRUE|FALSE), default TRUE
     * - required : (TRUE|FALSE), default FALSE
     * - readonly : (TRUE|FALSE), default FALSE
     * - default  : Default value, works also for not visible attributes
     */

    'extra' => array(
        'type'     => 'range;10;30;10', // 10, 20 or 30 days
        'visible'  => true,
        'default'  => 5,
    ),

    'valid_from' => array(
        'visible' => false,
    ),

    'valid_to' => array(
        'visible' => false,
    ),

);
