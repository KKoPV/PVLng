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

    'unit' => array(
        'default'  => 'W/m²'
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

    'resolution' => array(
        'position' => 420,
        'type'     => 'bool;0:Marker;1:Curve',
        'required' => FALSE
    ),

    'times' => array(
        'position' => 430,
        'type'     => 'bool;0:no;1:yes',
    ),

    'extra' => array(
        'visible'  => TRUE,
        'type'     => 'sql:X:'
                     .'SELECT'
                     .'  `guid`,'
                     .'  CONCAT('
                     .'    `name`,'
                     .'    IF(`description`<>"", CONCAT(" (", `description`, ")"), ""),'
                     .'    IF(`channel`<>"", CONCAT(" [", `channel`, "]"), "")'
                     .'  )'
                     .' FROM `pvlng_channel`'
                     .'WHERE `type` = 64'
    ),

);
