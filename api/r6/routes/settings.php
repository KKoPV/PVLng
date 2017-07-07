<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->get(
    '/settings/:scope/:name/:key',
    $APIkeyRequired,
    function ($scope, $name, $key) use ($api) {
        if ($scope == 'null') {
            $scope = '';
        }
        if ($name  == 'null') {
            $name  = '';
        }
        if ($key   == 'null') {
            $key   = '';
        }
        $api->render(\ORM\Settings::getScopeValue($scope, $name, $key));
    }
)
->name('GET /settings/:scope/:name/:key')
->help = array(
    'since'       => 'r6',
    'description' => 'Read an application setting value, if a part is empty, '
                   . 'request with "null", e.g. /settings/core/null/Latitude',
    'apikey'      => true
);

/**
 *
 */
$api->get(
    '/settings/title',
    function () use ($api) {
        $api->render(\ORM\Settings::getCoreValue(null, 'title'));
    }
)
->name('GET /settings/title')
->help = array(
    'since'       => 'r6',
    'description' => 'Read application title'
);
