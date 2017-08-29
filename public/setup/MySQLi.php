<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
class MySQLi extends SetupTask
{
    /**
     * Seconds for uptime calculation
     */
    const SECONDS_PER_HOUR = 60*60;
    const SECONDS_PER_DAY  = 24*60*60;

    /**
     *
     */
    public $title = 'MySQL configuration';

    /**
     *
     */
    public function process($params)
    {
        if (strstr($params['config'], '.php')) {
            $config = include $params['config'];
        } elseif (strstr($params['config'], '.yaml')) {
            $config = \Spyc::YAMLLoad($params['config']);
        }

        $db = mysqli_init();

        $db->real_connect(
            self::arrayPath2Key($config, $params['credentials']['host']),
            self::arrayPath2Key($config, $params['credentials']['username']),
            self::arrayPath2Key($config, $params['credentials']['password']),
            self::arrayPath2Key($config, $params['credentials']['database']),
            self::arrayPath2Key($config, $params['credentials']['port']),
            self::arrayPath2Key($config, $params['credentials']['socket']),
            MYSQLI_CLIENT_COMPRESS
        );

        if (!$db->connect_error) {
            $this->success('MySQL', $db->server_info);

            $res = $db->query('SHOW GLOBAL STATUS LIKE "Uptime"');
            // Seconds
            $s = $res->fetch_row()[1];
            // Days
            $d = floor($s / static::SECONDS_PER_DAY);
            $s -= $d * static::SECONDS_PER_DAY;
            // Hours
            $h = floor($s / static::SECONDS_PER_HOUR);
            $s -= $h * static::SECONDS_PER_HOUR;
            // Minutes
            $m = floor($s / 60);
            // Seconds left
            $s -= $m * 60;
            $this->info(sprintf('Uptime:<tt> %dd %02d:%02d:%02d</tt>', $d, $h, $m, $s));

            if (isset($params['variables'])) {
                $this->subTitle('MySQL settings');

                foreach ($params['variables'] as $var => $value) {
                    if ($res = $db->query('SELECT @@'.$var)) {
                        if (($row = $res->fetch_row()) &&
                            in_array(strtoupper($row[0]), $value[0])) {
                            $this->success($var, '=', $row[0]);
                        } else {
                            $url = sprintf('<a href="%s">expected was "%s"</a>', $value[1], $value[0][0]);
                            $this->error($var, '=', $row[0], '-', $url);
                        }
                    } else {
                        $this->error('INVALID:', $var);
                    }
                }
            }
        } else {
            $this->error(htmlspecialchars($db->connect_error));
            $this->info('Please check your database settings!');
        }
    }
}
