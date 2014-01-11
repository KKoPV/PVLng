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
        Weather station (as Multi-Sensor) with pre-configured child sensors:
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
        Adjust e.g. units, decimals and public settings afterwards.
    ',

    'channels' => array(

        /**
         * 0       - grouping channel, optional
         * 1 ... n - Real measuring channels
         *
         * Only required attributes must be filled
         */
        0 => array(
            'type'        => 4, // Multi-Sensor
            'name'        => 'Weather station',
        ),

        1 => array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature outside',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        2 => array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Soil temperature',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        3 => array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity outside',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        ),

        4 => array(
            'type'        => 63, // Windspeed sensor
            'name'        => 'Wind speed',
            'unit'        => 'm/s',
            'decimals'    => 1,
        ),

        5 => array(
            'type'        => 67, // Winddirection sensor
            'name'        => 'Wind direction',
            'unit'        => '°',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 360,
        ),

        6 => array(
            'type'        => 68, // Rainfall sensor
            'name'        => 'Rainfall',
            'unit'        => 'mm/h',
            'decimals'    => 0,
        ),

        7 => array(
            'type'        => 73, // Rainfall meter
            'name'        => 'Rainfall absolute',
            'unit'        => 'mm',
            'decimals'    => 0,
            'meter'       => 1,
        ),

        8 => array(
            'type'        => 58, // Pressure sensor
            'name'        => 'Air pressure',
            'unit'        => 'hPa',
            'decimals'    => 1,
        ),

        9 => array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature inside',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        10 => array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity inside',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        )
    )
);
