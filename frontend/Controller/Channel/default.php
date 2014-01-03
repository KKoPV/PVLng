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
     * - type     : (text|textarea|textextra|numeric|integer|radio), default text
     *              radio results in (0|1)
     * - visible  : (TRUE|FALSE), default TRUE
     * - required : (TRUE|FALSE), default FALSE
     * - readonly : (TRUE|FALSE), default FALSE
     * - default  : Default value, works also for not visible attributes
     */

    'name' => array(
        'type'     => 'text',
        'visible'  => TRUE,
        'required' => TRUE,
        'readonly' => FALSE,
        'default'  => ''
    ),

    'description' => array(
        'type'     => 'text',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => ''
    ),

    'serial' => array(
        'type'     => 'text',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => ''
    ),

    'channel' => array(
        'type'     => 'text',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => ''
    ),

    'resolution' => array(
        'type'     => 'numeric',
        'visible'  => TRUE,
        'required' => TRUE,
        'readonly' => FALSE,
        'default'  => 1
    ),

    'unit' => array(
        'type'     => 'textsmall',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => ''
    ),

    'decimals' => array(
        'type'     => 'integer',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => 2
    ),

    'meter' => array(
        'type'     => 'radio',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => 0
    ),

    'numeric' => array(
        'type'     => 'radio',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => 1
    ),

    'offset' => array(
        'type'     => 'numeric',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => NULL
    ),

    'adjust' => array(
        'type'     => 'radio',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => 0
    ),

    'cost' => array(
        'type'     => 'numeric',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => NULL
    ),

    'threshold' => array(
        'type'     => 'numeric',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => NULL
    ),

    'valid_from' => array(
        'type'     => 'numeric',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => NULL
    ),

    'valid_to' => array(
        'type'     => 'numeric',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => NULL
    ),

    'public' => array(
        'type'     => 'radio',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => 1
    ),

    'comment' => array(
        'type'     => 'textarea',
        'visible'  => TRUE,
        'required' => FALSE,
        'readonly' => FALSE,
        'default'  => ''
    ),

);
