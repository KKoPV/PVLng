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

    'name' => 'Weather station',

    'description' => '
        Weather station with pre-configured sensors:
        <ul>
            <li>Temperature outside (°C)</li>
            <li>Soil temperature (°C)</li>
            <li>Humidity outside (%)</li>
            <li>Wind speed (m/s)</li>
            <li>Wind direction (°)</li>
            <li>Rainfall (mm/h)</li>
            <li>Rainfall absolute (mm)</li>
            <li>Air pressure (hPa)</li>
            <li>Temperatue inside (°C)</li>
            <li>Humidity inside (%)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 4, // Multi-Sensor
            'name'        => 'Weather station',
        ),

        // Real channels
        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature outside',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Soil temperature',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity outside',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        ),

        array(
            'type'        => 63, // Windspeed sensor
            'name'        => 'Wind speed',
            'unit'        => 'm/s',
            'decimals'    => 1,
        ),

        array(
            'type'        => 67, // Winddirection sensor
            'name'        => 'Wind direction',
            'unit'        => '°',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 360,
        ),

        array(
            'type'        => 68, // Rainfall sensor
            'name'        => 'Rainfall',
            'unit'        => 'mm/h',
            'decimals'    => 0,
        ),

        array(
            'type'        => 73, // Rainfall meter
            'name'        => 'Rainfall absolute',
            'unit'        => 'mm',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        array(
            'type'        => 58, // Pressure sensor
            'name'        => 'Air pressure',
            'unit'        => 'hPa',
            'decimals'    => 1,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature inside',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity inside',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        )
    )
);
