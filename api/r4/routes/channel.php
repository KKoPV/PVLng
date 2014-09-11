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
})->name('GET /channels')->help = array(
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
))->name('GET /channel/:guid')->help = array(
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
))->name('GET /channel/:guid/:attribute')->help = array(
    'since'       => 'r3',
    'description' => 'Fetch all channel attributes or specific channel attribute',
);

/**
 *
 */
$api->delete('/channel/:id', $APIkeyRequired, function($id) use ($api) {
    $channel = new ORM\Channel($id);

    if ($channel->getId()) {
        $channel->delete();
        if (!$channel->isError()) {
            $api->status(204);
        } else {
            $api->stopAPI(__($channel->Error(), $channel->getName()), 400);
        }
    } else {
        $api->stopAPI('No channel found for Id '.$id, 404);
    }
})->name('DELETE /channel/:id')->help = array(
    'since'       => 'r4',
    'description' => 'Delete channel and its readings',
);
