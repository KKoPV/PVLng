<?php
/**
 * Hide all attributes relevant for numeric channels only
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
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

    'resolution' => array(
        'visible' => false
    ),

    'decimals' => array(
        'visible' => false
    ),

    'unit' => array(
        'visible' => false
    ),

    'meter' => array(
        'visible' => false
    ),

    'numeric' => array(
        'visible' => false,
        'default' => 0
    ),

    'offset' => array(
        'visible' => false
    ),

    'adjust' => array(
        'visible' => false
    ),

    'cost' => array(
        'visible' => false
    ),

    'tariff' => array(
        'visible'  => false
    ),

    'threshold' => array(
        'visible' => false
    ),

    'valid_from' => array(
        'visible' => false
    ),

    'valid_to' => array(
        'visible' => false
    ),

);
