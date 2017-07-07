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
 * Helper function
 */
$checkLogId = function (Slim\Route $route) use ($api) {

    $id = $route->getParam('id');

    if ($id == 'all') {
        return;
    }

    if ($id == '') {
        $api->stopAPI('Missing log entry Id');
    }

    $log = new ORM\Log($id);

    if ($log->getId() == '') {
        $api->stopAPI('No log entry found for Id: '.$id, 404);
    }
};

/**
 *
 */
$api->put(
    '/log',
    $APIkeyRequired,
    function () use ($api) {
        $request = json_decode($api->request->getBody(), true);

        $log = new ORM\Log;

        $log->setScope(!empty($request['scope']) ? $request['scope'] : 'API r'.$api->version);
        $log->setData(!empty($request['message']) ? trim($request['message']) : '');

        if ($log->insert()) {
            $api->status(201);
            $result = array('status' => 'success', 'id' => $log->id);
        } else {
            $api->status(400);
            $result = array('status' => 'error');
        }

        $api->render($result);
    }
)
->name('PUT /log')
->help = array(
    'since'       => 'r2',
    'description' => 'Store new log entry, scope defaults to \'API '.$api->version.'\'',
    'apikey'      => true,
    'payload'     => array('{"scope":"...", "message":"..."}'=>'Message data'),
    );

/**
 *
 */
$api->get(
    '/log/:id',
    $APIkeyRequired,
    $checkLogId,
    function ($id) use ($api) {
        $log = new ORM\Log($id);

        $result = array(
            'status' => 'success',
            'log'    => array(
                'id'        => +$log->getId(),
                'timestamp' => strtotime($log->timestamp),
                'datetime'  => $log->getTimestamp(),
                'scope'     => $log->getScope(),
                'message'   => $log->getData()
            )
        );

        $api->render($result);
    }
)
->name('GET /log/:id')
->help = array(
    'since'       => 'r2',
    'apikey'      => true,
    'description' => 'Read a log entry',
);

/**
 *
 */
$api->get(
    '/log/all(/:page(/:count))',
    $APIkeyRequired,
    function ($page = 1, $count = 50) use ($api) {
        if ($page < 1) {
            $page  = 1;
        }
        if ($count < 1) {
            $count = 1;
        }

        $result = array(
            'status' => 'success',
            'log'    => array()
        );

        // Read all entries
        $q = DBQuery::forge('pvlng_log')->order('id')->limit($count, ($page-1)*$count);

        if ($res = $api->db->query($q)) {
            while ($log = $res->fetch_object()) {
                $result['log'][] = array(
                    'id'        => +$log->id,
                    'timestamp' => strtotime($log->timestamp),
                    'datetime'  => $log->timestamp,
                    'scope'     => $log->scope,
                    'message'   => $log->data
                );
            }
        }

        $api->render($result);
    }
)
->name('GET /log/all(/:page(/:count))')
->help = array(
    'since'       => 'r2',
    'description' => 'Read all log entries, paginated for :page, :count entries',
    'apikey'      => true,
);

/**
 *
 */
$api->post(
    '/log/:id',
    $APIkeyRequired,
    $checkLogId,
    function ($id) use ($api) {
        $request = json_decode($api->request->getBody(), true);

        $log = new ORM\Log($id);

        $log->setScope(!empty($request['scope']) ? $request['scope'] : 'API r'.$api->version);
        $log->setData(!empty($request['message']) ? trim($request['message']) : '');

        if ($log->replace()) {
            $result = array(
                'status' => 'success',
                'log' => array(array(
                    'id'        => +$log->getId(),
                    'timestamp' => strtotime($log->timestamp),
                    'datetime'  => $log->getTimestamp(),
                    'scope'     => $log->getScope(),
                    'message'   => $log->getData()
                ))
            );
            $api->render($result);
        } else {
            $api->halt(400);
        }
    }
)
->name('POST /log/:id')
->help = array(
    'since'       => 'r2',
    'description' => 'Update a log entry',
    'apikey'      => true,
    'payload'     => array('{"scope":"...", "message":"..."}'=>'Message data'),
);

/**
 *
 */
$api->delete(
    '/log/:id',
    $APIkeyRequired,
    $checkLogId,
    function ($id) use ($api) {
        $log = new ORM\Log($id);
        $api->status($log->delete() ? 204 : 400);
    }
)
->name('DELETE /log/:id')
->help = array(
    'since'       => 'r2',
    'description' => 'Delete a log entry',
    'apikey'      => true,
);
