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

    'name' => 'Server load channels',

  'description' => 'Data from /proc/loadavg',

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
            'name'        => 'Server load',
            'public'      => 0, // private by default
            'icon'        => '/images/ico/clock.png'
        ),

        // Real channels
        // If you don't need grouping channel and create just a bunch of channels,
        // start with index = 1
        array(
            'type'        => 65, // Timer
            'name'        => 'Load average last minute',
            'unit'        => 'processes',
            'public'      => 0, // private by default
        ),

        array(
            'type'        => 65, // Timer
            'name'        => 'Load average last 5 minutes',
            'unit'        => 'processes',
            'public'      => 0, // private by default
        ),

        array(
            'type'        => 65, // Timer
            'name'        => 'Load average last 15 minutes',
            'unit'        => 'processes',
            'public'      => 0, // private by default
        )
    )
);
