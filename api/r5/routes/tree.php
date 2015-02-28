<?php

/**
 *
 */
$api->get('/tree', function() use ($api) {
    $channels = array();
    // Return without API key only public channels
    foreach ((new ORM\Tree)->getWithParents(!$api->APIKeyValid) as $channel) {
        unset($channel['entity']);
        $channels[] = $channel;
    }
    $api->render($channels);
})->name('GET /tree')->help = array(
    'since'       => 'r5',
    'description' => 'Fetch whole channels hierarchy',
);

/**
 *
 */
  $api->get('/tree/:id', function($id) use ($api) {
    $channels = array();
    // Return without API key only public channels
    foreach ((new ORM\Tree)->getWithParents(!$api->APIKeyValid) as $channel) {
        if ($channel['id'] != $id AND $channel['parent'] != $id) continue;
        unset($channel['entity']);
        $channels[] = $channel;
    }
    $api->render($channels);
})->name('GET /tree/:id')->help = array(
    'since'       => 'r5',
    'description' => 'Fetch a channel and its direct child channels',
);

/**
 * Create Alias
 */
$api->put('/tree/alias/:id', $APIkeyRequired, function($id) use ($api) {
    $channel = new \ORM\Tree($id);

    if (!$channel->getId())   $api->stopAPI('Unkown hierarchy Id: '.$id, 404);
    if ($channel->getAlias()) $api->stopAPI(__('AliasStillExists'));

    $alias = new \ORM\Channel;
    $alias->setType(0)
          // Put channel GUID into Alias "channel" attribute for reference
          ->setChannel($channel->getGuid())
          ->setComment('Alias of "'.$channel->getFullName().'"')
          ->insert();

    if (!$alias->isError()) {
        $api->stopAPI(__('AliasCreated'), 200);
    } else {
        $api->stopAPI($alias->Error());
    }
})->name('PUT /tree/alias/:id')->help = array(
    'since'       => 'r4',
    'description' => 'Create alias from given hierarchy Id',
    'apikey'      => TRUE
);

/**
 * Remove a node from channel tree
 */
$api->delete('/tree/:id', $APIkeyRequired, function($id) use ($api) {
    if (Channel::ById($id, FALSE)->removeFromTree()) {
        $api->halt(204);
    } else {
        $api->stopAPI('Unable to delete node '.$id);
    }
})->name('DELETE /tree/:id')->help = array(
    'since'       => 'r4',
    'description' => 'Delete channel from channel hierarchy',
    'apikey'      => TRUE
);

/**
 * Aliases
 */

/**
 *
 */
$api->get('/hierarchy', function() use ($api) {
    $api->redirect('tree', 301);
})->name('GET /hierarchy')->help = array(
    'since'       => 'r5',
    'description' => 'Alias for '.$api->urlFor('GET /tree'),
);
