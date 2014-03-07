<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
return array(

    'serial' => array(
        'visible'  => TRUE,
        'required' => TRUE,
    ),
    'channel' => array( // used for device type
        'visible'  => TRUE,
                   // select;<key>:<text>[;<key>:<text>]...
        'type'     => 'select;1:Inverter;2:SensorBox',
        'default'  => 1
    ),
    'resolution' => array(
        'visible'  => TRUE,
        'required' => TRUE,
    )

);
