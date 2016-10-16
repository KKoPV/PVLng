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
     *              bool;0:no;1:yes
     *              radio results in (0|1)
     * - visible  : (true|false), default true
     * - required : (true|false), default false
     * - readonly : (true|false), default false
     * - default  : Default value, works also for not visible attributes
     */

    'extra' => array(
//         'visible'  => true,
        'type'     => 'bool',
        'default'  => 0,
    ),

);
