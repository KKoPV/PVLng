<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     2.0.0
 *
 * 2.0.0
 * - 1st channel is always the grouping channel
 *
 * 1.0.0
 * - Inital creation
 */
return array(

    'name' => 'A Meaningful name for selection ...',

    'description' => 'Describe the channels here, see the example templates',

    'channels' => array(

        /**
         * 1st channel - Grouping channel
         * other       - Real channels
         *
         * Only required attributes must be filled
         */

        // Grouping channel
        array(
            'type'        => 5, // Group, available types at end of file
            'name'        => '',
            'description' => '',
            'comment'     => '',
        ),

        // Real channels
        // If you don't need grouping channel and create just a buch of channels,
        // start with index = 1
        # 1 => array(
        array(
            'type'        => 0,
            'name'        => '',
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
            'tariff'      => 0,
            'threshold'   => null,
            'valid_from'  => null,
            'valid_to'    => null,
            'public'      => 1,
            'tags'        => '',
            'extra'       => '', //	Not visible field for models to store extra info
            'comment'     => ''
        ),

    ),

);

/* ***************************************************************************
** Channel types
** ***************************************************************************
  1 - Power plant
  2 - Inverter
  3 - Building
  4 - Multi-Sensor
  5 - Group
 10 - Random
 11 - Fixed value
 12 - Estimate
 13 - Daylight
 15 - Ratio calculator
 16 - Accumulator
 17 - Differentiator
 18 - Full Differentiator
 19 - Sensor to meter
 20 - Import / Export
 21 - Average
 22 - Calculator
 23 - History
 24 - Baseline
 25 - Topline
 26 - Meter to sensor
 27 - Full Accumulator
 28 - Selector
 29 - Multiplier
 30 - Dashboard channel
 31 - Solar Estimate
 32 - Averageline
 40 - Kaco Inverter
 41 - SMA Inverter
 42 - SMA Sensorbox
 43 - Fronius Inverter
 44 - Fronius Sensorbox
 45 - OpenWeatherMap
 46 - Wunderground
 47 - Kostal Piko Inverter
 48 - Solar Edge Inverter
 49 - Solar Edge Optimizer
 50 - Energy meter absolute
 51 - Power sensor
 52 - Voltage sensor
 53 - Current sensor
 54 - Gas sensor
 55 - Heat sensor
 56 - Humidity sensor
 57 - Luminosity sensor
 58 - Pressure sensor
 59 - Radiation sensor
 60 - Temperature sensor
 61 - Valve sensor
 62 - Water sensor
 63 - Windspeed sensor
 64 - Irradiation sensor
 65 - Timer
 66 - Frequency sensor
 67 - Winddirection sensor
 68 - Rainfall sensor
 69 - Sensor
 70 - Gas meter
 71 - Radiation meter
 72 - Water meter
 73 - Rainfall meter
 74 - Meter
 80 - Percentage
 90 - Power sensor counter
 91 - Switch
 99 - Database usage
100 - PV-Log Plant
101 - PV-Log Inverter
102 - PV-Log JSON 1.1 Plant
103 - PV-Log JSON 1.1 Inverter
110 - Sonnenertrag JSON

*/
