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

    // -----------------------------------------------------------------------
    // INTERNAL SETTINGS, DO NOT TOUCH
    // -----------------------------------------------------------------------
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
