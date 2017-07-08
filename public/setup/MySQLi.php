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
     *
     */
    public $title = 'Check database configuration';

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

        $db = @(new \MySQLi(
            self::arrayPath2Key($config, $params['host']),
            self::arrayPath2Key($config, $params['username']),
            self::arrayPath2Key($config, $params['password']),
            self::arrayPath2Key($config, $params['database']),
            (int) self::arrayPath2Key($config, $params['port']),
            self::arrayPath2Key($config, $params['socket'])
        ));

        if (!$db->connect_error) {
            $this->success(
                'MySQL',
                $db->query('SELECT version()')->fetch_array(MYSQL_NUM)[0]
            );
        } else {
            $this->error(htmlspecialchars($db->connect_error));
            $this->info('Please check your database settings!');
        }
    }
}
