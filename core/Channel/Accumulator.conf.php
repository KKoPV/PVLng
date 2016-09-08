<?php
/**
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Get meter from childs
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

    // Get from childs
    'meter' => array(
        'visible' => false
    ),

    // Strict mode
    'extra' => array(
        'visible' => true,
        'type'    => 'bool;0:no;1:yes',
        'default' => 0
    ),

);
