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
namespace Formatter\Special;

/**
 *
 */
use Formatter\JSON;
use Buffer;

/**
 * https://www.pvcast.de/api#measurements
 */
class PVCAST extends JSON
{
    /**
     *
     */
    public function render($result)
    {
        if (!($result instanceof Buffer)) {
            $this->app->stopAPI('Invalid channel for this formatter', 404);
        }

        // Always JSON
        $this->app->ContentType('application/json;charset=utf-8');

        echo '{"measurements":{';

        $count = count($result);
        $i = 0;
        foreach ($result as $row) {
            if ($i > 0 && $i < $count) {
                echo ',';
            }
            printf('"%d":{"power":%d}', $row['timestamp'], $row['data']);
            $i++;
        }

        echo '}}';
    }
}
