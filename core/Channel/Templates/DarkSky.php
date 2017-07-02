<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2014-2017 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * 1.0.0
 * - Inital creation
 */
return array(

    'name' => 'Dark Sky weather forecast API',

    'description' => '
        <a href="https://darksky.net/dev/docs">Dark Sky API documentation overview</a>
        <ul>
            <li>Condition (text)</li>
            <li>Temperature (°C)</li>
            <li>Apparent temperature (°C)</li>
            <li>Dew point (°C)</li>
            <li>Humidity (%)</li>
            <li>Wind speed (m/s)</li>
            <li>Wind direction (°)</li>
            <li>Air pressure (hPa)</li>
            <li>Visibility (km)</li>
            <li>Cloud cover (%)</li>
            <li>Rain probability (%)</li>
            <li>Ozone (Dobson)</li>
            <li>Icon (text)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 96, // Dark Sky
            'name'        => 'Dark Sky weather',
        ),

        // Real channels
        array(
            'type'        => 91, // Switch
            'name'        => 'Condition',
            'channel'     => 'summary',
            'numeric'     => 0,
            'extra'       => 1, // Only nonempty values
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature',
            'channel'     => 'temperature',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Apparent temperature',
            'channel'     => 'apparentTemperature',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Dew point',
            'channel'     => 'dewPoint',
            'unit'        => '°C',
             'decimals'    => 1,
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity',
            'channel'     => 'humidity',
            'unit'        => '%',
            'decimals'    => 0,
            'resolution'  => 100,
            'valid_from'  => 0,
            'valid_to'    => 1,
        ),

        array(
            'type'        => 63, // Windspeed sensor
            'name'        => 'Wind speed',
            'channel'     => 'windSpeed',
            'unit'        => 'm/s',
            'decimals'    => 0,
        ),

        array(
            'type'        => 67, // Winddirection sensor
            'name'        => 'Wind direction',
            'channel'     => 'windBearing',
            'unit'        => '°',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 360,
        ),

        array(
            'type'        => 58, // Pressure sensor
            'name'        => 'Air pressure',
            'channel'     => 'pressure',
            'unit'        => 'hPa',
            'decimals'    => 0,
        ),

        array(
            'type'        => 69, // Sensor
            'name'        => 'Visibility',
            'channel'     => 'visibility',
            'unit'        => 'km',
            'comment'     => 'Value is capped at 10 miles',
            'decimals'    => 1,
        ),

        array(
            'type'        => 64, // Irradiation sensor
            'name'        => 'Cloud cover',
            'channel'     => 'cloudCover',
            'unit'        => '%',
            'decimals'    => 0,
            'resolution'  => 100,
            'valid_from'  => 0,
            'valid_to'    => 1,
        ),

        array(
            'type'        => 68, // Rainfall sensor
            'name'        => 'Precipitation probability',
            'channel'     => 'precipProbability',
            'unit'        => '%',
            'decimals'    => 0,
            'resolution'  => 100,
            'valid_from'  => 0,
            'valid_to'    => 1,
        ),

        array(
            'type'        => 57, // Luminosity sensor
            'name'        => 'Ozone',
            'channel'     => 'ozone',
            'unit'        => 'Dobson',
            'decimals'    => 0,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Condition icon',
            'channel'     => 'icon',
            'numeric'     => 0,
            'extra'       => 1, // Only nonempty values
        )
    )
);
