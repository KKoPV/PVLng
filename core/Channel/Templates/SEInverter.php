<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 *
 * 1.0.0
 * - Inital creation
 */
return array(

    'name' => 'Solar Edge Inverter',

    'description' => '
        <ul>
            <li>E-Total lifetime (Wh)</li>
            <li>Pac (W)</li>
            <li>Udc (V)</li>
            <li>Uac1, Uac2, Uac3 (V)</li>
            <li>Iac1, Iac2, Iac3 (A)</li>
            <li>Mains frequency 3 phases (Hz)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 48, // Solar Edge Inverter
            'name'        => 'Solar Edge Inverter',
            'description' => 'FILL THE SERIAL NUMBER WITH YOURS',
        ),

        // Real channels
        array(
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'description' => 'Energy production',
            'channel'     => 'lifeTimeData->energy',
            'resolution'  => 1,
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac',
            'description' => 'AC Power',
            'channel'     => 'currentPower->power',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc',
            'description' => 'DC Voltage',
            'channel'     => 'totalActivePower',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac1',
            'description' => 'AC Current string 1',
            'channel'     => 'L1Data->acCurrent',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac1',
            'description' => 'AC Voltage',
            'channel'     => 'L1Data->acVoltage',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Freq1',
            'description' => 'Mains frequency 1',
            'channel'     => 'L1Data->acFrequency',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac2',
            'description' => 'AC Current string 2',
            'channel'     => 'L2Data->acCurrent',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac2',
            'description' => 'AC Voltage 2',
            'channel'     => 'L2Data->acVoltage',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Freq2',
            'description' => 'Mains frequency 2',
            'channel'     => 'L2Data->acFrequency',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac3',
            'description' => 'AC Current string 3',
            'channel'     => 'L3Data->acCurrent',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac3',
            'description' => 'AC Voltage 3',
            'channel'     => 'L3Data->acVoltage',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Freq3',
            'description' => 'Mains frequency 3',
            'channel'     => 'L3Data->acFrequency',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Inverter temperature',
            'channel'     => 'temperature',
            'unit'        => '°C',
            'decimals'    => 1,
        ),
    )
);
