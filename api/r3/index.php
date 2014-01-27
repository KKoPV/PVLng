<?php
/**
 * Main API file
 *
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2013 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 * Directories
 */
define('DS',       DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));
define('CORE_DIR', ROOT_DIR . DS . 'core');
define('LIB_DIR',  ROOT_DIR . DS . 'lib');
define('TEMP_DIR', ROOT_DIR . DS . 'tmp');
define('LANGUAGE', 'en');

/**
 * Initialize Loader
 */
include LIB_DIR . DS . 'Loader.php';

Loader::register(
    array(
        'path'    => array(LIB_DIR, CORE_DIR),
        'pattern' => array('%s.php'),
        'exclude' => array('contrib/')
    ),
    TEMP_DIR
);

class API extends Slim\Slim {

    /**
     *
     */
    public function strParam( $name, $default ) {
        $value = trim($this->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     *
     */
    public function intParam( $name, $default ) {
        $value = trim($this->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     *
     */
    public function boolParam( $name, $default ) {
        $value = strtolower(trim($this->request->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }

    /**
     *
     */
    public function stopAPI( $message, $code=400 ) {
        $this->status($code);
        $this->response()->header('X-Status-Reason', $message);
        $this->render(array( 'status'=>$code<400?'success':'error', 'message'=>$message ));
        $this->stop();
    }
}

$config = slimMVC\Config::getInstance()
        ->load(ROOT_DIR . DS . 'config' . DS . 'config.app.php')
        ->load(ROOT_DIR . DS . 'config' . DS . 'config.php')
        ->load('config.php', false);

if ($config->get('develop')) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

/**
 * Setup database connection
 */
slimMVC\MySQLi::setUser($config->get('Database.Username'));
slimMVC\MySQLi::setPassword($config->get('Database.Password'));
slimMVC\MySQLi::setDatabase($config->get('Database.Database'));
slimMVC\MySQLi::setHost($config->get('Database.Host'));
slimMVC\MySQLi::$SETTINGS_TABLE = 'pvlng_config';

require 'View.php';

$api = new API(array(
    'mode'      => 'production',
    'log.level' => Slim\Log::ALERT,
    'debug'     => FALSE,
    'view'      => new View
));

if ($config->get('develop')) {
    $api->config('mode', 'development');
    $api->config('log.level', Slim\Log::INFO);
}

$api->db = slimMVC\MySQLi::getInstance();

$api->cache = Cache::factory(
    array(
        'Token'     => 'PVLng',
        'Directory' => TEMP_DIR,
    ),
    $config->get('Cache')
);

/**
 * Nested set for channel tree
 */
include_once LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

NestedSet::Init(array (
    'db'    => $api->db,
    'debug' => true,
    'lang'  => 'en',
    'path'  => LIB_DIR . DS . 'contrib' . DS . 'messages',
    'db_table' => array (
        'tbl'  => 'pvlng_tree',
        'nid'  => 'id',
        'l'    => 'lft',
        'r'    => 'rgt',
        'mov'  => 'moved',
        'pay'  => 'entity'
    )
));

$api->error(function($e) use ($api) {
    $api->stopAPI($e->getMessage(), $e->getCode());
});

/**
 * Some defines
 */
$version = file(ROOT_DIR . DS . '.version', FILE_IGNORE_NEW_LINES);
define('PVLNG',              'PhotoVoltaic Logger new generation');
define('PVLNG_VERSION',      $version[0]);
define('PVLNG_VERSION_DATE', $version[1]);

$api->response->headers->set('X-Version', PVLNG_VERSION);
$api->response->headers->set('X-API-Version', 'r2');

/**
 * Detect requested content type by file extension, correct PATH_INFO value
 * without extension and set Response conntent header
 *
 * Analyse X-PVLng-Key header
 */
$api->hook('slim.before', function() use ($api) {
    $PathInfo = $api->environment['PATH_INFO'];
    if ($dot = strrpos($PathInfo, '.')) {
        // File extension
        $ext = substr($PathInfo, $dot+1);
        // Correct PATH_INFO, remove extension
        $api->environment['PATH_INFO'] = substr($PathInfo, 0, $dot);
        // All supported content types
        switch ($ext) {
            case 'csv':   $type = 'application/csv';   break;
            case 'tsv':   $type = 'application/tsv';   break;
            case 'txt':   $type = 'text/plain';        break;
            case 'xml':   $type = 'application/xml';   break;
            case 'json':  $type = 'application/json';  break;
            default:
                $api->contentType('text/plain');
                $api->halt(400, 'Unknown Accept content type: '.$ext);
        }
    } else {
        // Defaults to JSON
        $type = 'application/json';
    }
    // Set the response header, used also by View to build proper response body
    $api->contentType($type);

    // Analyse X-PVLng-Key header
    $APIKey = $api->request->headers->get('X-PVLng-Key');

    if ($APIKey == '') {
        // Not given
        $api->APIKeyValid = 0;
    } elseif ($APIKey == $api->db->queryOne('SELECT getAPIKey()')) {
        // Key is valid
        $api->APIKeyValid = 1;
    } else {
        // Key is invalid
        $api->stopAPI('Invalid API key given.', 403);
    }
});

/**
 *
 */
$APIkeyRequired = function() use ($api) {
    $api->APIKeyValid || $api->stopAPI('Access only with valid API key!', 403);
};

/**
 *
 */
$accessibleChannel = function(Slim\Route $route) use ($api) {
    if ($api->APIKeyValid == 0) {
        // No API key given, check channel is public
        $channel = Channel::byGUID($route->getParam('guid'));
        if (!$channel->public) {
            $api->stopAPI('Access to private channel \''.$channel->name.'\' only with valid API key!', 403);
        }
    }
};

/**
 *
 */
$checkLocation = function() use ($api) {
    $api->Latitude  = $api->config->get('Location.Latitude');
    $api->Longitude = $api->config->get('Location.Longitude');

    if ($api->Latitude == '' OR $api->Longitude == '') {
        $api->stopAPI('No valid location defined in configuration', 404);
    }
};

/**
 *
 */
Slim\Route::setDefaultConditions(array(
    'guid' => '(\w{4}-){7}\w{4}',
    'id'   => '\d+',
));

// ---------------------------------------------------------------------------
// The routes
// ---------------------------------------------------------------------------
// Help
// ---------------------------------------------------------------------------
$api->notFound(function() use ($api) {
    // Catch also /
    $api->redirect($api->request()->getRootUri() . '/help');
});

$api->any('/help', function() use ($api) {
    $content = array();

    foreach ($api->router()->getNamedRoutes() as $route) {
        $name    = $route->getName();
        $pattern = implode('|', $route->getHttpMethods()) . ' '
                 . $api->request()->getRootUri() . $route->getPattern();
        $help = $route->help;
        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }
        $content[$pattern] = $help;
    }

    $api->response->headers->set('Content-Type', 'application/json');
    $api->render($content);
})->name('help')->help = array(
    'since'       => 'v1',
    'description' => 'This help, overview of valid calls',
);

// ---------------------------------------------------------------------------
// Attributes
// ---------------------------------------------------------------------------
$api->get('/:guid', $accessibleChannel, function($guid) use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes());
})->name('channel attributes')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch attributes',
);

$api->get('/:guid/:attribute', $accessibleChannel, function($guid, $attribute='') use ($api) {
    $api->render(Channel::byGUID($guid)->$attribute);
})->conditions(array(
    'attribute' => '\w+'
))->name('single channel attribute')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch single channel attribute',
);

$api->get('/attributes/:guid(/:attribute)', $accessibleChannel, function($guid, $attribute='') use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes($attribute));
})->conditions(array(
    'attribute' => '\w+'
))->name('all or single channel attribute')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch all channel attributes or specific channel attribute',
);

