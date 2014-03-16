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

    'name' => '<a href="http://www.wunderground.com/" targe="_blank">Wunderground</a> weather data',

    'description' => '
        Pre-configured sensors:
        <ul>
            <li>Temperature (°C)</li>
            <li>Atmospheric pressure (hPa)</li>
            <li>Humidity (%)</li>
            <li>Wind speed (km/h)</li>
            <li>Wind direction (°)</li>
            <li>Condition</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 46, // Wunderground
            'name'        => 'Wunderground',
            'description' => '<a href="http://www.wunderground.com.org/" targe="_blank">Wunderground</a> weather data',
        ),

        // Real channels
        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature',
            'channel'     => 'current_observation->temp_c',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 58, // Pressure sensor
            'name'        => 'Atmospheric pressure',
            'channel'     => 'current_observation->pressure_mb',
            'unit'        => 'hPa',
            'decimals'    => 1,
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity',
            'channel'     => 'current_observation->relative_humidity',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        ),

        array(
            'type'        => 63, // Windspeed sensor
            'name'        => 'Wind speed',
            'channel'     => 'current_observation->wind_kph',
            'unit'        => 'km/h',
            'decimals'    => 1,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Wind direction',
            'channel'     => 'current_observation->wind_degrees',
            'unit'        => '°',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 360,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Condition',
            'channel'     => 'current_observation->weather',
            'numeric'     => 0,
        ),
    )
);
