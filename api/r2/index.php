<?php
/**
 * Main API file
 *
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2013 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

setlocale(LC_NUMERIC, 'C');

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
slimMVC\MySQLi::setHost($config->get('Database.Host'));
slimMVC\MySQLi::setPort($config->get('Database.Port'));
slimMVC\MySQLi::setSocket($config->get('Database.Socket'));
slimMVC\MySQLi::setUser($config->get('Database.Username'));
slimMVC\MySQLi::setPassword($config->get('Database.Password'));
slimMVC\MySQLi::setDatabase($config->get('Database.Database'));
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

slimMVC\ORM::setDatabase($api->db);
slimMVC\ORM::setCache($api->cache);

foreach ((new ORM\SettingsKeys)->find()->asAssoc() as $setting) {
    $config->set($setting['key'], $setting['value']);
}

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
        $content[$pattern] = $route->help;
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
})->name('single channel attribute')->help = array(
    'since'       => 'v2',
    'description' => 'Fetch single channel attribute',
);

$api->get('/attributes/:guid(/:attribute)', $accessibleChannel, function($guid, $attribute='') use ($api) {
    $api->render(Channel::byGUID($guid)->getAttributes($attribute));
})->name('all or single channel attribute')->help = array(
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
    'payload'     => '{"data":"<value>"}',
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
                'relative from now, see http://php.net/manual/en/datetime.formats.relative.php'
            ),
        ),
        'end' => array(
            'description' => 'End timestamp for readout, default today midnight',
            'value'       => array(
                'YYYY-mm-dd HH:ii:ss',
                'seconds since 1970',
                'relative from now, see http://php.net/manual/en/datetime.formats.relative.php'
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

/**
 *
 */
$custom_routes = 'custom' . DS . 'routes.php';
if (file_exists($custom_routes)) include $custom_routes;

/**
 * Let's go
 */
$api->run();
