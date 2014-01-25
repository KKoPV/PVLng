<?php
/**
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
return array(

    /**
     * If you like to use PVLng as middleware for vzlogger you must set this to TRUE
     *
     * But remember, vzlogger doesn't use any security for data manipulation like
     * PVLng API key and this will also save data to private channels!
     *
     * http://wiki.volkszaehler.org/software/controller/vzlogger
     */
    'vzlogger' => array(
        'enabled' => FALSE
    ),

);
