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
$api->put('/data/:guid', $APIkeyRequired, function($guid) use ($api) {
    $request = json_decode($api->request->getBody(), TRUE);
    // Check request for 'timestamp' attribute, take as is if numeric,
    // otherwise convert datetime to timestamp
    $timestamp = isset($request['timestamp'])
               ? ( is_numeric($request['timestamp'])
                 ? $request['timestamp']
                 : strtotime($request['timestamp'])
                 )
               : NULL;
    $cnt = Channel::byGUID($guid)->write($request, $timestamp);
    if ($cnt) $api->stopAPI($cnt.' reading(s) added', 201);
})->name('PUT /data/:guid')->help = array(
    'since'       => 'r2',
    'description' => 'Save a reading value',
    'apikey'      => TRUE,
    'payload'     => array('{"data":"<value>"}' => 'JSON encoded value'),
);

/**
 *
 */
$api->post('/data/:guid', $APIkeyRequired, function($guid) use ($api) {
    $request = json_decode($api->request->getBody(), TRUE);
    // Check request for 'timestamp' attribute, take as is if numeric,
    // otherwise convert datetime to timestamp
    $timestamp = isset($request['timestamp'])
               ? ( is_numeric($request['timestamp'])
                 ? $request['timestamp']
                 : strtotime($request['timestamp'])
                 )
               : FALSE;
    if (!$timestamp) $api->stopAPI('Timestamp required for data update', 400);
    Channel::byGUID($guid)->write($request, $timestamp, TRUE);
})->name('POST /data/:guid')->help = array(
    'since'       => 'r4',
    'description' => 'Update a reading value, timestamp is required here',
    'apikey'      => TRUE,
    'payload'     => array('{"data":"<value>","timestamp":"<timestamp>"}' => 'JSON encoded value'),
);

/**
 *
 */
$api->put('/data/raw/:guid', $APIkeyRequired, function($guid) use ($api) {
    // Channel handles raw data
    $cnt = Channel::byGUID($guid)->write($api->request->getBody());
    if ($cnt) $api->stopAPI($cnt.' reading(s) added', 201);
})->name('put raw data')->help = array(
    'since'       => 'r4',
    'description' => 'Save raw data, channel decide what to do with them',
    'apikey'      => TRUE
);

/**
 *
 */
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

        if ($full AND $channel->meter) {
            // Calculate overall consumption and costs
            $cons = 0;
            // Loop all rows to get value from last row if exists
            foreach ($buffer as $row) $cons = $row['data'];
            $attr['consumption'] = round($cons, $attr['decimals']);
            $attr['costs'] = round(
                $cons * $attr['cost'],
                \slimMVC\Config::getInstance()->Core_Currency_Decimals
            );

        }
        $result->write($attr);
    }

    // optimized flow 1st "if" then "loop"...
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
    'since'       => 'r2',
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

/**
 *
 */
$api->delete('/data/:guid/:timestamp', $APIkeyRequired, function($guid, $timestamp) use ($api) {
    $channel = Channel::byGUID($guid);
    $tbl = $channel->numeric ? new \ORM\ReadingNum : new \ORM\ReadingStr;
    if ($tbl->filterByIdTimestamp($channel->entity, $timestamp)->findOne()->getId()) {
        $tbl->delete();
        $api->halt(204);
    } else {
        $api->stopAPI('Reading not found', 400);
    }
})->name('delete data')->help = array(
    'since'       => 'r2',
    'description' => 'Delete a reading value',
    'apikey'      => TRUE,
);
