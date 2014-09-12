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

    'name' => 'Solar Edge Optimizer',

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
            'type'        => 49, // Solar Edge Optimizer
            'name'        => 'Solar Edge Optimizer',
        ),

        // Real channels
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc',
            'description' => 'DC Power - ADJUST the channel with concrete data!',
            'channel'     => 'P<Inverter>.<String>.<Optimizer> P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc',
            'description' => 'DC Current - ADJUST the channel with concrete data!',
            'channel'     => 'P<Inverter>.<String>.<Optimizer> I',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc1',
            'description' => 'DC Voltage panel - ADJUST the channel with concrete data!',
            'channel'     => 'P<Inverter>.<String>.<Optimizer> V',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc2',
            'description' => 'DC Voltage optimizer - ADJUST the channel with concrete data!',
            'channel'     => 'OP<Inverter>.<String>.<Optimizer> V',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            // Real source is "Energy last hour", so handle with auto adjusted meter channel
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'description' => 'Energy production',
            'channel'     => 'P<Inverter>.<String>.<Optimizer> E',
            'resolution'  => 1,
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
            'adjust'      => 1,
        ),
    )
);
