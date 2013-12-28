<?php
/**
 * Hooks must be defined in directory public_html/hooks
 *
 * The system
 * - looks for a file <hook name>.class.php
 * - require_once $file
 * - calls <hook name>( &$entity, $config )
 *
 * Example
 *
 * 'data.save.after' => array(
 *   'MyHook' => array(
 *     config data ...
 *   )
 * ),
 *
 * file: MyHook.class.php
 * function: data_save_after( &$entity, $config )
 *
 * So multiple hook functions can be defined in file
 *
 */
return array(

    'data.save.before' => array(
    ),

    'data.save.after' => array(

/*
        'XivelyStatus' => array(

            // Repeat for all PVLng channels as needed >>>

            // PVLng channel GUID as key
            '...' => array(
                // API end point
                'https://api.xively.com/v2/feeds/...',
                // Your API key
                '...',
                // Xively channel name
                '...',
                // Format, optional, mostly for temperature channels
                // '%.1f'
            ),

            // <<< Repeat for all channels as needed

        ),
*/

    ),

    'data.read.after' => array(
    ),

);
