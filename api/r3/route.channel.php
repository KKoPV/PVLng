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
$api->get('/channels', function() use ($api) {
    $q = new DBQuery('pvlng_channel_view');
    $q->whereNE('type_id', 0)
      ->whereEQ('childs', 0);

    if ($api->APIKeyValid == 0) $q->whereEQ('public', 1);

    $api->render($api->db->queryRowsArray($q));

})->name('channels')->help = array(
    'since'       => 'v3',
    'description' => 'Fetch attributes',
);

/**
 *
 */
$api->get('/channel/:guid', $accessibleChannel, function($guid) use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes());
})->conditions(array(
    'attribute' => '\w+'
))->name('channel GUID')->help = array(
    'since'       => 'v3',
    'description' => 'Fetch single channel attribute',
);

/**
 *
 */
$api->get('/channel/:guid/:attribute', $accessibleChannel, function($guid, $attribute) use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes($attribute));
})->conditions(array(
    'attribute' => '\w+'
))->name('channel GUID attribute')->help = array(
    'since'       => 'v3',
    'description' => 'Fetch all channel attributes or specific channel attribute',
);
