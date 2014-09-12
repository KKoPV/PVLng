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
        Solar inverter with up to 3 strings:
        <ul>
            <li>E-Total lifetime, readings in kWh, output in Wh</li>
            <li>Pac (W)</li>
            <li>Pdc1, Pdc2, Pdc3 (W)</li>
            <li>Udc1, Udc2, Udc3 (V)</li>
            <li>Idc1, Idc2, Idc3 (A)</li>
            <li>Mode (string)</li>
            <li>Error (string)</li>
            <li>Temperature (°C)</li>
            <li>Mains frequency (Hz)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 48, // Solar Edge Inverter
            'name'        => 'Solar Edge Inverter',
        ),

        // Real channels
        array(
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'description' => 'Energy production',
            'channel'     => 'overview->lifeTimeData->energy',
            'resolution'  => 1,
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac',
            'description' => 'AC Power',
            'channel'     => 'overview->currentPower->power',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc',
            'description' => 'DC Voltage',
            'channel'     => 'data->telemetries[]->totalActivePower||data->telemetries[]->date',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac1',
            'description' => 'DC Current string 1',
            'channel'     => 'data->telemetries[]->L1Data->acCurrent||data->telemetries[]->date',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac1',
            'description' => 'AC Voltage',
            'channel'     => 'data->telemetries[]->L1Data->acVoltage||data->telemetries[]->date',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Freq1',
            'description' => 'Mains frequency 1',
            'channel'     => 'data->telemetries[]->L1Data->acFrequency||data->telemetries[]->date',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac2',
            'description' => 'DC Current string 2',
            'channel'     => 'data->telemetries[]->L2Data->acCurrent||data->telemetries[]->date',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac2',
            'description' => 'AC Voltage 2',
            'channel'     => 'data->telemetries[]->L2Data->acVoltage||data->telemetries[]->date',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Freq2',
            'description' => 'Mains frequency 2',
            'channel'     => 'data->telemetries[]->L2Data->acFrequency||data->telemetries[]->date',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac3',
            'description' => 'DC Current string 3',
            'channel'     => 'data->telemetries[]->L3Data->acCurrent||data->telemetries[]->date',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac3',
            'description' => 'AC Voltage 3',
            'channel'     => 'data->telemetries[]->L3Data->acVoltage||data->telemetries[]->date',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Freq3',
            'description' => 'Mains frequency 3',
            'channel'     => 'data->telemetries[]->L3Data->acFrequency||data->telemetries[]->date',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Inverter temperature',
            'channel'     => 'data->telemetries[]->temperature||data->telemetries[]->date',
            'unit'        => '°C',
            'decimals'    => 1,
        ),
    )
);
