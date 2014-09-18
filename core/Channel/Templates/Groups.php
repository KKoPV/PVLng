<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     2.0.0
 *
 * 1.0.0
 * - Inital creation
 */
return array(

    'name' => 'Groups',

    'description' => 'Several grouping channels for general purposes',

    'channels' => array(

        // Real channels
        // If you don't need grouping channel and create just a bunch of channels,
        // start with index = 1
        1 => array(
            'type'        => 5, // Group
            'name'        => 'Calculations',
            'description' => 'Group general calculations, e.g. below an inverter',
            'numeric'     => 0
        ),

        array(
            'type'        => 5, // Group
            'name'        => 'Power',
            'description' => 'Group power calculations',
            'numeric'     => 0
        ),

        array(
            'type'        => 5, // Group
            'name'        => 'Energy',
            'description' => 'Group energy calculations',
            'numeric'     => 0
        )
    )
);
