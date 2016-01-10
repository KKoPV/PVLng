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
$api->put('/batch/:guid', $APIkeyRequired, function($guid) use ($api) {

    // Analyse separator headers
    $sep1 = $api->request->headers->get('X-PVLng-CSV-RecordSeparator', ';');
    if (strtoupper($sep1) == 'TAB') $sep1 = "\t";
    $sep = $api->request->headers->get('X-PVLng-CSV-Separator', ',');
    if (strtoupper($sep) == 'TAB') $sep = "\t";

    saveCSVdata($guid, explode($sep1, trim($api->request->getBody())), $sep);

})->name('put batch data')->help = array(
    'since'       => 'r2',
    'description' => 'Save multiple reading values',
    'apikey'      => TRUE,
    'header'      => array(
        'X-PVLng-CSV-RecordSeparator' => 'Set record separator (if not semicolon) since r5, '
                                       . 'TAB as string will be also accepted',
        'X-PVLng-CSV-Separator'       => 'Set data separator (if not comma) since r5, '
                                       . 'TAB as string will be also accepted'
    ),
    'payload'     => array(
        '<timestamp>,<value>;...'   => 'timestamp and value data sets',
        '<date time>,<value>;...'   => 'date time and value data sets',
        '<date>,<time>,<value>;...' => 'date, time and value data sets',
    ),
);
