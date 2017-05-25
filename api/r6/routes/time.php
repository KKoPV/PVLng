<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 * Get actual server time in seconds since 1970
 */
$api->get(
    '/time(/:format)',
    function ($format = 'U') use ($api) {
        $api->contentType('text/plain');
        $api->halt(200, date($format));
    }
)->name('GET /time')->help = array(
    'description' => 'Deliver actual server time',
    'since' => 'r4'
);
