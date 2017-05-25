<?php
/**
 *
 *
 * $wb = new \Equipment\SMA\Webbox('192.168.1.168');
 * echo $wb->test();
 *
 */
namespace Equipment\SMA;

/**
 *
 */
class Webbox
{
    /**
     *
     */
    public function __construct($host, $port = 80)
    {
        if (strstr($host, '://') == '') {
            $host = 'http://' . $host;
        }
        $this->url = $host . ':' . $port . '/rpc';
        $this->curl = curl_init($this->url);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLINFO_HEADER_OUT, false);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    }

    /**
     *
     */
    public function getPlantOverview()
    {
        return $this->call($this->initRPC('GetPlantOverview'));
    }

    /**
     *
     */
    public function getDevices()
    {
        return $this->call($this->initRPC('GetDevices'));
    }

    /**
     *
     */
    public function getProcessDataChannels($device)
    {
        $rpc = $this->initRPC('GetProcessDataChannels');
        $rpc->params = new \StdClass;
        $rpc->params->device = $device;
        return $this->call($rpc);
    }

    /**
     *
     */
    public function getProcessData($device, $channels = null)
    {
        $rpc = $this->initRPC('GetProcessData');
        $rpc->params = new \StdClass;
        $rpc->params->devices = array();
        if (!is_null($channels) and !is_array($channels)) {
            $channels = array($channels);
        }
        foreach ((array) $device as $key) {
            $d = new \StdClass;
            $d->key = $key;
            $d->channels = $channels;
            $rpc->params->devices[] = $d;
        }
        return $this->call($rpc);
    }

    /**
     *
     */
    public function getParameter($device, $channels = null)
    {
        $rpc = $this->initRPC('GetParameter');
        $rpc->params = new \StdClass;
        $rpc->params->devices = array();
        foreach ((array) $device as $key) {
            $d = new \StdClass;
            $d->key = $key;
            $d->channels = $channels;
            $rpc->params->devices[] = $d;
        }
        return $this->call($rpc);
    }

    /**
     *
     */
    public function test()
    {
        ob_start();

        $this->log($wb, $wb->getPlantOverview());

        $result = $wb->getDevices();
        $this->log($wb, $result);

        foreach ($result->devices as $device) {
            $this->log($wb, $wb->getProcessDataChannels($device->key));
            $this->log($wb, $wb->getProcessData($device->key));
            #    $this->log($wb, $wb->getProcessData($device->key, 'TmpMdul C'));
            $this->log($wb, $wb->getParameter($device->key));
        }

        return ob_get_clean();
    }

    /**
     *
     */
    public function info($opt = '')
    {
        return ($opt and isset($this->info[$opt])) ? $this->info[$opt] : $this->info;
    }

    /**
     *
     */
    public function verbose($verbose)
    {
        curl_setopt($this->curl, CURLOPT_VERBOSE, !!$verbose);
    }

    /**
     *
     */
    public function isError()
    {
        return ($this->error != '');
    }

    /**
     *
     */
    public function error()
    {
        return $this->error;
    }

    /**
     *
     */
    public function response()
    {
        return $this->response;
    }

    /**
     *
     */
    public function query()
    {
        return $this->call;
    }

    /**
     *
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $url;

    /**
     *
     */
    protected $curl;

    /**
     *
     */
    protected $call;

    /**
     *
     */
    protected $response;

    /**
     *
     */
    protected $info;

    /**
     *
     */
    protected $error;

    /**
     *
     */
    protected function initRPC($proc)
    {
        $rpc = new \StdClass;
        $rpc->version = '1.0';
        $rpc->id      = (string) rand(1000, 9999);
        $rpc->format  = 'JSON';
        $rpc->proc    = $proc;
        return $rpc;
    }

    /**
     *
     */
    protected function call($rpc)
    {

        $this->error = '';

        $call = 'RPC='.json_encode($rpc);

        // Set POST fields for this call
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $call);

        $this->call = 'POST ' . $this->url . '?' . $call;

        $this->response = curl_exec($this->curl);

        $this->info = curl_getinfo($this->curl);

        if (!$this->response) {
            $this->error = 'Curl error (' . curl_errno($this->curl) . '): ' . curl_error($this->curl);
            return false;
        }

        // Got answer
        $result = json_decode($this->response);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                if (isset($result->result)) {
                    // Fine, return result
                    $this->error = false;
                    return $result->result;
                } else {
                    // Set error, return FALSE at end
                    $this->error = $result->error;
                }
                break;
            case JSON_ERROR_DEPTH:
                $this->error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $this->error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $this->error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $this->error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $this->error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $this->error = 'Unknown error';
                break;
        }

        return false;
    }

    /**
     *
     */
    protected function log($wb, $result)
    {
        echo str_repeat('-', 78), PHP_EOL, $wb->query(), PHP_EOL;
        if (!$wb->isError()) {
            echo 'Response: ', $wb->response(), PHP_EOL;
            echo 'Result: ', print_r($result, true);
        } else {
            echo 'ERROR: ', $wb->error();
        }
        echo PHP_EOL, PHP_EOL;
    }
}
