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

        $this->dbg('!', 'Listen for "' . $topic . '" ...');

        $this->phpMQTT->subscribe([
            $topic => ['function' => [$this, 'saveData'], 'qos' => 0]
        ]);

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
            $this->dbg('>', $topic);

            // pvlng/<API key>/data/<GUID>[[/<timestamp>]/<value>]
            $aTopic = array_slice(explode('/', $topic), 3);

            $guid = array_shift($aTopic);

            if ($guid == '') {
                throw new Exception('Invalid topic: '.$topic);
            }

            // Sometimes there is a \000 at the begin of the message...
            if (strpos($msg, '{') !== false) {
                $msg = preg_replace('~^[^{]+~', '', $msg);
            }

            $this->dbg('>', $msg);

            $data = JSON::decode($msg, true);

            $ts = -microtime(true);

            $result = $this->getChannel($guid)->write($data);

            $this->dbg('<', $result, 'row(s) added in', round(($ts+microtime(true))*1000), 'ms');
        } catch (Exception $e) {
            $this->dbg('-', $e->getMessage());
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
    protected $channels = [];

    /**
     *
     */
    protected function dbg()
    {
        if ($this->verbose) {
            printf(
                '[%s] %s'.PHP_EOL,
                date('Y-m-d H:i:s'),
                implode(' ', func_get_args())
            );
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

    /**
     * Support microseconds for date strings
     *
     * Idea from http://php.net/manual/de/datetime.format.php#113607
     */
    protected function date($format, $timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = microtime(true);
        }

        $ts = floor($timestamp);
        $ms = round(($timestamp - $ts) * 1e6);
        // Make 6 char long
        $ms = sprintf('%06d', $ms);

        // Replace unescaped "u" with the calculated microseconds
        $format = preg_replace('~(?<!\\\\)u~', $ms, $format);

        return date($format, $ts);
    }
}
