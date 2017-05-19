<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->get(
    '/tree',
    function() use ($api)
{
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
$api->get(
    '/tree/:id',
    function($id) use ($api)
{
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
    'parameters'  => array(
        'id'          => 'Tree Id of original channel',
    )
);

/**
 * Add channel to Tree
 */
$api->put('/tree/:pguid/:cguid', $APIkeyRequired, function($pguid, $cguid) use ($api)
{
    if ($pguid == '0000-0000-0000-0000-0000-0000-0000-0000') {
        // Interpret as "root node"
        $parent = Channel::byId(1);
    } else {
        $parent = Channel::byGUID($pguid);
    }

    $child = Channel::byGUID($cguid);
    $id = $parent->addChild($child->getId());

    // Recreate child to read all attributes
    $child = Channel::byGUID($id);

})->conditions(array(
    'pguid'       => '(\w{4}-){7}\w{4}',
    'cguid'       => '(\w{4}-){7}\w{4}',
))->name('PUT /tree/:parent/:id')->help = array(
    'since'       => 'r5',
    'description' => 'Add channel to parent',
    'apikey'      => true,
    'parameters'  => array(
        'pguid'       => 'Parent channel GUID',
        'cguid'       => 'Child channel GUID',
    )
);

/**
 * Create Alias
 */
$api->put('/tree/alias/:id', $APIkeyRequired, function($id) use ($api)
{
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
    'apikey'      => true,
    'parameters'  => array(
        'id'          => 'Tree Id of original channel',
    )
);

/**
 * Remove a node from channel tree
 */
$api->delete('/tree/:id', $APIkeyRequired, function($id) use ($api)
{
    if (Channel::ById($id, false)->removeFromTree()) {
        $api->halt(204);
    } else {
        $api->stopAPI('Unable to delete node '.$id);
    }
})->name('DELETE /tree/:id')->help = array(
    'since'       => 'r4',
    'description' => 'Delete channel from channel hierarchy',
    'apikey'      => true,
    'parameters'  => array('id' => 'Tree Id of original channel')
);

/**
 * Aliases
 */

/**
 *
 */
$api->get('/hierarchy', function() use ($api)
{
    $api->redirect('tree', 301);
})->name('GET /hierarchy')->help = array(
    'since'       => 'r5',
    'description' => 'Alias for '.$api->urlFor('GET /tree'),
);
