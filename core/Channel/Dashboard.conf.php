<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.2.0
 *
 * 1.2.0
 * - View channels which by default not used for grouping channels
 *
 * 1.1.0
 * - Add channel required
 *
 * 1.0.0
 * - Inital creation
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

    'valid_from' => array(
        'required' => TRUE
    ),
    'valid_to' => array(
        'required' => TRUE
    ),
    'colors' => array(
        'type'     => 'textarea'
    )

);
