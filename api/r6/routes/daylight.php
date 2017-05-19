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
    '/sunrise(/:date)',
    $checkLocation,
    function($date=null) use ($api)
{
    $date = isset($date) ? strtotime($date) : time();

    $lat  = $api->numParam('latitude',  $api->Latitude);
    $lon  = $api->numParam('longitude', $api->Longitude);

    $ts   = date_sunrise($date, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, date('Z', $date)/3600);

    if (!$format = $api->request->get('format')) {
        $format = 'Y-m-d H:i:s';
    }

    $raw = new Buffer;

    $raw->write(array(
        'datetime'    => date('Y-m-d H:i:s', $ts),
        'timestamp'   => $ts,
        'data'        => date($format, $ts),
        'min'         => date($format, $ts),
        'max'         => date($format, $ts),
        'count'       => 1,
        'timediff'    => 0,
        'consumption' => 0
    ));

    $result = new Buffer;

    if ($api->boolParam('attributes', false)) {
        $result->write(array(
            'name' => 'Sunrise'
        ));
    }

    $api->render($api->formatResult($raw, $result, false, false, 0));
})->name('GET /sunrise(/:date)')->help = array(
    'since'       => 'r3',
    'description' => 'Get sunrise of day, using configured loaction'
);

/**
 *
 */
$api->get(
    '/sunset(/:date)',
    $checkLocation,
    function($date=null) use ($api)
{
    $date = isset($date) ? strtotime($date) : time();

    $lat  = $api->numParam('latitude',  $api->Latitude);
    $lon  = $api->numParam('longitude', $api->Longitude);

    $ts   = date_sunset($date, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, date('Z', $date)/3600);

    if (!$format = $api->request->get('format')) {
        $format = 'Y-m-d H:i:s';
    }

    $raw = new Buffer;

    $raw->write(array(
        'datetime'    => date('Y-m-d H:i:s', $ts),
        'timestamp'   => $ts,
        'data'        => date($format, $ts),
        'min'         => date($format, $ts),
        'max'         => date($format, $ts),
        'count'       => 1,
        'timediff'    => 0,
        'consumption' => 0
    ));

    $result = new Buffer;

    if ($api->boolParam('attributes', false)) {
        $result->write(array(
            'name' => 'Sunset'
        ));
    }

    $api->render($api->formatResult($raw, $result, false, false, 0));
})->name('GET /sunset(/:date)')->help = array(
    'since'       => 'r3',
    'description' => 'Get sunset of day, using configured loaction'
);

/**
 *
 */
$api->get(
    '/daylight(/:offset)',
    $checkLocation,
    function($offset=0) use ($api)
{
    $offset *= 60; // Minutes to seconds
    $now     = time();
    $sunrise = date_sunrise($now, SUNFUNCS_RET_TIMESTAMP, $api->Latitude, $api->Longitude, 90, date('Z')/3600);
    $sunset  = date_sunset($now, SUNFUNCS_RET_TIMESTAMP, $api->Latitude, $api->Longitude, 90, date('Z')/3600);
    $api->render(array(
        'daylight' => (int) ($sunrise-$offset <= $now AND $now <= $sunset+$offset)
    ));
})->name('GET /daylight(/:offset)')->help = array(
    'since'       => 'r3',
    'description' => 'Check for daylight for configured location, accept additional minutes before/after',
);

/**
 *
 */
$api->get(
    '/daylight/:latitude/:longitude(/:offset)',
    function($latitude, $longitude, $offset=0) use ($api)
{
    $offset *= 60; // Minutes to seconds
    $now     = time();
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600);
    $sunset  = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90, date('Z')/3600);
    $api->render(array(
        'daylight' => (int) ($sunrise-$offset <= $now AND $now <= $sunset+$offset)
    ));
})->name('GET /daylight/:latitude/:longitude(/:offset)')->help = array(
    'since'       => 'r3',
    'description' => 'Check for daylight, accept additional minutes before/after',
);
