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

    'name' => 'Kostal Piko Solar Inverter',

    'description' => '
        Kostal Piko Solar inverter with up to 3 MPPs
        <ul>
            <li>E-Total lifetime, readings in kWh, output in Wh</li>
            <li>Pdc1, Pdc2, Pdc3 (W)</li>
            <li>Udc1, Udc2, Udc3 (V)</li>
            <li>Idc1, Idc2, Idc3 (A)</li>
            <li>Pac1, Pac2, Pac3 (W)</li>
            <li>Uac1, Uac2, Uac3 (V)</li>
            <li>Iac1, Iac2, Iac3 (A)</li>
            <li>Mains frequency (Hz)</li>
            <li>Accumulators for Pdc, Pac (W)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 47, // Kostal Piko Inverter
            'name'        => 'Kostal Piko',
        ),

        // Real channels
        array(
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'description' => 'Energy production',
            'channel'     => 'total E',
            'resolution'  => 1000,
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        /**
         * DC MPP 1
         */
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc1',
            'description' => 'DC Power string 1',
            'channel'     => 'DC1 P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc1',
            'description' => 'DC Voltage string 1',
            'channel'     => 'DC1 U',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc1',
            'description' => 'DC Current string 1',
            'channel'     => 'DC1 I',
            'resolution'  => 0.001,
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        /**
         * DC MPP 2
         */
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc2',
            'description' => 'DC Power string 2',
            'channel'     => 'DC2 P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc2',
            'description' => 'DC Voltage string 2',
            'channel'     => 'DC2 U',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc2',
            'description' => 'DC Current string 2',
            'channel'     => 'DC2 I',
            'resolution'  => 0.001,
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        /**
         * DC MPP 3
         */
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc3',
            'description' => 'DC Power string 3',
            'channel'     => 'DC3 P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc3',
            'description' => 'DC Voltage string 3',
            'channel'     => 'DC3 U',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc3',
            'description' => 'DC Current string 3',
            'channel'     => 'DC3 I',
            'resolution'  => 0.001,
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        /**
         * AC phase 1
         */
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac1',
            'description' => 'AC Power phase 1',
            'channel'     => 'AC1 P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac1',
            'description' => 'AC Voltage phase 1',
            'channel'     => 'AC1 U',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac1',
            'description' => 'AC Current phase 1',
            'channel'     => 'AC1 I',
            'resolution'  => 0.001,
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        /**
         * AC phase 2
         */
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac2',
            'description' => 'AC Power phase 2',
            'channel'     => 'AC2 P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac2',
            'description' => 'AC Voltage phase 2',
            'channel'     => 'AC2 U',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac2',
            'description' => 'AC Current phase 2',
            'channel'     => 'AC2 I',
            'resolution'  => 0.001,
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        /**
         * AC phase 3
         */
        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac3',
            'description' => 'AC Power phase 3',
            'channel'     => 'AC3 P',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Uac3',
            'description' => 'AC Voltage phase 3',
            'channel'     => 'AC3 U',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Iac3',
            'description' => 'AC Current phase 3',
            'channel'     => 'AC3 I',
            'resolution'  => 0.001,
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Mains frequency',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        ),

        /**
         * Accumulators
         */
        array(
            'type'        => 16, // Accumulator
            'name'        => 'Pdc',
            'description' => 'DC Power',
            'unit'        => 'W',
            'decimals'    => 0,
            // Add the following just created channels by id
            '_'           => array( 2, 5, 8 )
        ),

        array(
            'type'        => 16, // Accumulator
            'name'        => 'Pac',
            'description' => 'AC Power',
            'unit'        => 'W',
            'decimals'    => 0,
            // Add the following just created channels by id
            '_'           => array( 11, 14, 17 )
        )
    )
);
