<?php
/**
 * KEy - Value store routes for external use
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
$api->get(
    '/settings/:scope/:name/:key',
    $APIkeyRequired,
    function($scope, $name, $key) use ($api)
{
    if ($scope == 'null') $scope = '';
    if ($name  == 'null') $name  = '';
    if ($key   == 'null') $key   = '';
    $api->render(\ORM\Settings::getScopeValue($scope, $name, $key));
})->name('GET /settings/:scope/:name/:key')->help = array(
    'since'       => 'r6',
    'description' => 'Read an application setting value, if a part is empty, request with "null", e.g. /settings/core/null/Latitude',
    'apikey'      => TRUE
);
