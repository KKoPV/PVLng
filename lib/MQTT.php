<?php
/**
 * MQTT listener for channel data
 *
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2017 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */
use Channel\Channel;
use Core\JSON;
use Core\PVLng;

/**
 *
 */
class MQTT
{
    /**
     *
     */
    public function __construct($server, $port)
    {
        $this->phpMQTT = new phpMQTT($server, $port, 'PVLng');

        if (!$this->phpMQTT->connect(true)) {
            exit(1);
        }
    }

    /**
     * Loop
     */
    public function run($verbose = false)
    {
        $this->verbose = $verbose;

        /**
         * Listen only for messages for API key
         */
        $topic = 'pvlng/'.PVLng::getApiKey().'/data/#';

        $this->dbg('Listen for', $topic, '...');

        $this->phpMQTT->subscribe(array(
            $topic => array(
                'function' => array($this, 'saveData'),
                'qos'      => 0
            )
        ));

        // Endless loop
        while ($this->phpMQTT->proc()) {
            usleep(200000);
        }
    }

    /**
     * Topic callback
     */
    public function saveData($topic, $msg)
    {
        try {
            // Sometimes there is a \000 at the begin of the message...
            if (strpos($msg, '{') !== false) {
                $msg = preg_replace('~^[^{]+~', '', $msg);
            }

            $this->dbg('Topic:', $topic);
            $this->dbg('Message:', $msg);

            // pvlng/<API key>/data/<GUID>[/<timestamp>]
            $topic = array_slice(explode('/', $topic), 3);

            $guid = array_shift($topic);

            if (empty($topic)) {
                // Assume JSON data send
                $data = JSON::decode($msg, true);
            } else {
                // Assume raw data send
                $data = array('data' => $msg, 'timestamp' => $topic[0]);
            }

            if (!array_key_exists($guid, $this->channels)) {
                $this->channels[$guid] = Channel::byGUID($guid);
            }

            $rows = $this->channels[$guid]->write($data);

            $this->dbg('Result:', $rows, 'row(s) added');
        } catch (Exception $e) {
            $this->dbg('ERROR: '.$e->getMessage());
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->phpMQTT->close();
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $phpMQTT;

    /**
     *
     */
    protected $verbose = 0;

    /**
     *
     */
    protected $channels = array();

    /**
     *
     */
    protected function dbg($level)
    {
        if ($this->verbose) {
            printf('[%s] %s'.PHP_EOL, date('c'), implode(' ', func_get_args()));
        }
    }
}
