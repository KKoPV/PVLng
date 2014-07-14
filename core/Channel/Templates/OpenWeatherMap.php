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

    'name' => '<a href="http://openweathermap.org/" target="_blank">OpenWeatherMap</a> weather data',

    'description' => '
        Pre-configured sensors:
        <ul>
            <li>Temperature (°C)</li>
            <li>Atmospheric pressure (hPa)</li>
            <li>Humidity (%)</li>
            <li>Wind speed (m/s)</li>
            <li>Wind direction (°)</li>
            <li>Clouds (%)</li>
            <li>Condition</li>
            <li>Condition code</li>
            <li>Condition icon Id</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 45, // OpenWeatherMap
            'name'        => 'OpenWeatherMap',
            'description' => 'Weather data',
        ),

        // Real channels
        array(
            'type'        => 60, // Temperature sensor
            'name'        => 'Temperature',
            'channel'     => 'main->temp',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 58, // Pressure sensor
            'name'        => 'Atmospheric pressure',
            'channel'     => 'main->pressure',
            'unit'        => 'hPa',
            'decimals'    => 1,
        ),

        array(
            'type'        => 56, // Humidity sensor
            'name'        => 'Humidity',
            'channel'     => 'main->humidity',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        ),

        array(
            'type'        => 63, // Windspeed sensor
            'name'        => 'Wind speed',
            'channel'     => 'wind->speed',
            'unit'        => 'm/s',
            'decimals'    => 1,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Wind direction',
            'channel'     => 'wind->deg',
            'unit'        => '°',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 360,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Clouds',
            'channel'     => 'clouds->all',
            'unit'        => '%',
            'decimals'    => 0,
            'valid_from'  => 0,
            'valid_to'    => 100,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Condition',
            'channel'     => 'weather->0->description',
            'numeric'     => 0,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Condition code',
            'channel'     => 'weather->0->id',
            'decimals'    => 0,
        ),

        array(
            'type'        => 91, // Switch
            'name'        => 'Condition icon Id',
            'channel'     => 'weather->0->icon',
            'numeric'     => 0,
        ),
    )
);
