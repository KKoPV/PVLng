<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->put(
    '/channel',
    $APIkeyRequired,
    function () use ($api) {
        $attr = json_decode($api->request->getBody(), true);
        if ($attr === null) {
            throw new Exception('Invalid JSON data', 400);
        }

        foreach (array('type', 'name') as $k) {
            if (empty($attr[$k])) {
                throw new Exception('Attribute \''.$k.'\' is required', 400);
            }
        }

        $channel = new ORM\Channel;

        // Default unit & icon
        $type = new ORM\ChannelType($attr['type']);
        $channel->setUnit($type->getUnit());
        $channel->setIcon($type->getIcon());

        // Set values
        foreach ($attr as $key => $value) {
            $channel->set($key, $value);
        }

        $channel->setThrowException()->insert();

        // Re-read channel to get all attributes
        $channel = Channel\Channel::byChannel($channel->getId());
        // Set HTTP code 201 for "created"
        $api->response->setStatus(201);
        // Return attributes of created channel
        $api->render($channel->getAttributesShort());
    }
)
->name('PUT /channel')
->help = array(
    'since'       => 'r5',
    'description' => 'Create channel',
    'apikey'      => true
);

/**
 *
 */
$api->get(
    '/channels',
    function () use ($api) {
        $channels = array();
        foreach ((new ORM\ChannelView)->find() as $channel) {
            if (!$channel->guid || (!$api->APIKeyValid && !$channel->public)) {
                continue;
            }
            $ch = $channel->asAssoc();
            unset($ch['id']);
            $channels[$channel->id] = $ch;
        }
        ksort($channels);
        $api->render(array_values($channels));
    }
)
->name('GET /channels')->help = array(
    'since'       => 'r3',
    'description' => 'Fetch all channels',
);

/**
 *
 */
$api->get(
    '/channel/:guid',
    $accessibleChannel,
    function ($guid) use ($api) {
        $api->render(Channel\Channel::byGUID($guid)->getAttributesShort());
    }
)
->name('GET /channel/:guid')
->help = array(
    'since'       => 'r3',
    'description' => 'Fetch channel attributes',
);

/**
 *
 */
$api->get(
    '/channel/:guid/stats',
    $accessibleChannel,
    function ($guid) use ($api) {
        $channel = Channel\Channel::byGUID($guid);

        $result = $api->boolParam('attributes', false)
                ? $channel->getAttributes()
                : ['guid' => $channel->guid, 'name' => $channel->name];

        if ($channel->numeric) {
            $q = DBQuery::factory('pvlng_reading_num')
                ->get($q->MIN('timestamp'), 'timestamp_first')
                ->get($q->MAX('timestamp'), 'timestamp_last')
                ->get($q->COUNT('id'), 'readings')
                ->get($q->MIN('data'), 'min')
                ->get($q->MAX('data'), 'max')
                ->get($q->AVG('data'), 'avg')
                ->whereEQ('id', $channel->entity);
        } else {
            $q = DBQuery::factory('pvlng_reading_str')
                ->get($q->MIN('timestamp'), 'timestamp_first')
                ->get($q->MAX('timestamp'), 'timestamp_last')
                ->get($q->COUNT('id'), 'readings')
                ->whereEQ('id', $channel->entity);
        }

        $result = $result + $api->db->queryRowArray($q);

        if ($result['timestamp_last']) {
            $q->reset()
              ->get('data', 'last')
              ->whereEQ('id', $channel->entity)
              ->whereEQ('timestamp', $result['timestamp_last']);
            $result = $result + $api->db->queryRowArray($q);
        } else {
            $result['last'] = null;
        }

        $api->render($result);
    }
)
->conditions(array(
    'attribute' => '\w+'
))
->name('GET /channel/:guid/stats')
->help = array(
    'since'       => 'r5',
    'description' => 'Fetch channel statistics',
);

/**
 *
 */
$api->get(
    '/channel/:guid/:attribute',
    $accessibleChannel,
    function ($guid, $attribute) use ($api) {
        $api->render(array_map(
            function ($a) {
                return html_entity_decode($a);
            },
            Channel\Channel::byGUID($guid)->getAttributes($attribute)
        ));
    }
)->conditions(array(
    'attribute' => '\w+'
))
->name('GET /channel/:guid/:attribute')
->help = array(
    'since'       => 'r3',
    'description' => 'Fetch specific channel attribute',
);

/**
 *
 */
$api->get(
    '/channel/:guid/parent/:attribute',
    $accessibleChannel,
    function ($guid, $attribute) use ($api) {
        $channel = ORM\Tree::f()->filterByGuid($guid)->findOne();
        if (($id = $channel->getId()) == '') {
            $api->stopAPI('No channel found for GUID: '.$guid, 400);
        }
        $parent = Core\PVLng::getNestedSet()->getParent($id)['id'];
        if ($parent == 1) {
            $api->stopAPI('Channel is on top level', 400);
        }

        $api->render(
            array_map(
                function ($a) {
                    return html_entity_decode($a);
                },
                Channel\Channel::byId($parent)->getAttributes($attribute)
            )
        );
    }
)
->conditions(array(
    'attribute' => '\w+'
))
->name('GET /channel/:guid/parent/:attribute')
->help = array(
    'since'       => 'r4',
    'description' => 'Fetch all attributes or a specific attribute from parent channel',
    'error'       => array(
        'No channel found for GUID' => 400,
        'Channel is on top level'   => 400
    )
);

/**
 *
 */
$api->delete(
    '/channel/:id',
    $APIkeyRequired,
    function ($id) use ($api) {
        $channel = new ORM\Channel($id);

        if ($channel->getId()) {
            $channel->delete();
            if (!$channel->isError()) {
                $api->status(204);
            } else {
                $api->stopAPI(I18N::translate($channel->Error(), $channel->getName()), 400);
            }
        } else {
            $api->stopAPI('No channel found for Id '.$id, 404);
        }
    }
)
->name('DELETE /channel/:id')
->help = array(
    'since'       => 'r4',
    'description' => 'Delete channel and its readings',
);