// ---------------------------------------------------------------------------
// Data
// ---------------------------------------------------------------------------
$api->put('/data/:guid', $APIkeyRequired, $accessibleChannel, function($guid) use ($api) {
    $request = json_decode($api->request->getBody(), TRUE);
    // Check request for 'timestamp' attribute, take as is if numeric,
    // otherwise try to convert datetime to timestamp
    $timestamp = isset($request['timestamp'])
               ? ( is_numeric($request['timestamp'])
                 ? $request['timestamp']
                 : strtotime($request['timestamp'])
                 )
               : NULL;
    Channel::byGUID($guid)->write($request, $timestamp) && $api->halt(201);
})->name('put data')->help = array(
    'since'       => 'v2',
    'description' => 'Save a reading value',
    'payload'     => '{"<data>":"<value>"}',
);

/**
 * Act as vzlogger middleware?
 */
if ($config->get('vzlogger.enabled')) {

    /**
     * vzlogger compatible data saving
     *
     * Original:
     *   POST http://demo.volkszaehler.org/middleware.php/data/550e8400-e29b-11d4-a716-446655441352.json?ts=1284677961150&value=12
     * Here:
     *   POST http://your.domain.here/api/r2/vz/d8e3-1dd6-a75b-a6b4-1394-f45d-2ee2-66c9.json?ts=1284677961150&value=12
     */
    $app->post('/vz/:guid', function($guid) use ($app) {
        Channel::byGUID($guid)->write(
            array( 'data' => $app->request->post('value') ),
            $app->request->post('ts')
        ) && $app->halt(201);
    })->name('post data from vzlogger')->help = array(
        'since'       => 'v3',
        'description' => 'Save a reading value from vzlogger (http://wiki.volkszaehler.org/software/controller/vzlogger)',
        'payload'     => '?ts=<timestamp>&value=<value>'
    );

}

