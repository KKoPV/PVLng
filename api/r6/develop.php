<?php
/**
 * Debugging Middleware during development
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
class DevTimerMiddleware extends Slim\Middleware {

    /**
     *
     */
    public function call() {

        $time = microtime(TRUE);

        $this->next->call();

        $time = microtime(TRUE) - $time;
        $memory = memory_get_peak_usage(TRUE);

        $headers = $this->app->Response()->Headers();

        $headers->set('X-Time-Seconds',      $time);
        $headers->set('X-Time-Milliseconds', $time*1000);
        $headers->set('X-Queries',           $this->app->db->getQueryCount());
        $headers->set('X-Memory-Byte',       $memory);
        $headers->set('X-Memory-KByte',      $memory/1024);
        $headers->set('X-Memory-MByte',      $memory/1024/1024);
        $headers->set('X-Version',           PVLNG_VERSION);
        $headers->set('X-API',               $this->app->version);
    }
}

// Apply Middleware
$api->add(new DevTimerMiddleware);
