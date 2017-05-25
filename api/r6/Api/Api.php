<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Api;

/**
 *
 */
use Channel\Channel;
use ORM\ReadingNum as ORMReadingNum;
use Slim\Slim;
use Buffer;

/**
 *
 */
class Api extends Slim
{
    /**
     * Get named parameter as string
     */
    public function strParam($name, $default = '')
    {
        $value = trim($this->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     * Get named parameter as integer
     */
    public function intParam($name, $default = 0)
    {
        $value = trim($this->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     * Get named parameter as numeric value
     */
    public function numParam($name, $default = 0)
    {
        $value = trim($this->request->params($name));
        return is_numeric($value) ? +$value : +$default;
    }

    /**
     * Get named parameter as boolean,
     * all of (true|on|yes|y|x|1) interpreted as TRUE
     */
    public function boolParam($name, $default = false)
    {
        $value = strtolower(trim($this->request->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|y|x|1)$~i', $value) === 1)
             : $default;
    }

    /**
     *
     */
    public function stopAPI($message, $code = 400)
    {
        $this->status($code);
        $this->response()->header('X-Status-Reason', $message);
        $this->render(array(
            'status'  => $code<400 ? 'success' : 'error',
            'message' => $message
        ));
        $this->stop();
    }

    /**
     *
     */
    public function readData($guid, $request)
    {
        try {
            $channel = Channel::byGUID($guid);
        } catch (Exception $e) {
            $this->stopAPI($e->getMessage(), 404);
        }

        // Special models can provide an own GET functionality
        // e.g. for special return formats like PVLog or Sonnenertrag
        if (method_exists($channel, 'GET')) {
            $result = $channel->GET($request);
        } else {
            $data   = $channel->read($request);
            $buffer = new Buffer;

            if ($this->boolParam('attributes', false)) {
                $attr = $channel->getAttributes();

                if ($channel->meter && $this->boolParam('full', false)) {
                    // Calculate overall consumption and costs
                    $cons = ($last = $data->last()) ? $last['data'] : 0;
                    $attr['consumption'] = round($cons, $channel->decimals);
                    $attr['costs'] = round(
                        $cons * $attr['cost'],
                        $this->config->get('Core.Currency.Decimals')
                    );
                }
                $buffer->write($attr);
            }

            $result = $this->formatResult(
                $data, $buffer, $channel->meter, $channel->numeric, $channel->decimals
            );
        }

        if (isset($request['filename'])) {
            $this->view->set('filename', $request['filename']);
        }

        return $result;
    }

    /**
     *
     */
    public function saveBulkCSV($guid, $rows, $sep)
    {

        // Ignore empty datasets
        $rows = array_values(array_filter($rows));

        if (empty($rows)) {
            return;
        }

        try {
            $channel  = Channel::byGUID($guid);
            $bulkdata = array();

            // Ignore empty datasets, track also row Id for error messages
            foreach ($rows as $row => $dataset) {
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

                if (!is_numeric($timestamp)) {
                    $timestamp = strtotime($timestamp);
                }

                if ($timestamp === false) {
                    throw new Exception('Invalid timestamp in row '.($row+1).': "'.$dataset.'"', 400);
                }

                if ($this->dryrun) {
                    echo $timestamp, $sep, $value,
                         ' (', date('Y-m-d H:i:s', $timestamp), ' : ', $value, ')', PHP_EOL;
                } else {
                    $bulkdata[$timestamp] = $value;
                }
            }

            // All fine, insert data
            $saved = ORMReadingNum::f()->insertBulk($channel->entity, $bulkdata);

            if ($saved) {
                $this->status(201);
            }

            $result = array(
                'status'  => 'succes',
                'message' => ($row+1) . ' valid row(s) sended, ' . $saved . ' row(s) inserted/updated'
            );

            $this->render($result);
        } catch (Exception $e) {
            $this->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
        }
    }

    /**
     *
     */
    public function saveCSV($guid, $rows, $sep)
    {

        // Ignore empty datasets
        $rows = array_values(array_filter($rows));

        if (empty($rows)) {
            return;
        }

        try {
            // Disable AutoCommit in case of errors
            $this->db->autocommit(false);
            $saved = 0;

            $channel = Channel::byGUID($guid);

            // Ignore empty datasets, track also row Id for error messages
            foreach ($rows as $row => $dataset) {
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

                if (!is_numeric($timestamp)) {
                    $timestamp = strtotime($timestamp);
                }

                if ($timestamp === false) {
                    throw new Exception('Invalid timestamp in row '.($row+1).': "'.$dataset.'"', 400);
                }

                if ($this->dryrun) {
                    echo $timestamp, $sep, $value,
                         ' (', date('Y-m-d H:i:s', $timestamp), ' : ', $value, ')', PHP_EOL;
                } else {
                    $saved += $channel->write(array('data'=>$value), $timestamp);
                }
            }
            // All fine, commit changes
            $this->db->commit();

            if ($saved) {
                $this->status(201);
            }

            $result = array(
                'status'  => 'succes',
                'message' => ($row+1) . ' valid row(s) sended, '
                           . $saved . ' row(s) inserted/updated'
            );

            $this->render($result);
        } catch (Exception $e) {
            // Rollback all correct data
            $this->db->rollback();
            $this->stopAPI($e->getMessage() . '; No data saved!', $e->getCode());
        }
    }

    /**
     *
     */
    public function formatResult(Buffer $data, Buffer $result, $meter, $numeric, $decimals)
    {
        $full  = $this->boolParam('full', false);
        $short = $this->boolParam('short', false);

        // Optimized flow, 1st "switch" then "loop" ...
        switch (true) {
            // Passthrough all values as numeric based array
            case $full && $short:
                foreach ($data as $row) {
                    $numeric && $this->roundData($row, $decimals);
                    if (!$meter) {
                        unset($row['consumption']);
                    }
                    $result->write(array_values($row));
                }
                break;

            // Do nothing special, use as is
            case $full:
                foreach ($data as $row) {
                    $numeric && $this->roundData($row, $decimals);
                    if (!$meter) {
                        unset($row['consumption']);
                    }
                    $result->write($row);
                }
                break;

            // Default mobile result: only timestamp and data
            case $short:
                foreach ($data as $row) {
                    $numeric && $this->roundData($row, $decimals);
                    $result->write(array(
                        /* 0 */ $row['timestamp'],
                        /* 1 */ $row['data']
                    ));
                }
                break;

            // Default result: only timestamp and data
            default:
                foreach ($data as $row) {
                    $numeric && $this->roundData($row, $decimals);
                    $result->write(array(
                        'timestamp' => $row['timestamp'],
                        'data'      => $row['data']
                    ));
                }
                break;
        } // switch

        // Free memory
        $data->close();

        return $result;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function roundData(&$data, $decimals)
    {
        foreach (array('min', 'max', 'data', 'consumption') as $key) {
            $data[$key] = round($data[$key], $decimals);
        }
    }
}