$api->get('/data/:guid(/:p1(/:p2))', $accessibleChannel, function($guid, $p1='', $p2='') use ($api) {

    $request = $api->request->get();
    $request['p1'] = $p1;
    $request['p2'] = $p2;

    $channel = Channel::byGUID($guid);

    // Special models can provide an own GET functionality
    // e.g. for special return formats like PVLog or Sonnenertrag
    if (method_exists($channel, 'GET')) {
        $api->render($channel->GET($request));
        return;
    }

    $buffer = $channel->read($request);
    $result = new Buffer;

    $full  = $api->boolParam('full', FALSE);
    $short = $api->boolParam('short', FALSE);

    if ($api->boolParam('attributes', FALSE)) {
        $attr = $channel->getAttributes();

        if ($full) {
            // Calculate overall consumption and costs
            foreach($buffer as $row) {
                $attr['consumption'] += $row['consumption'];
            }
            $attr['consumption'] = round($attr['consumption'], $attr['decimals']);
            $attr['costs'] = round(
                $attr['consumption'] * $attr['cost'],
                \slimMVC\Config::getInstance()->Currency_Decimals
            );

        }
        $result->write($attr);
    }

    // optimized flow...
    if ($full and $short) {
        // passthrough all values as numeric based array
        foreach ($buffer as $row) {
            $result->write(array_values($row));
        }
    } elseif ($full) {
        // do nothing, use as is
        $result->append($buffer);
    } elseif ($short) {
        // default mobile result: only timestamp and data
        foreach ($buffer as $row) {
            $result->write(array(
                /* 0 */ $row['timestamp'],
                /* 1 */ $row['data']
            ));
        }
    } else {
        // default result: only timestamp and data
        foreach ($buffer as $row) {
            $result->write(array(
                'timestamp' => $row['timestamp'],
                'data'      => $row['data']
            ));
        }
    }

    $api->response->headers->set('X-Data-Rows', count($result));
    $api->response->headers->set('X-Data-Size', $result->size() . ' Bytes');

    $api->render($result);

})->name('get data')->help = array(
    'since'       => 'v2',
    'description' => 'Read reading values',
    'parameters'  => array(
        'start' => array(
            'description' => 'Start timestamp for readout, default today 00:00',
            'value'       => array(
                'YYYY-mm-dd HH:ii:ss',
                'seconds since 1970',
                'relative from now, see http://php.net/manual/en/datetime.formats.relative.php',
                'sunrise - needs location in config/config.php'
            ),
        ),
        'end' => array(
            'description' => 'End timestamp for readout, default today midnight',
            'value'       => array(
                'YYYY-mm-dd HH:ii:ss',
                'seconds since 1970',
                'relative from now, see http://php.net/manual/en/datetime.formats.relative.php',
                'sunset - needs location in config/config.php'
            ),
        ),
        'period' => array(
            'description' => 'Aggregation period, default none',
            'value'       => array( '[0-9.]+minutes', '[0-9.]+hours',
                                    '[0-9.]+days',  '[0-9.]+weeks',
                                    '[0-9.]+month', '[0-9.]+quarters',
                                    '[0-9.]+years', 'last', 'readlast', 'all' ),
        ),
        'attributes' => array(
            'description' => 'Return channel attributes as 1st line',
            'value'       => array( 1, 'true' ),
        ),
        'full' => array(
            'description' => 'Return all data, not only timestamp and value',
            'value'       => array( 1, 'true' ),
        ),
        'short' => array(
            'description' => 'Return data as array, not object',
            'value'       => array( 1, 'true' ),
        ),
    ),
);

