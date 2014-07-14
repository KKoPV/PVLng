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
    $channels = array();
    foreach ((new ORM\ChannelView)->find() as $channel) {
        if ($api->APIKeyValid OR $channel->public) $channels[$channel->id] = $channel->asAssoc();
    }
    ksort($channels);
    $api->render($channels);
})->name('channels')->help = array(
    'since'       => 'r3',
    'description' => 'Fetch all channels',
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
