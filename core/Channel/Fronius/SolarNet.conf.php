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

    'serial' => array( // used for DeviceId
        'visible'  => true,
        'required' => true,
        'type'     => 'integer'
    ),
    'channel' => array( // used for device type
        'visible'  => true,
                   // select;<key>:<text>[;<key>:<text>]...
        'type'     => 'select;1:Inverter;2:InverterWithStrings;3:SensorBox',
        'default'  => 1
    )

);