$api->delete('/data/:guid/:timestamp', $APIkeyRequired, $accessibleChannel, function($guid, $timestamp) use ($api) {
    $channel = Channel::byGUID($guid);
    $tbl = $channel->numeric ? new \ORM\ReadingNum : new \ORM\ReadingStr;
    if ($tbl->findPrimary(array($channel->entity, $timestamp))->id) {
        $tbl->delete();
        $api->halt(204);
    } else {
        $api->stopAPI('Reading not found', 400);
    }
})->name('delete data')->help = array(
    'since'       => 'v2',
    'description' => 'Delete a reading value'
);

// ---------------------------------------------------------------------------
// JSON xPath parser
// ---------------------------------------------------------------------------
function JSONxPath( $api, $path, $json ) {

    $json = json_decode($json, TRUE);

    if (($err = json_last_error()) != JSON_ERROR_NONE) {
        $api->stopAPI(JSON::check($err), 422);
    }

    // Root pointer
    $p = &$json;

    foreach ($path as $key) {
        if (is_array($p) AND isset($p[$key])) {
            // Move pointer foreward ...
            $p = &$p[$key];
        } else {
            // ... until key not more found
            $api->halt(404);
        }
    }

    // Key found, return its value
    $api->render($p);
};

$api->get('/json/:path+', function($path) use ($api) {
    JSONxPath($api, $path, $api->request->get('json'));
})->name('json extract via get')->help = array(
    'description' => 'Extract a section/value from given JSON data from query string',
    'payload'     => '...json/path/to/node/?json=<JSON data>'
);

$api->post('/json/:path+', function($path) use ($api) {
    JSONxPath($api, $path, $api->request->getBody());
})->name('json extract via post')->help = array(
    'description' => 'Extract a section/value from given JSON data sended in request body e.g. from a file',
);

// ---------------------------------------------------------------------------
// File
// ---------------------------------------------------------------------------
$api->put('/csv/:guid', $APIkeyRequired, $accessibleChannel, function($guid) use ($api) {

    $channel = Channel::byGUID($guid);

    // Diasble AutoCommit in case of errors
    $api->db->autocommit(FALSE);
    $count = 0;

    try {
        foreach (explode(PHP_EOL, $api->request->getBody()) as $send=>$dataset) {
            if ($dataset == '') continue;

            $data = explode(';', $dataset);
            switch (count($data)) {
                case 2:
                    // timestamp/datetime and data
                    $timestamp = $data[0];
                    if (!is_numeric($timestamp)) {
                        $timestamp = strtotime($timestamp);
                    }
                    $data = $data[1];
                    break;
                case 3:
                    // date, time and data
                    $timestamp = strtotime($data[0] . ' ' . $data[1]);
                    if ($timestamp === FALSE) {
                        throw new Exception('Invalid timestamp in data: '.$dataset, 400);
                    }
                    $data = $data[2];
                    break;
                default:
                    throw new Exception('Invalid batch data: '.$dataset, 400);
            } // switch

            $count += $channel->write(array('data'=>$data), $timestamp);
        }
        // All fine, commit changes
        $api->db->commit();

        if ($count) $api->status(201);

        $result = array(
            'status'  => 'succes',
            'message' => ($send+1) . ' row(s) sended, ' . $count . ' row(s) inserted'
        );

        $api->render($result);

    } catch (Exception $e) {
        // Rollback all correct data
        $api->db->rollback();
        $api->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
    }

})->name('put data from file')->help = array(
    'since'       => 'v2',
    'description' => 'Save multiple reading values',
    'payload'     => array(
        '<timestamp>;<value>'   => 'Semicolon separated timestamp and value data rows',
        '<date time>;<value>'   => 'Semicolon separated date time and value data rows',
        '<date>;<time>;<value>' => 'Semicolon separated date, time and value data rows',
    ),
);

