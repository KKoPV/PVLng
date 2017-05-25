<?php
/**
 * MQTT listener for channel data
 *
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2017 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */
use Channel\Channel;
use PVLng\PVLng;

/**
 *
 */
class MQTT
{
    /**
     *
     */
    public $qos = 0;

    /**
     *
     */
    public $verbose = 0;

    /**
     *
     */
    private $phpMQTT;

    /**
     *
     */
    public function __construct($server, $port)
    {
        $this->phpMQTT = new phpMQTT($server, $port, 'PVLng');
        if (!$this->phpMQTT->connect(false)) {
            exit(1);
        }
    }

    /**
     *
     */
    public function run()
    {
        /**
         * Listen only for messages for API key
         */
        $topic  = 'pvlng/'.PVLng::getApiKey().'/data/#';

        $this->dbg('Listen for', $topic, '...');

        $this->phpMQTT->debug = $this->verbose;

        $this->phpMQTT->subscribe(array(
            $topic => array(
                'function' => array($this, 'saveData'),
                'qos'      => $this->qos
            )
        ));

        while ($this->phpMQTT->proc()) {
            // Wait a bit
            sleep(1);
        }
    }

    /**
     * Topic callback
     */
    public function saveData($topic, $msg)
    {
        $this->dbg('Topic:', $topic);

        if ($data = json_decode($msg, true)) {
            $this->dbg('Message:', $msg);

            // pvlng/<API key>/data/<GUID>
            list(,,,$guid) = explode('/', $topic);

            if (!array_key_exists($guid, $this->channels)) {
                $this->channels[$guid] = Channel::byGUID($guid);
            }

            try {
                $rows = $this->channels[$guid]->write($data);
                $this->dbg(1, 'Result:', $rows, 'row(s) added');
            } catch (Exception $e) {
                $this->dbg('ERROR:', $e->getMessage());
            }
        } else {
            $this->dbg('INVALID:', $msg);
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
