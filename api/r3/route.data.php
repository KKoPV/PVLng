<?php
/**
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 *
 */
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

/**
 *
 */
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
