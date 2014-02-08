<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     2.0.0
 *
 * 2.0.0
 * - 1st channel is always the grouping channel
 *
 * 1.0.0
 * - Inital creation
 */
return array(

    'name' => 'Fronius inverter',

    'description' => '
        Fronius solar inverter with:
        <ul>
            <li>E-Total lifetime, readings in kWh, output in Wh</li>
            <li>Pac (W)</li>
            <li>Sac - Reactive power (VA)</li>
            <li>Uac, Udc (V)</li>
            <li>Iac, Idc (A)</li>
            <li>State (string)</li>
            <li>Error (string)</li>
            <li>Mains frequency (Hz)</li>
        </ul>
        Don\'t forget to adjust inverters <strong>DeviceId</strong> afterwards!
    ',


    'channels' => array(

        /**
         * 1st channel - Grouping channel
         * other       - Real channels
         *
         * Only required attributes must be filled
         */

        // Grouping channel
        array(
            'type'        => 43, // Fronius inverter
            'name'        => 'Fronius inverter',
            'serial'      => 0,
            'channel'     => 1
        ),

        // Real channels
        array(
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'channel'     => 'Body->Data->TOTAL_ENERGY',
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
            'comment'     => 'Fill the "cost" with the amount of money you get for each watt hour (e.g. cent / 1000 / 1000 to get EUR).',
        ),
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac',
            'channel'     => 'Body->Data->PAC',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 1,
            'comment'     => 'Set lower valid border to avoid store invalid data (inverter OFF)'
        ),
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Sac (Reactive power)',
            'channel'     => 'Body->Data->SAC',
            'resolution'  => 1,
            'unit'        => 'VA',
            'decimals'    => 0,
            'valid_from'  => 1,
            'comment'     => 'Set lower valid border to avoid store invalid data (inverter OFF)'
        ),
        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac',
            'channel'     => 'Body->Data->UAC',
            'unit'        => 'V',
            'decimals'    => 0,
            'valid_from'  => 1,
            'comment'     => 'Set lower valid border to avoid store invalid data (inverter OFF)'
        ),
        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac',
            'channel'     => 'Body->Data->IAC',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
            'comment'     => 'Set lower valid border to avoid store invalid data (inverter OFF)'
        ),
        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc',
            'channel'     => 'Body->Data->UDC',
            'unit'        => 'V',
            'decimals'    => 0,
            'valid_from'  => 1,
            'comment'     => 'Set lower valid border to avoid store invalid data (inverter OFF)'
        ),
        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc',
            'channel'     => 'Body->Data->IDC',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
            'comment'     => 'Set lower valid border to avoid store invalid data (inverter OFF)'
        ),
        array(
            'type'        => 91, // Switch, non-numeric
            'name'        => 'State',
            'channel'     => 'Body->Data->DeviceStatus->StatusCode',
            'numeric'     => 0,
            'decimals'    => 0,
            'comment'     => 'The following JSON map object will translate the numeric reading value during write to textual representation (ALL IN ONE LINE).'."\n\n"
                            .'WRITEMAP::{"0":"Startup","1":"Startup","2":"Startup","3":"Startup","4":"Startup","5":"Startup","6":"Startup","7":"Running","8":"Standby","9":"Boot loading","19":"Error"}'
        ),
        array(
            'type'        => 91, // Switch, numeric
            'name'        => 'Error',
            'channel'     => 'Body->Data->DeviceStatus->ErrorCode',
            'decimals'    => 0,
            'comment'     => 'Numeric error codes',
        ),
        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Mains frequency',
            'channel'     => 'Body->Data->FAC',
            'unit'        => 'Hz',
            'decimals'    => 1,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),
    ),
);

/* ***************************************************************************

Channel types

1 - Power plant
2 - Inverter
3 - Building
4 - Multi-Sensor
5 - Group
10 - Random
11 - Fixed value
12 - Estimate
15 - Ratio calculator
16 - Accumulator
17 - Differentiator
18 - Full Differentiator
19 - Sensor to meter
20 - Import / Export
21 - Average
22 - Calculator
23 - History
24 - Baseline
25 - Topline
30 - Dashboard channel
40 - SMA Sunny Webbox
41 - SMA Inverter
42 - SMA Sensorbox
43 - Fronius Inverter
44 - Fronius Sensorbox
50 - Energy meter, absolute
51 - Power sensor
52 - Voltage sensor
53 - Current sensor
54 - Gas sensor
55 - Heat sensor
56 - Humidity sensor
57 - Luminosity sensor
58 - Pressure sensor
59 - Radiation sensor
60 - Temperature sensor
61 - Valve sensor
62 - Water sensor
63 - Windspeed sensor
64 - Irradiation sensor
65 - Timer
66 - Frequency sensor
67 - Winddirection sensor
68 - Rainfall sensor
70 - Gas meter
71 - Radiation meter
72 - Water meter
73 - Rainfall meter
90 - Power sensor counter
91 - Switch
100 - PV-Log Plant
101 - PV-Log Inverter
102 - PV-Log Plant (r2)
103 - PV-Log Inverter (r2)
110 - Sonnenertrag JSON

*************************************************************************** */