// ---------------------------------------------------------------------------
// Batch
// ---------------------------------------------------------------------------
$api->put('/batch/:guid', $APIkeyRequired, $accessibleChannel, function($guid) use ($api) {

    $channel = Channel::byGUID($guid);

    // Diasble AutoCommit in case of errors
    $api->db->autocommit(FALSE);
    $count = 0;

    try {
        foreach (explode(';', $api->request->getBody()) as $dataset) {
            if ($dataset == '') continue;

            $data = explode(',', $dataset);
            switch (count($data)) {
                case 2:
                    // timestamp and data
                    $timestamp = $data[0];
                    if (!is_numeric($timestamp)) {
                        $timestamp = strtotime($timestamp);
                    }
                    $data = $data[1];
                    break;
                case 3:
                    // date, time and data
                    $timestamp = strtotime($data[0] . ' ' . $data[1]);
                    if ($timestamp === FALSE) {
                        throw new Exception('Invalid timestamp in data: '.$dataset, 400);
                    }
                    $data = $data[2];
                    break;
                default:
                    throw new Exception('Invalid batch data: '.$dataset, 400);
            } // switch

            $count += $channel->write(array('data'=>$data), $timestamp);
        }
        // All fine, commit changes
        $api->db->commit();

        if ($count) $api->status(201);

        $result = array(
            'status'  => 'succes',
            'message' => $count . ' row(s) inserted'
        );

        $api->render($result);

    } catch (Exception $e) {
        // Rollback all correct data
        $api->db->rollback();
        $api->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
    }

})->name('put batch data')->help = array(
    'since'       => 'v2',
    'description' => 'Save multiple reading values',
    'payload'     => array(
        '<timestamp>,<value>;...'   => 'Semicolon separated timestamp and value data sets',
        '<date time>,<value>;...'   => 'Semicolon separated date time and value data sets',
        '<date>,<time>,<value>;...' => 'Semicolon separated date, time and value data sets',
    ),
);

// ---------------------------------------------------------------------------
// Log
// ---------------------------------------------------------------------------
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

    $log->scope = !empty($request['scope']) ? $request['scope'] : 'API r2';
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
    'description' => 'Store new log entry, scope defaults to \'API r2\'',
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

