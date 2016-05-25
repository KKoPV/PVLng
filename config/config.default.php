<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
return [

    /**
     * Change the database credentials for your needs
     */
    'Database' => [
        'Host'     => '<HOST>',
        'Socket'   => '<SOCKET>',
        'Port'     => '<PORT>',
        'Username' => '<USER>',
        'Password' => '<PASSWORD>',
        'Database' => '<DATABASE>'
    ],

    /**
     * Preferd cache mode, auto detect best one if empty
     */
    'Cache' => null
    // Force one, if you like,
    // MemCache is best tested and recommended!
    # 'Cache' => 'MemCache'     // MUST BE ISTALLED AND ENABLED!
    # 'Cache' => 'APC'          // MUST BE ISTALLED AND ENABLED!
    # 'Cache' => 'Mock'         // Disable caching complete

];
