<?php
/**
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 *
 */
$api->get('/:guid', $accessibleChannel, function($guid) use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes());
})->name('channel attributes')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch attributes',
);

/**
 *
 */
$api->get('/:guid/:attribute', $accessibleChannel, function($guid, $attribute='') use ($api) {
    $api->render(Channel::byGUID($guid)->$attribute);
})->conditions(array(
    'attribute' => '\w+'
))->name('single channel attribute')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch single channel attribute',
);

/**
 *
 */
$api->get('/attributes/:guid(/:attribute)', $accessibleChannel, function($guid, $attribute='') use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes($attribute));
})->conditions(array(
    'attribute' => '\w+'
))->name('all or single channel attribute')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch all channel attributes or specific channel attribute',
);