$api->post('/log/:id', $APIkeyRequired, $checkLogId, function($id) use ($api) {

    $request = json_decode($api->request->getBody(), TRUE);

    $log = new ORM\Log($id);

    $log->scope = !empty($request['scope']) ? $request['scope'] : 'API r2';
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

$api->delete('/log/:id', $APIkeyRequired, $checkLogId, function($id) use ($api) {
    $log = new ORM\Log($id);
    $api->status($log->delete() ? 204 : 400);
})->name('delete log entry')->help = array(
    'since'       => 'v2',
    'description' => 'Delete a log entry',
);

// ---------------------------------------------------------------------------
// Status
// ---------------------------------------------------------------------------
$api->get('/status', function() use ($api) {

    $result = array(
        'version' => exec('cat /proc/version')
    );

    // http://www.linuxinsight.com/proc_uptime.html
    // This file contains the length of time since the system was booted,
    // as well as the amount of time since then that the system has been idle.
    // Both are given as floating-point values, in seconds.
    $res = explode(' ', exec('cat /proc/uptime'));
      if (!empty($res)) {
            $result['uptime'] = array(
            'overall'         => +$res[0],
            'overall_minutes' => $res[0]/60,
            'overall_hours'   => $res[0]/3600,
            'overall_days'    => $res[0]/86400,
            'idle'            => +$res[1],
            'idle_minutes'    => +$res[1]/60,
            'idle_hours'      => +$res[1]/3600,
            'idle_days'       => +$res[1]/86400
        );
    }

    //              total       used       free     shared    buffers     cached
    // Mem:          1771       1714         57          0        215       1178
    // Swap:         1905          6       1898
    // Total:        3676       1721       1955
    exec('free -mto', $res);

    if (preg_match_all('~^(\w+): +(\S+) +(\S+) +(\S+)~m',
                       implode("\n", $res), $args, PREG_SET_ORDER)) {
          foreach ($args as $arg) {
              $result['memory'][$arg[1]] = array(
                'total_mb' => +$arg[2],
                'total_gb' => $arg[2]/1024,
                'used_mb'  => +$arg[3],
                'used_gb'  => $arg[3]/1024,
                'free_mb'  => +$arg[4],
                'free_gb'  => $arg[4]/1024
            );
          }
    }

    // http://juliano.info/en/Blog:Memory_Leak/Understanding_the_Linux_load_average
    // These values represent the average system load in the last 1, 5 and 15 minutes,
    // the number of active and total scheduling entities (tasks) and
    // the PID of the last created process in the system.
      $res = exec('cat /proc/loadavg');
      if (preg_match('~([0-9.]+) ([0-9.]+) ([0-9.]+) (\d+)/(\d+)~', $res, $args)) {
            $result['load'] = array(
            'miutes_1'  => +$args[1],
            'miutes_5'  => +$args[2],
            'miutes_15' => +$args[3],
            'active'    => +$args[4],
            'total'     => +$args[5]
        );
    }

    exec('cat /proc/cpuinfo', $res);
    if (preg_match_all('~^([^\t]+)\s*:\s*(.+)$~m',
                       implode("\n", $res), $args, PREG_SET_ORDER)) {
          foreach ($args as $arg) {
              $result['cpuinfo'][str_replace(' ', '_', $arg[1])] =
                (string) +$arg[2] == $arg[2] ? +$arg[2] : $arg[2];
          }
    }

    $api->response->headers->set('Content-Type', 'application/json');

    $api->render($result);

})->name('system status')->help = array(
    'since'       => 'v2',
    'description' => 'System status',
);

// ---------------------------------------------------------------------------
// sunrise, sunset and daylight routes
// ---------------------------------------------------------------------------

$api->get('/sunrise/(/:date)', $checkLocation, function($date=NULL) use ($api) {
    $date = isset($date) ? strtotime($date) : time();
    $api->render(array(
        'sunrise' =>
        date_sunrise($date, SUNFUNCS_RET_TIMESTAMP, $api->Latitude, $api->Longitude, 90, date('Z')/3600)
    ));
})->conditions(array(
    'date' => '\d{4}-\d{2}-\d{2}'
))->name('sunrise intern')->help = array(
    'since'       => 'v3',
    'description' => 'Get sunrise of day, using configured loaction'
);

$api->get('/sunrise/:latitude/:longitude(/:date)', function($latitude, $longitude, $date=NULL) use ($api) {
    $date = isset($date) ? strtotime($date) : time();
    $api->render(array(
        'sunrise' =>
        date_sunrise($date, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600)
    ));
})->conditions(array(
    'latitude'  => '[\d.-]+',
    'longitude' => '[\d.-]+',
    'date'      => '\d{4}-\d{2}-\d{2}'
))->name('sunrise')->help = array(
    'since'       => 'v3',
    'description' => 'Get sunrise for location and day'
);

$api->get('/sunset/(/:date)', $checkLocation, function($date=NULL) use ($api) {
    $date = isset($date) ? strtotime($date) : time();
    $api->render(array(
        'sunrise' =>
        date_sunset($date, SUNFUNCS_RET_TIMESTAMP, $api->Latitude, $api->Longitude, 90, date('Z')/3600)
    ));
})->conditions(array(
    'date' => '\d{4}-\d{2}-\d{2}'
))->name('sunset intern')->help = array(
    'since'       => 'v3',
    'description' => 'Get sunset of day, using configured loaction'
);

$api->get('/sunset/:latitude/:longitude(/:date)', function($latitude, $longitude, $date=NULL) use ($api) {
    $date = isset($date) ? strtotime($date) : time();
    $api->render(array(
        'sunset' =>
        date_sunset($date, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600)
    ));
})->conditions(array(
    'latitude'  => '[\d.-]+',
    'longitude' => '[\d.-]+',
    'date'      => '\d{4}-\d{2}-\d{2}'
))->name('sunset')->help = array(
    'since'       => 'v3',
    'description' => 'Get sunset of day'
);

$api->get('/daylight/(/:offset)', $checkLocation, function($offset=0) use ($api) {
    $offset *= 60; // Minutes to seconds
    $now     = time();
    $sunrise = date_sunrise($now, SUNFUNCS_RET_TIMESTAMP, $api->Latitude, $api->Longitude, 90, date('Z')/3600);
    $sunset  = date_sunset($now, SUNFUNCS_RET_TIMESTAMP, $api->Latitude, $api->Longitude, 90, date('Z')/3600);
    $api->render(array(
        'daylight' => (int) ($sunrise-$offset <= $now AND $now <= $sunset+$offset)
    ));
})->conditions(array(
    'offset' => '\d+'
))->name('daylight intern')->help = array(
    'since'       => 'v3',
    'description' => 'Check for daylight for configured location, accept additional minutes before/after',
);

$api->get('/daylight/:latitude/:longitude(/:offset)', function($latitude, $longitude, $offset=0) use ($api) {
    $offset *= 60; // Minutes to seconds
    $now     = time();
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600);
    $sunset  = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600);
    $api->render(array(
        'daylight' => (int) ($sunrise-$offset <= $now AND $now <= $sunset+$offset)
    ));
})->conditions(array(
    'latitude'  => '[\d.-]+',
    'longitude' => '[\d.-]+',
    'offset'    => '\d+',
))->name('daylight')->help = array(
    'since'       => 'v3',
    'description' => 'Check for daylight, accept additional minutes before/after',
);

