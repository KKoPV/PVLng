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

    'name' => 'SMA Sensorbox',

    'description' => '
        <a href="http://www.sma.de/en/products/monitoring-control/sunny-sensorbox.html">SMA Sensorbox</a>
        connected to a SMA&nbsp;Webbox with some environment channels.
        <ul>
            <li>Irradiation (W/m²)</li>
            <li>Module temperature (°C)</li>
            <li>Ambient temperature (°C)</li>
            <li>Wind speed (m/s)</li>
            <li>Irradiation (external) (W/m²)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 42, // SMA Sensorbox
            'name'        => 'SMA Sensorbox',
        ),

        // Real channels
        array(
            'type'        => 50, // Irradiation sensor
            'name'        => 'Irradiation',
            'channel'     => 'IntSolIrr',
            'unit'        => 'W/m^2',
            'decimals'    => 0,
        ),

        array(
            'type'        => 51, // Temperature sensor
            'name'        => 'Module temperature',
            'channel'     => 'TmpMdul C',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 51, // Temperature sensor
            'name'        => 'Ambient temperature',
            'channel'     => 'TmpAmb C',
            'unit'        => '°C',
            'decimals'    => 1,
        ),

        array(
            'type'        => 52, // Wind speed sensor
            'name'        => 'Wind speed',
            'channel'     => 'WindVel m/s',
            'unit'        => 'm/s',
            'decimals'    => 0,
        ),

        array(
            'type'        => 53, // Irradiation sensor
            'name'        => 'Irradiation extern',
            'channel'     => 'ExlSolIrr',
            'unit'        => 'W/m^2',
            'decimals'    => 0,
        ),

    )
);
