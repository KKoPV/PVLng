<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
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

            if ($msg) {
                $this->dbg('Message:', $msg);
            }

            // pvlng/<API key>/data/<GUID>[[/<timestamp>]/<value>]
            $topic = array_slice(explode('/', $topic), 3);

            $guid = array_shift($topic);

            switch (count($topic)) {
                default: // 0
                    // Assume JSON data send
                    $data = JSON::decode($msg, true);
                    break;
                case 1:
                    // Assume raw data send for timestamp
                    $data = array('timestamp' => $topic[0], 'data' => $msg);
                    break;
                case 2:
                    // Assume scalar data send for timestamp
                    $data = array('timestamp' => $topic[0], 'data' => $topic[1]);
                    break;
            } // switch

            $result = $this->getChannel($guid)->write($data);

            switch ($result) {
                case 0:
                    $result = 'No row added';
                    break;
                case 1:
                    $result = '1 row added';
                    break;
                default:
                    $result .= ' rows added';
                    break;
            }

            $this->dbg($result);
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

    /**
     * Lazy load channel instances
     */
    protected function getChannel($guid)
    {
        if (!array_key_exists($guid, $this->channels)) {
            $this->channels[$guid] = Channel::byGUID($guid);
        }

        return $this->channels[$guid];
    }
}
