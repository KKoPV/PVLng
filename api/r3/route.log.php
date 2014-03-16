<?php
/**
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 * Helper function
 */
$checkLogId = function(Slim\Route $route) use ($api) {

    $id = $route->getParam('id');

    if ($id == 'all') return;

    if ($id == '') {
        $api->stopAPI('Missing log entry Id');
    }

    $log = new ORM\Log($id);

    if ($log->id == '') {
        $api->stopAPI('No log entry found for Id: '.$id, 404);
    }
};

/**
 *
 */
$api->put('/log', $APIkeyRequired, function() use ($api) {

    $request = json_decode($api->request->getBody(), TRUE);

    $log = new ORM\Log;

    $log->scope = !empty($request['scope']) ? $request['scope'] : 'API r'.$api->version;
    $log->data  = !empty($request['message']) ? trim($request['message']) : '';

    if ($log->insert()) {
        $api->status(201);
        $result = array('status' => 'success', 'id' => $log->id);
    } else {
        $api->status(400);
        $result = array('status' => 'error');
    }

    $api->render($result);
    $api->stop();

})->name('put log')->help = array(
    'since'       => 'v2',
    'description' => 'Store new log entry, scope defaults to \'API r'.$api->version.'\'',
    'payload'     => '{"scope":"...", "message":"..."}',
);

/**
 *
 */
$api->get('/log/:id', $APIkeyRequired, $checkLogId, function($id) use ($api) {

    $log = new ORM\Log($id);

    $result = array(
        'status' => 'success',
        'log'    => array(
            'id'        => +$log->id,
            'timestamp' => strtotime($log->timestamp),
            'datetime'  => $log->timestamp,
            'scope'     => $log->scope,
            'message'   => $log->data
        )
    );

    $api->render($result);

})->name('read log entry')->help = array(
    'since'       => 'v2',
    'description' => 'Read a log entry',
);

/**
 *
 */
$api->get('/log/all(/:page(/:count))', $APIkeyRequired, function($page=1, $count=50) use ($api) {

    if ($page < 1) $page = 1;
    if ($count < 1) $count = 1;

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

})->name('read log entries paginated')->help = array(
    'since'       => 'v2',
    'description' => 'Read all log entries, paginated for :page, :count entries',
);

/**
 *
 */
$api->post('/log/:id', $APIkeyRequired, $checkLogId, function($id) use ($api) {

    $request = json_decode($api->request->getBody(), TRUE);

    $log = new ORM\Log($id);

    $log->scope = !empty($request['scope']) ? $request['scope'] : 'API r'.$api->version;
    $log->data  = !empty($request['message']) ? trim($request['message']) : '';

    if ($log->replace()) {
        $log = new ORM\Log($id);
        $result = array(
            'status' => 'success',
            'log' => array(array(
                'id'        => +$log->id,
                'timestamp' => strtotime($log->timestamp),
                'datetime'  => $log->timestamp,
                'scope'     => $log->scope,
                'message'   => $log->data
            ))
        );
        $api->render($result);
    } else {
        $api->halt(400);
    }

})->name('post log')->help = array(
    'since'       => 'v2',
    'description' => 'Update a log entry',
    'payload'     => '{"scope":"...", "message":"..."}',
);

/**
 *
 */
$api->delete('/log/:id', $APIkeyRequired, $checkLogId, function($id) use ($api) {
    $log = new ORM\Log($id);
    $api->status($log->delete() ? 204 : 400);
})->name('delete log entry')->help = array(
    'since'       => 'v2',
    'description' => 'Delete a log entry',
);
