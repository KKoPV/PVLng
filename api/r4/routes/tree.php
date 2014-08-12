<?php

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
          ->setComment('Alias of "'.$channel->getName()
                     . ($channel->getDescription() ? ' ('.$channel->getDescription().')' : '')
                     . '"')
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
    if (Channel::ById($id)->removeFromTree()) {
        $api->halt(204);
    } else {
        $api->stopAPI('Unable to delete node '.$node);
    }
})->name('DELETE /tree/:id')->help = array(
    'since'       => 'r4',
    'description' => 'Delete channel from channel hierarchy',
    'apikey'      => TRUE
);
