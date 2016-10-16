<?php
/**
 * API class
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
class Api extends Slim\Slim
{

    /**
     * Get named parameter as string
     */
    public function strParam($name, $default='')
    {
        $value = trim($this->request->params($name));
        return !is_null($value) ? $value : $default;
    }

    /**
     * Get named parameter as integer
     */
    public function intParam($name, $default=0)
    {
        $value = trim($this->request->params($name));
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     * Get named parameter as boolean, all of (true|on|yes|1) interpreted as TRUE
     */
    public function boolParam($name, $default=false)
    {
        $value = strtolower(trim($this->request->params($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }

    /**
     *
     */
    public function stopAPI($message, $code=400)
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
            $return = $channel->GET($request);
            $filename = isset($request['filename']) ? $request['filename'] : null;
            $this->render($return, array('filename'=>$filename));
            exit;
        }

        $buffer = $channel->read($request);
        $result = new Buffer;

        $full  = $this->boolParam('full', false);
        $short = $this->boolParam('short', false);

        if ($this->boolParam('attributes', false)) {
            $attr = $channel->getAttributes();

            if ($full && $channel->meter) {
                // Calculate overall consumption and costs
                $cons = 0;
                // Loop all rows to get value from last row if exists
                foreach ($buffer as $row) $cons = $row['data'];
                $attr['consumption'] = round($cons, $attr['decimals']);
                $attr['costs'] = round(
                    $cons * $attr['cost'],
                    $this->config->get('Core.Currency.Decimals')
                );
            }
            $result->write($attr);
        }

        // Optimized flow, 1st "if" then "loop" ...
        switch (true) {

            // Passthrough all values as numeric based array
            case $full && $short:
                foreach ($buffer as $row) {
                    if (!$channel->meter) unset($row['consumption']);
                    $result->write(array_values($row));
                }
                break;

            // Do nothing, use as is
            case $full:
                foreach ($buffer as $row) {
                    if (!$channel->meter) unset($row['consumption']);
                    $result->write($row);
                }
                break;

            // Default mobile result: only timestamp and data
            case $short:
                foreach ($buffer as $row) {
                    $result->write(array(
                        /* 0 */ $row['timestamp'],
                        /* 1 */ $row['data']
                    ));
                }
                break;

            // Default result: only timestamp and data
            default:
                foreach ($buffer as $row) {
                    $result->write(array(
                        'timestamp' => $row['timestamp'],
                        'data'      => $row['data']
                    ));
                }

        } // switch

        return $result;
    }
}
