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

    'name' => 'SMA Sunnboy/Tripower inverter',

    'description' => '
        <a href="http://www.sma.de/en/products/solarinverters">SMA Sunny Boy or Tripower inverter</a>
        connected to a SMA&nbsp;Webbox with up to 6 strings (A1...A5, B):
        <ul>
            <li>E-Total lifetime, readings in kWh, output in Wh</li>
            <li>Pac (W)</li>
            <li>Pdc A, Pdc B (W)</li>
            <li>Udc A, Udc B (V)</li>
            <li>Idc A, Idc A1, Idc A2, Idc A3, Idc A4, Idc A5,<br />Idc B, Idc B1 (A)</li>
            <li>Mode (string)</li>
            <li>Error (string)</li>
            <li>Mains frequency (Hz)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 41, // SMA Inverter
            'name'        => 'SMA Inverter',
        ),

        // Real channels
        array(
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'description' => 'Energy production',
            'channel'     => 'E-Total',
            'resolution'  => 1000,
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac',
            'description' => 'AC Power',
            'channel'     => 'Pac',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc A',
            'description' => 'DC Power string A',
            'channel'     => 'A.Ms.Watt',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc A',
            'description' => 'DC Voltage string A',
            'channel'     => 'A.Ms.Vol',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc A',
            'description' => 'DC Current string A',
            'channel'     => 'A.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc A1',
            'description' => 'DC Current string A1',
            'channel'     => 'A1.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc A2',
            'description' => 'DC Current string A2',
            'channel'     => 'A2.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc A3',
            'description' => 'DC Current string A3',
            'channel'     => 'A3.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc A4',
            'description' => 'DC Current string A4',
            'channel'     => 'A4.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc A5',
            'description' => 'DC Current string A5',
            'channel'     => 'A5.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc B',
            'description' => 'DC Power string B',
            'channel'     => 'B.Ms.Watt',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc B',
            'description' => 'DC Voltage string B',
            'channel'     => 'B.Ms.Vol',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc B',
            'description' => 'DC Current string B',
            'channel'     => 'B.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc B1',
            'description' => 'DC Current string B1',
            'channel'     => 'B1.Ms.Amp',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Inverter mode',
            'description' => 'Switch for state changes',
            'channel'     => 'Mode',
            'numeric'     => 0,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Inverter error',
            'description' => 'Switch for state changes',
            'channel'     => 'Error',
            'numeric'     => 0,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Mains frequency',
            'channel'     => 'GridMs.Hz',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        )
    )
);
