<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->get(
    '/status',
    $APIkeyRequired,
    function () use ($api) {
        $result = array(
            'version' => exec('cat /proc/version')
        );

        // http://www.linuxinsight.com/proc_uptime.html
        // This file contains the length of time since the system was booted,
        // as well as the amount of time since then that the system has been idle.
        // Both are given as floating-point values, in seconds.
        $res = explode(' ', exec('cat /proc/uptime'));
        if (!empty($res)) {
            $result['uptime'] = array(
                'overall'         => +$res[0],
                'overall_minutes' => $res[0]/60,
                'overall_hours'   => $res[0]/3600,
                'overall_days'    => $res[0]/86400,
                'idle'            => +$res[1],
                'idle_minutes'    => $res[1]/60,
                'idle_hours'      => $res[1]/3600,
                'idle_days'       => $res[1]/86400
            );
        }

        //              total       used       free     shared    buffers     cached
        // Mem:          1771       1714         57          0        215       1178
        // Swap:         1905          6       1898
        // Total:        3676       1721       1955
        exec('free -mto', $res);

        if (preg_match_all('~^(\w+): +(\S+) +(\S+) +(\S+)~m', implode("\n", $res), $args, PREG_SET_ORDER)) {
            foreach ($args as $arg) {
                $result['memory'][$arg[1]] = array(
                    'total_mb' => +$arg[2],
                    'total_gb' => $arg[2]/1024,
                    'used_mb'  => +$arg[3],
                    'used_gb'  => $arg[3]/1024,
                    'free_mb'  => +$arg[4],
                    'free_gb'  => $arg[4]/1024
                );
            }
        }

        // http://juliano.info/en/Blog:Memory_Leak/Understanding_the_Linux_load_average
        // These values represent the average system load in the last 1, 5 and 15 minutes,
        // the number of active and total scheduling entities (tasks) and
        // the PID of the last created process in the system.
        $res = exec('cat /proc/loadavg');
        if (preg_match('~([0-9.]+) ([0-9.]+) ([0-9.]+) (\d+)/(\d+)~', $res, $args)) {
            $result['load'] = array(
                'minutes_1'  => +$args[1],
                'minutes_5'  => +$args[2],
                'minutes_15' => +$args[3],
                'active'     => +$args[4],
                'total'      => +$args[5]
            );
        }

        exec('cat /proc/cpuinfo', $res);

        if (preg_match_all('~^([^\t]+)\s*:\s*(.+)$~m', implode("\n", $res), $args, PREG_SET_ORDER)) {
            foreach ($args as $arg) {
                $result['cpuinfo'][str_replace(' ', '_', $arg[1])] =
                (string) +$arg[2] == $arg[2] ? +$arg[2] : $arg[2];
            }
        }

        $api->response->headers->set('Content-Type', 'application/json');

        $api->render($result);
    }
)
->name('GET /status')
->help = array(
    'since'       => 'r2',
    'description' => 'System status',
    'apikey'      => true,
);
