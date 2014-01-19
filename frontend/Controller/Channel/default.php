<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
return array(

    /**
     * Possible keys:
     * - type     : (text|textarea|textextra|numeric|integer|select), default text
     * - visible  : (TRUE|FALSE), default TRUE
     * - required : (TRUE|FALSE), default FALSE
     * - readonly : (TRUE|FALSE), default FALSE
     * - default  : Default value, works also for not visible attributes
     */

    'name' => array(
        'required' => TRUE
    ),

    'description' => array(),

    'serial' => array(),

    'channel' => array(),

    'resolution' => array(
        'type'     => 'numeric',
        'required' => TRUE,
        'default'  => 1
    ),

    'unit' => array(
        'type'     => 'textsmall',
    ),

    'decimals' => array(
        'type'     => 'integer',
        'default'  => 2
    ),

    'meter' => array(
        'type'     => 'select;1:yes;0:no',
        'default'  => 0
    ),

    'numeric' => array(
        'type'     => 'select;1:yes;0:no',
        'default'  => 1
    ),

    'offset' => array(
        'type'     => 'numeric',
    ),

    'adjust' => array(
        'type'     => 'select;1:yes;0:no',
        'default'  => 0
    ),

    'cost' => array(
        'type'     => 'numeric',
    ),

    'threshold' => array(
        'type'     => 'numeric',
    ),

    'valid_from' => array(
        'type'     => 'numeric',
    ),

    'valid_to' => array(
        'type'     => 'numeric',
    ),

    'public' => array(
        'type'     => 'select;1:yes;0:no',
        'default'  => 1
    ),

    'comment' => array(
        'type'     => 'textarea',
    ),

);
