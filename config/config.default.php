<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
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
     * Default language, 'en' or 'de', Translators are welcome :-)
     *
     * Will be overwritten by browsers accept language settings if exists
     */
    'Language' => 'en',

    /**
     * Defaults, no need to change
     *
     * Login cookie settings for private installation
     */
    'Cookie' => array(
        'Name' => 'PVLng',
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
     * Model flags
     */
    'Model' => array(

        'Daylight' => array(
            /**
             * Average of last ? days
             */
            'CurveDays' => 5,
        ),

        /**
         * How to calculate the average of data in History channels
         *
         * 0 - linear (default)
         * 1 - harmonic
         * 2 - geometric
         */
         'History' => array(
             'Average' => 0
         ),

         'Estimate' => array(
             'Marker' => '/images/energy.png'
         ),
    ),

    /**
     * Controller settings
     */
    'Controller' => array(

        'Index' => array(
            /**
             * Chart canvas height, width is 940px,
             *
             * - Ratio  5 x  4 : 752
             * - Ratio  4 x  3 : 705
             * - Ratio 16 x 10 : 587
             * - Ratio 10 x  6 : 564
             * - Ratio 16 x  9 : 528
             */
            'ChartHeight' => 528,

            /**
             * Chart auto refresh timeout in sec., set 0 for no automatic refresh
             */
            'Refresh' => 300,

            /**
             * Show notification for each loaded channel
             */
            'NotifyLoadEach' => FALSE,

            /**
             * Show notification for loaded channels when all channels was loaded
             */
            'NotifyLoadAll' => TRUE,
        ),

        'Dashboard' => array(
            /**
             * Login for dashboards required?
             */
            'Login' => TRUE,
        ),

        'Tariff' => array(
            /**
             * Tariff price line count
             */
            'TimesLineCount' => 6,
        ),

        'Mobile' => array(
            /**
             * Chart canvas height
             */
            'ChartHeight' => 320,
        ),

    ),

    /**
     * View settings
     */
    'View' => array(

        /**
         * Replace static images by base64 encoded inline code
         * Only for images with max x/y of 8px
         */
        'InlineImages'     => 8,

        /**
         * FALSE, 0, 10, 62
         */
        'JavaScriptPacker' => 62,

    ),

);
