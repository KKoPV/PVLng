<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.1.0
 *
 * 1.1.0
 * - add channel required
 *
 * 1.0.0
 * - inital creation
 */
return array(

    /**
     * Possible keys:
     * - visible             : (TRUE|FALSE), default TRUE
     * - required             : (TRUE|FALSE), default FALSE
     * - default             : default value
     */
    'name' => array(
        'visible' => FALSE,
    ),
    'description' => array(
        'visible' => FALSE,
    ),
    'serial' => array(
        'visible' => FALSE,
    ),
    'channel' => array(
        'required' => TRUE,
    ),
    'numeric' => array(
        'visible' => FALSE,
    ),
    'meter' => array(
        /* Get the info from the child */
        'visible' => FALSE,
    ),
    'threshold' => array(
        'visible' => FALSE,
    ),
    'cost' => array(
        'visible' => FALSE,
    ),
    'valid_from' => array(
        'required' => TRUE,
    ),
    'valid_to' => array(
        'required' => TRUE,
    )

);
