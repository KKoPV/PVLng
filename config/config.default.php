<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
return array(

    /**
     * 1. Change the database credentials for your needs
     */
    'Database' => array(
        'Host'     => '<HOST>',
        'Socket'   => '<SOCKET>',
        'Port'     => '<PORT>',
        'Username' => '<USER>',
        'Password' => '<PASSWORD>',
        'Database' => '<DATABASE>'
    ),

    /**
     * 2. Protect your installation.
     *
     * Call http://your.domain.here/adminpass
     * to setup your administrator account
     */
    'Admin' => array(
        'User'     => NULL,
        'Password' => NULL
    ),

    /**
     * Allow admin to login by IP bound token
     */
    'TokenLogin' => TRUE,

    /**
     * Loaction for sunrise/sunset
     *
     * Decimals
     *
     * Latitude defaults to North, pass in a negative value for South
     * Longitude defaults to East, pass in a negative value for West
     */
    'Location' => array(
        'Latitude'  => NULL,
        'Longitude' => NULL
    ),

    /**
     *  Your personal title :-)
     */
    'Title' => 'PhotoVoltaic Logger new generation',

    /**
     * Currency settings
     */
    'Currency' => array(
        'ISO'      => 'EUR',
        'Symbol'   => '€',
        'Decimals' => 2,
        'Format'   => '%.2f €'
    ),

    /**
     * Preferd cache mode, auto detect best one if empty
     */
    'Cache' => NULL,
    // Force one, if you like:
    # 'Cache' => 'APC',          // Save each key
    # 'Cache' => 'MemCache',     // Save each key
    # 'Cache' => 'MemCacheOne',  // Save all data into one key (like file cache)
    # 'Cache' => 'File',
    # 'Cache' => 'Mock',         // Disable caching complete

    /**
     * View settings
     */
    'View' => array(

        /**
         * FALSE, 0, 10, 62
         */
        'JavaScriptPacker' => 62,

    ),

);
