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

    'channel' => array(
        'visible'  => FALSE,
        'default'  => 'sky'
    ),
    'serial' => array(
        'visible'  => FALSE,
    ),
    'unit' => array(
        'readonly' => TRUE,
    ),
    'decimals' => array(
        'default'  => 0
    ),
    'offset' => array(
        'visible'  => FALSE,
    ),
    'threshold' => array(
        'visible'  => FALSE,
    ),
    'valid_from' => array(
        'visible'  => FALSE,
    ),
    'valid_to' => array(
        'visible'  => FALSE,
    ),

    'resolution' => array(
        'position'  => 490,
        'required'  => FALSE,
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
