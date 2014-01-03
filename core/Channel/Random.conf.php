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
     * - type     : (text|textarea|numeric|integer|radio), default text
     *              radio results in (0|1)
     * - visible  : (TRUE|FALSE), default TRUE
     * - required : (TRUE|FALSE), default FALSE
     * - readonly : (TRUE|FALSE), default FALSE
     * - default  : Default value, works also for not visible attributes
     */

    'threshold' => array(
        'visible'  => TRUE,
        'required' => TRUE
    ),
    'valid_from' => array(
        'required' => TRUE
    ),
    'valid_to' => array(
        'required' => TRUE
    ),

);
