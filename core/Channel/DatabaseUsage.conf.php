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
     * - type     : (text|textarea|numeric|integer|select), default text
     * - visible  : (TRUE|FALSE), default TRUE
     * - required : (TRUE|FALSE), default FALSE
     * - readonly : (TRUE|FALSE), default FALSE
     * - default  : Default value, works also for not visible attributes
     */

    'meter' => array(
        'default'  => 1
    ),
    'resolution' => array(
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
    'public' => array(
        'default'  => 0,
    ),
    'extra' => array(
        'type'     => 'select;1:Numeric readings;0:Non-numeric readings',
        'visible'  => TRUE
    ),

);