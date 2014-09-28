<?php
/**
 * Save from CSV files
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
$api->put('/csv/:guid', $APIkeyRequired, function($guid) use ($api) {

    // Analyse X-PVLng-CSV-Separator header
    $sep = $api->request->headers->get('X-PVLng-CSV-Separator', ';');
    if (strtoupper($sep) == 'TAB') $sep = "\t";

    saveCSVdata($guid, explode(PHP_EOL, trim($api->request->getBody())), $sep);

})->name('put data from file')->help = array(
    'since'       => 'r2',
    'description' => 'Save multiple reading values from CSV',
    'apikey'      => TRUE,
    'header'      => array(
        'X-PVLng-CSV-Separator' => 'Set data separator (if not semicolon) since r5, '
                                 . '"TAB" as string will be also accepted'
    ),
    'payload'     => array(
        '<timestamp>;<value>'   => 'timestamp and value data row(s)',
        '<date time>;<value>'   => 'date time and value data row(s)',
        '<date>;<time>;<value>' => 'date, time and value data row(s)',
    ),
);
