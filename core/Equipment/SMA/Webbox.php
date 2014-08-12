<?php

namespace Equipment\SMA;

class Webbox {

    /**
     *
     */
    public function __construct( $host, $port=80 ) {
        if (strstr($host, '://') == '') $host = 'http://' . $host;
        $this->url = $host . ':' . $port . '/rpc';
        $this->curl = curl_init($this->url);
        curl_setopt($this->curl, CURLOPT_HEADER, FALSE);
        curl_setopt($this->curl, CURLINFO_HEADER_OUT, FALSE);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->curl, CURLOPT_POST, TRUE);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    }

    /**
     *
     */
    public function GetPlantOverview() {
        return $this->call($this->initRPC('GetPlantOverview'));
    }

    /**
     *
     */
    public function GetDevices() {
        return $this->call($this->initRPC('GetDevices'));
    }

    /**
     *
     */
    public function GetProcessDataChannels( $device ) {
        $rpc = $this->initRPC('GetProcessDataChannels');
        $rpc->params = new \StdClass;
        $rpc->params->device = $device;
        return $this->call($rpc);
    }

    /**
     *
     */
    public function GetProcessData( $device, $channels=NULL ) {
        $rpc = $this->initRPC('GetProcessData');
        $rpc->params = new \StdClass;
        $rpc->params->devices = array();
        if (!is_null($channels) AND !is_array($channels)) $channels = array($channels);
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
    public function GetParameter( $device, $channels=NULL ) {
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
    public function info( $opt='' ) {
        return ($opt AND isset($this->info[$opt])) ? $this->info[$opt] : $this->info;
    }

    /**
     *
     */
    public function verbose( $verbose ) {
        curl_setopt($this->curl, CURLOPT_VERBOSE, !!$verbose);
    }

    /**
     *
     */
    public function isError() {
        return ($this->error != '');
    }

    /**
     *
     */
    public function error() {
        return $this->error;
    }

    /**
     *
     */
    public function response() {
        return $this->response;
    }

    /**
     *
     */
    public function query() {
        return $this->call;
    }

    /**
     *
     */
    public function __destruct() {
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
    protected function initRPC( $proc ) {
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
    protected function call( $rpc ) {

        $this->error = '';

        $call = 'RPC='.json_encode($rpc);

        // Set POST fields for this call
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $call);

        $this->call = 'POST ' . $this->url . '?' . $call;

        $this->response = curl_exec($this->curl);

        $this->info = curl_getinfo($this->curl);

        if (!$this->response) {
            $this->error = 'Curl error (' . curl_errno($this->curl) . '): ' . curl_error($this->curl);
            return FALSE;
        }

        // Got answer
        $result = json_decode($this->response);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                if (isset($result->result)) {
                    // Fine, return result
                    $this->error = FALSE;
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

        return FALSE;
    }
}

/**
 *
 */
function log($wb, $result) {
    echo str_repeat('-', 78), PHP_EOL, $wb->query(), PHP_EOL;
    if (!$wb->isError()) {
        echo 'Response: ', $wb->response(), PHP_EOL;
        echo 'Result: ', print_r($result, TRUE);
    } else {
        echo 'ERROR: ', $wb->error();
    }
    echo PHP_EOL, PHP_EOL;
}

$wb = new \Equipment\SMA\Webbox('192.168.1.168');

log($wb, $wb->GetPlantOverview());

$result = $wb->getDevices();
log($wb, $result);

foreach ($result->devices as $device) {
    log($wb, $wb->GetProcessDataChannels($device->key));
    log($wb, $wb->GetProcessData($device->key));
#    log($wb, $wb->GetProcessData($device->key, 'TmpMdul C'));
    log($wb, $wb->GetParameter($device->key));
}
/* */
