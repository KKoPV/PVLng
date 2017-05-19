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

    'name' => 'Weather station',

    'description' => '
        <a href="http://www.heavyweather.info/english_uk/english_uk_2300.html" target="_blank">
            LaCrosse WS2300 Weather station
        </a>
        with pre-configured sensors:
        <ul>
            <li>Temperature indoor (°C)</li>
            <li>Temperature outdoor (°C)</li>
            <li>Dewpoint (°C)</li>
            <li>Windchill (°C)</li>
            <li>Wind speed (m/s)</li>
            <li>Wind direction text</li>
            <li>Wind direction (°)</li>
            <li>Humidity indoor (%)</li>
            <li>Humidity outdoor (%)</li>
            <li>Rainfall (mm/h)</li>
            <li>Air pressure (hPa)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 95, // LaCrosse WS2300
            'name'        => 'LaCrosse WS2300',
        ),

        // Real channels
        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature indoor',
            'unit'        => '°C',
            'decimals'    => 1,
            'channel'     => 'TempIndoor',
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature outdoor',
            'unit'        => '°C',
            'decimals'    => 1,
            'channel'     => 'TempOutdoor',
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Dewpoint',
            'unit'        => '°C',
            'decimals'    => 1,
            'channel'     => 'Dewpoint',
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Windchill',
            'unit'        => '°C',
            'decimals'    => 1,
            'channel'     => 'Windchill',
        ),

        array(
            'type'        => 63, // Windspeed sensor
            'name'        => 'Wind speed',
            'unit'        => 'm/s',
            'decimals'    => 1,
            'channel'     => 'WindSpeed',
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Wind direction text',
            'numeric'     => 0,
            'channel'     => 'WindDirectionText',
            'extra'       => 1, // Only non-empty values allowed
        ),

        array(
            'type'        => 67, // Winddirection sensor
            'name'        => 'Wind direction',
            'unit'        => '°',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 360,
            'channel'     => 'WindDirection',
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Relative humidity indoor',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
            'channel'     => 'RelativeHumidityIndoor',
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Relative humidity outdoor',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
            'channel'     => 'RelativeHumidityOutdoor',
        ),

        array(
            'type'        => 68, // Rainfall sensor
            'name'        => 'Rainfall',
            'unit'        => 'mm/h',
            'decimals'    => 0,
            'channel'     => 'Rain',
        ),

        array(
            'type'        => 58, // Pressure sensor
            'name'        => 'Relative pressure',
            'unit'        => 'hPa',
            'decimals'    => 1,
            'channel'     => 'RelativePressure',
        )
    )
);
