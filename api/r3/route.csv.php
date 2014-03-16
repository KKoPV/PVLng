<?php
/**
 * Save from CSV files
 *
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 *
 */
$api->put('/csv/:guid', $APIkeyRequired, $accessibleChannel, function($guid) use ($api) {

    $channel = Channel::byGUID($guid);

    // Diasble AutoCommit in case of errors
    $api->db->autocommit(FALSE);
    $send = $saved = 0;

    try {
        foreach (explode(PHP_EOL, $api->request->getBody()) as $dataset) {
            if ($dataset == '') continue;

            $send++;

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
                    $data = $data[2];
                    break;
                default:
                    throw new Exception('Invalid data: '.$dataset, 400);
            } // switch

            if ($timestamp === FALSE) {
                throw new Exception('Invalid timestamp in data: '.$dataset, 400);
            }

            $saved += $channel->write(array('data'=>$data), $timestamp);
        }
        // All fine, commit changes
        $api->db->commit();

        if ($saved) $api->status(201);

        $result = array(
            'status'  => 'succes',
            'message' => $send . ' row(s) sended, ' . $saved . ' row(s) inserted'
        );

        $api->render($result);

    } catch (Exception $e) {
        // Rollback all correct data
        $api->db->rollback();
        $api->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
    }

})->name('put data from file')->help = array(
    'since'       => 'v2',
    'description' => 'Save multiple reading values from CSV file',
    'payload'     => array(
        '<timestamp>;<value>'   => 'Semicolon separated timestamp and value data rows',
        '<date time>;<value>'   => 'Semicolon separated date time and value data rows',
        '<date>;<time>;<value>' => 'Semicolon separated date, time and value data rows',
    ),
);
