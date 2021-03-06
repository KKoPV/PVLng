<?php
/**
 * @author      Patrick Feisthammel <patrick.feisthammel@citrin.ch>
 * @copyright   2014 Patrick Feisthammel
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 *
 * 1.0.0
 * - Inital creation
 */

return array(

    'name' => 'Three phase sensor',

    'description' => '
        Accumulator group for three power phases:
        <ul>
            <li>Power sensor, phases 1, 2 and 3 (W)</li>
        </ul>
    ',

    'channels' => array(

        // Grouping channel
        array(
            'type'     => 16, // Accumulator
            'name'     => 'Power three Phases',
            'unit'     => 'W',
            'decimals' => 0,
            'icon'     => '/images/ico/plug.png',
        ),

        // Real channels
        array(
            'type'     => 51, // Power sensor
            'name'     => 'Power sensor phase 1',
            'unit'     => 'W',
            'decimals' => 0,
        ),

        array(
            'type'     => 51, // Power sensor
            'name'     => 'Power sensor phase 2',
            'unit'     => 'W',
            'decimals' => 0,
        ),

        array(
            'type'     => 51, // Power sensor
            'name'     => 'Power sensor phase 3',
            'unit'     => 'W',
            'decimals' => 0,
        )
    )
);
