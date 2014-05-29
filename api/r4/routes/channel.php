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
$api->get('/channels', function() use ($api) {
    $tbl = new ORM\ChannelView;

    $find['type_id'] = 0;
    $find['childs'] = 0;

    if ($api->APIKeyValid == 0) $find['public'] = 1;

    $channels = array();
    foreach ($tbl->findMany(array_keys($find), array_values($find)) as $channel) {
        $channels[] = $channel->getAll();
    }

    $api->render($channels);
})->name('channels')->help = array(
    'since'       => 'r3',
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
    'since'       => 'r3',
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
    'since'       => 'r3',
    'description' => 'Fetch all channel attributes or specific channel attribute',
);
