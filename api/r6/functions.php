<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
function saveBulkCSV($guid, $rows, $sep) {

    // Ignore empty datasets
    $rows = array_values(array_filter($rows));

    if (empty($rows)) return;

    try {
        $api = API::getInstance();

        $channel  = Channel::byGUID($guid);
        $bulkdata = array();

        // Ignore empty datasets, track also row Id for error messages
        foreach ($rows as $row=>$dataset) {
            $data = explode($sep, $dataset);

            switch (count($data)) {
                case 2:
                    // timestamp/datetime and data
                    list($timestamp, $value) = $data;
                    break;
                case 3:
                    // date, time and data
                    $timestamp = $data[0] . ' ' . $data[1];
                    $value     = $data[2];
                    break;
                default:
                    throw new Exception('Invalid data: '.$dataset, 400);
            } // switch

            if (!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

            if ($timestamp === FALSE) {
                throw new Exception('Invalid timestamp in row '.($row+1).': "'.$dataset.'"', 400);
            }

            if ($api->dryrun) {
                echo $timestamp, $sep, $value,
                     ' (', date('Y-m-d H:i:s', $timestamp), ' : ', $value, ')', PHP_EOL;
            } else {
                $bulkdata[$timestamp] = $value;
            }
        }

        // All fine, insert data
        $ORMReadingNum = new ORM\ReadingNum;
        $saved = $ORMReadingNum->insertBulk($channel->id, $bulkdata);

        if ($saved) $api->status(201);

        $result = array(
            'status'  => 'succes',
            'message' => ($row+1) . ' valid row(s) sended, ' . $saved . ' row(s) inserted'
        );

        $api->render($result);

    } catch (Exception $e) {
        $api->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
    }
};

/**
 *
 */
function SaveCSV( $guid, $rows, $sep ) {

    // Ignore empty datasets
    $rows = array_values(array_filter($rows));

    if (empty($rows)) return;

    try {
        $api = API::getInstance();

        // Disable AutoCommit in case of errors
        $api->db->autocommit(FALSE);
        $saved = 0;

        $channel = Channel::byGUID($guid);

        // Ignore empty datasets, track also row Id for error messages
        foreach ($rows as $row=>$dataset) {
            $data = explode($sep, $dataset);

            switch (count($data)) {
                case 2:
                    // timestamp/datetime and data
                    list($timestamp, $value) = $data;
                    break;
                case 3:
                    // date, time and data
                    $timestamp = $data[0] . ' ' . $data[1];
                    $value     = $data[2];
                    break;
                default:
                    throw new Exception('Invalid data: '.$dataset, 400);
            } // switch

            if (!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

            if ($timestamp === FALSE) {
                throw new Exception('Invalid timestamp in row '.($row+1).': "'.$dataset.'"', 400);
            }

            if ($api->dryrun) {
                echo $timestamp, $sep, $value,
                     ' (', date('Y-m-d H:i:s', $timestamp), ' : ', $value, ')', PHP_EOL;
            } else {
                $saved += $channel->write(array('data'=>$value), $timestamp);
            }
        }
        // All fine, commit changes
        $api->db->commit();

        if ($saved) $api->status(201);

        $result = array(
            'status'  => 'succes',
            'message' => ($row+1) . ' valid row(s) sended, ' . $saved . ' row(s) inserted'
        );

        $api->render($result);

    } catch (Exception $e) {
        // Rollback all correct data
        $api->db->rollback();
        $api->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
    }
}
