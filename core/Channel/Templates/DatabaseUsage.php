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

    'name' => 'Database usage',

    'description' => '
        Accumulator group for the two database tables with readings,
        will show the rows count
        <ul>
            <li>Numeric readings</li>
            <li>Non-numeric readings</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'        => 27, // Full Accumulator
            'name'        => 'Database reading rows',
            'decimals'    => 0,
            'unit'        => 'rows',
            'public'      => 0 // private by default
        ),

        // Real channels
        array(
            'type'        => 99, // Database usage
            'name'        => 'Database reading rows',
            'description' => 'numeric',
            'decimals'    => 0,
            'meter'       => 1,
            'unit'        => 'rows',
            'public'      => 0, // private by default
            'extra'       => 1  // numeric
        ),

        array(
            'type'        => 99, // Database usage
            'name'        => 'Database reading rows',
            'description' => 'non-numeric',
            'decimals'    => 0,
            'meter'       => 1,
            'unit'        => 'rows',
            'public'      => 0, // private by default
            'extra'       => 0  // non-numeric
        )
    )
);
