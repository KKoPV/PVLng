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
     *
     */
    'Title' => 'PhotoVoltaic Logger new generation',

    /**
     * Currency settings
     */
    'Currency' => array(
        'ISO'      => 'EUR',
        'Symbol'   => '€',
        'Decimals' => 2
    ),

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

    /**
     * Model flags
     */
    'Model' => array(

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
    ),

    /**
     * View settings
     */
    'View' => array(

        // Compile templates and reuse unless changed
        'ReuseCode'        => TRUE,

        // Replace static images by base64 encoded inline code
        // Only for images with max x/y of 8px
        'InlineImages'     => 8,

        // Don't compress HTML
        'Verbose'          => FALSE,

        // FALSE, 0, 10, 62, (95 is not working with UTF-8)
        'JavaScriptPacker' => 62,

        // XML View settings
        'XML' => array(
            'Node' => 'node',
            'Data' => 'data',
        ),
    ),

);
