<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 *
 * 1.0.0
 * - Inital creation
 */
return array(

    'name' => 'Inverter',

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
        Adjust e.g. units, decimals and public settings afterwards.
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 2, // Inverter
            'name'        => 'Inverter',
        ),

        // Real channels
        array(
            'type'        => 50, // Energy meter, absolute
            'name'        => 'E-Total',
            'description' => 'Energy production',
            'resolution'  => 1000,
            'unit'        => 'Wh',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pac',
            'description' => 'AC Power',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc1',
            'description' => 'DC Power string 1',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc1',
            'description' => 'DC Voltage string 1',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc1',
            'description' => 'DC Current string 1',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc2',
            'description' => 'DC Power string 2',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc2',
            'description' => 'DC Voltage string 2',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc2',
            'description' => 'DC Current string 2',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 51, // Power sensor
            'name'        => 'Pdc3',
            'description' => 'DC Power string 3',
            'unit'        => 'W',
            'decimals'    => 0,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 52, // Voltage sensor
            'name'        => 'Udc3',
            'description' => 'DC Voltage string 3',
            'unit'        => 'V',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 53, // Current sensor
            'name'        => 'Idc3',
            'description' => 'DC Current string 3',
            'unit'        => 'A',
            'decimals'    => 1,
            'valid_from'  => 0.001,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Inverter mode',
            'description' => 'Switch for state changes',
            'numeric'     => 0,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Inverter error',
            'description' => 'Switch for state changes',
            'numeric'     => 0,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Inverter temperature',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 66, // Frequency sensor
            'name'        => 'Mains frequency',
            'unit'        => 'Hz',
            'decimals'    => 2,
            'valid_from'  => 40,
            'valid_to'    => 60,
        )
    )
);
