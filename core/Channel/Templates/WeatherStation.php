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
        ),

/*
        0 => array(
            'type'        => 4, // Multi-Sensor
            'name'        => 'Weather station',
            'description' => '',
            'serial'      => '',
            'channel'     => '',
            'resolution'  => 1,
            'unit'        => '',
            'decimals'    => 2,
            'meter'       => 0,
            'numeric'     => 1,
            'offset'      => 0,
            'adjust'      => 0,
            'cost'        => 0,
            'threshold'   => NULL,
            'valid_from'  => NULL,
            'valid_to'    => NULL,
            'public'      => 1,
            'comment'     => '',
        ),
*/

    ),

);

/* ***************************************************************************

Channel types

  1   Power plant
  2   Inverter
  3   Building
  4   Multi-Sensor
  5   Group
 10   Random
 11   Fixed value
 12   Estimate
 15   Ratio calculator
 16   Accumulator
 17   Differentiator
 18   Full Differentiator
 19   Sensor to meter
 20   Import / Export
 21   Average
 22   Calculator
 23   History
 24   Baseline
 30   Dashboard channel
 40   SMA Sunny Webbox
 41   SMA Inverter
 42   SMA Sensorbox
 50   Energy meter, absolute
 51   Power sensor
 52   Voltage sensor
 53   Current sensor
 54   Gas sensor
 55   Heat sensor
 56   Humidity sensor
 57   Luminosity sensor
 58   Pressure sensor
 59   Radiation sensor
 60   Temperature sensor
 61   Valve sensor
 62   Water sensor
 63   Windspeed sensor
 64   Irradiation sensor
 65   Timer
 66   Frequency sensor
 67   Winddirection sensor
 68   Rainfall sensor
 70   Gas meter
 71   Radiation meter
 72   Water meter
 73   Rainfall meter
 90   Power sensor counter
 91   Switch
100   PV-Log Plant
101   PV-Log Inverter
102   PV-Log Plant (r2)
103   PV-Log Inverter (r2)
110   Sonnenertrag JSON

*************************************************************************** */