// ---------------------------------------------------------------------------
// Other
// ---------------------------------------------------------------------------
$api->map('/hash', function() use ($api) {

    $text = $api->request->params('text');

    $slug = strtr($text, array(
        'Š' => 'S',  'š' => 's',  'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z',
        'ž' => 'z',  'Č' => 'C',  'č' => 'c',  'Ć' => 'C',  'ć' => 'c',
        'À' => 'A',  'Á' => 'A',  'Â' => 'A',  'Ã' => 'A',  'Ä' => 'Ae',
        'Å' => 'A',  'Æ' => 'A',  'Ç' => 'C',  'È' => 'E',  'É' => 'E',
        'Ê' => 'E',  'Ë' => 'E',  'Ì' => 'I',  'Í' => 'I',  'Î' => 'I',
        'Ï' => 'I',  'Ñ' => 'N',  'Ò' => 'O',  'Ó' => 'O',  'Ô' => 'O',
        'Õ' => 'O',  'Ö' => 'Oe', 'Ø' => 'O',  'Ù' => 'U',  'Ú' => 'U',
        'Û' => 'U',  'Ü' => 'Ue', 'Ý' => 'Y',  'Þ' => 'B',  'ß' => 'Ss',
        'à' => 'a',  'á' => 'a',  'â' => 'a',  'ã' => 'a',  'ä' => 'ae',
        'å' => 'a',  'æ' => 'a',  'ç' => 'c',  'è' => 'e',  'é' => 'e',
        'ê' => 'e',  'ë' => 'e',  'ì' => 'i',  'í' => 'i',  'î' => 'i',
        'ï' => 'i',  'ð' => 'o',  'ñ' => 'n',  'ò' => 'o',  'ó' => 'o',
        'ô' => 'o',  'õ' => 'o',  'ö' => 'oe', 'ø' => 'o',  'ù' => 'u',
        'ú' => 'u',  'û' => 'u',  'ü' => 'ue', 'ý' => 'y',  'ý' => 'y',
        'þ' => 'b',  'ÿ' => 'y',  'Ŕ' => 'R',  'ŕ' => 'r'
    ));
    // Remove multiple spaces
    $slug = preg_replace(array('~\s{2,}~', '~[\t\r\n]+~'), ' ', $slug);
    $slug = preg_replace('~[^\w\d]~', '-', $slug);
    $slug = preg_replace('~-{2,}~', '-', $slug);
    $slug = strtolower(trim($slug, '-'));

    $api->render(array(
        'md5'  => md5($text),
        'sha1' => sha1($text),
        'slug' => $slug
    ));
})->via('GET', 'POST')->name('hash')->help = array(
    'since'       => 'v3',
    'description' => 'Create MD5 and SHA1 hashes and a slug for the given text',
    'parameters'  => array(
        'text' => array(
            'description' => 'Text to make hashes for'
        )
    )
);

// ---------------------------------------------------------------------------

/**
 *
 */
if (file_exists('custom.php')) include 'custom.php';

/**
 * Let's go
 */
$api->run();
