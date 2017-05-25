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
$api->put(
    '/csv/:guid',
    $APIkeyRequired,
    function ($guid) use ($api) {
        // Analyse X-PVLng-CSV-Separator header
        $sep = $api->request->headers->get('X-PVLng-CSV-Separator', ';');
        if (strtoupper($sep) == 'TAB') {
            $sep = "\t";
        }

        $api->saveCSV($guid, explode(PHP_EOL, trim($api->request->getBody())), $sep);
    }
)
->name('PUT /csv/:guid')
->help = array(
    'since'       => 'r2',
    'description' => 'Save multiple reading values from CSV',
    'apikey'      => true,
    'header'      => array(
        'X-PVLng-CSV-Separator' => 'Set data separator (if not semicolon) since r5, '
                                 . 'TAB as string will be also accepted for \t'
    ),
    'payload'     => array(
        '<timestamp>;<value>'   => 'timestamp and value data row(s)',
        '<date time>;<value>'   => 'date time and value data row(s)',
        '<date>;<time>;<value>' => 'date, time and value data row(s)',
    ),
);

/**
 *
 */
$api->put(
    '/csvbulk/:guid',
    $APIkeyRequired,
    function ($guid) use ($api) {
        // Analyse X-PVLng-CSV-Separator header
        $sep = $api->request->headers->get('X-PVLng-CSV-Separator', ';');
        if (strtoupper($sep) == 'TAB') {
            $sep = "\t";
        }

        $api->saveBulkCSV($guid, explode(PHP_EOL, trim($api->request->getBody())), $sep);
    }
)
->name('PUT /csvbulk/:guid')
->help = array(
    'since'       => 'r6',
    'description' => 'Save multiple reading values from CSV as bulk',
    'apikey'      => true,
    'header'      => array(
        'X-PVLng-CSV-Separator' => 'Set data separator (if not semicolon), '
                                 . 'TAB as string will be also accepted for \t'
    ),
    'payload'     => array(
        '<timestamp>;<value>'   => 'timestamp and value data row(s)',
        '<date time>;<value>'   => 'date time and value data row(s)',
        '<date>;<time>;<value>' => 'date, time and value data row(s)',
    ),
);
