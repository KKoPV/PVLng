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
    'since'       => 'r2',
    'description' => 'Save multiple reading values',
    'apikey'      => TRUE,
    'payload'     => array(
        '<timestamp>,<value>;...'   => 'Semicolon separated timestamp and value data sets',
        '<date time>,<value>;...'   => 'Semicolon separated date time and value data sets',
        '<date>,<time>,<value>;...' => 'Semicolon separated date, time and value data sets',
    ),
);
