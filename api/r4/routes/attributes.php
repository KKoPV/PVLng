<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
$api->get('/attributes/:guid(/:attribute)', $accessibleChannel, function($guid, $attribute='') use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes($attribute));
})->conditions(array(
    'attribute' => '\w+'
))->name('fetch attribute(s)')->help = array(
    'since'       => 'r2',
    'description' => 'Fetch all channel attributes or specific channel attribute',
);
