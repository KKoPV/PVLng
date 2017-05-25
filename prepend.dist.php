<?php
/**
 * Add here extra needed code for your installation
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
*/

/**
 * Example for monitored installation by Newrelic ()
 */
# if (extension_loaded('newrelic')) newrelic_set_appname('PVLng');

/**
 * If you encounter problems, make PVLng more chatty
 */
# PVLng\PVLng::$DEVELOP = true;
