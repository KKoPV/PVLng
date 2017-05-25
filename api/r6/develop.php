<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @codingStandardsIgnoreFile
 */
class DevTimerMiddleware extends Slim\Middleware
{
    /**
     *
     */
    public function call()
    {
        $time = microtime(true);

        $this->next->call();

        $time = microtime(true) - $time;
        $memory = memory_get_peak_usage(true);

        $headers = $this->app->Response()->Headers();

        $headers->set('X-Time-Seconds', $time);
        $headers->set('X-Time-Milliseconds', $time*1000);
        $headers->set('X-Queries', $this->app->db->getQueryCount());
        $headers->set('X-Memory-Byte', $memory);
        $headers->set('X-Memory-KByte', $memory/1024);
        $headers->set('X-Memory-MByte', $memory/1024/1024);
        $headers->set('X-Version', PVLNG_VERSION);
        $headers->set('X-API', $this->app->version);
    }
}
