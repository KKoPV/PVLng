<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
abstract class Setup {

    /**
     *
     */
    abstract public function getTitle();

    /**
     * Prepare result array
     */
    abstract public function process( $params, &$messages );

    /**
     *
     */
    public static function run( Array $config ) {

        $i = 1;
        self::$ok = TRUE;

        foreach ($config as $class=>$params) {

            $class = 'Setup\\' . $class;
            $class = new $class;

            echo '<h3>', $i++, '. ', $class->getTitle(), '</h3>';

            $messages = array();
            $class->process($params, $messages);

            echo '<ul', ($ok !== FALSE ? '>' : ' style="color:red">');
            foreach($messages as $msg) {
                echo '<li>', $msg, '</li>';
            }
            echo '</ul>';

            if (self::$ok === FALSE) return FALSE;
        }

        return TRUE;
    }

    /**
     *
     */
    protected static $ok;

    /**
     *
     */
    protected static function success() {
        return '<span style="color:green">' . implode(' ', func_get_args()) . '</span>';
    }

    /**
     *
     */
    protected static function error() {
        self::$ok = FALSE;
        return '<span style="color:red">' . implode(' ', func_get_args()) . '</span>';
    }

}

/**
 *
 */
class PHPVersion extends Setup {

    /**
     *
     */
    public function getTitle() {
        return 'Check required PHP Version';
    }

    /**
     *
     */
    public function process( $params, &$messages ) {
        if (version_compare(PHP_VERSION, $params[0], 'ge')) {
            $messages[] = self::success('Require PHP', $params[0], '- OK : PHP', PHP_VERSION);
        } else {
            $messages[] = self::error('Require PHP', $params[0], '- FAILED : PHP', PHP_VERSION);
        }
    }

}

/**
 *
 */
class PHPExtensions extends Setup {

    /**
     *
     */
    public function getTitle() {
        return 'Check required PHP Extensions';
    }

    /**
     *
     */
    public function process( $params, &$messages ) {
        foreach ($params as $ext=>$name) {
            if (extension_loaded($ext)) {
                $messages[] = self::success($name, '- OK');
            } else {
                $messages[] = self::error($name, '- FAILED : Please install extension:', $ext);
            }
        }
    }

}

/**
 *
 */
class Configuration extends Setup {

    /**
     *
     */
    public function getTitle() {
        return 'Check configuration file';
    }

    /**
     *
     */
    public function process( $params, &$messages ) {
        if (!file_exists($params['config'])) {
            $messages[] = '<tt>' . $params['config'] . '</tt> missing';
            $messages[] = 'Try to create from <tt>' . $params['default'] . '</tt>';
            copy($params['default'], $params['config']);
        }

        if (file_exists($params['config'])) {
            $messages[] = self::success('<tt>' . basename($params['config']) . '</tt> exists');
        } else {
            $messages[] = self::error('Can\'t create <tt>' . basename($params['config']) . '</tt>');
            $messages[] = self::error('Please copy <tt>'
                                    . basename($params['default']) . '</tt> to <tt>'
                                    . basename($params['config']) . '</tt>');
        }
    }

}

/**
 *
 */
class MySQLi extends Setup {

    /**
     *
     */
    public function getTitle() {
        return 'Check database configuration';
    }

    /**
     *
     */
    public function process( $params, &$messages ) {
        $config = include $params['config'];

        $db = new \MySQLi(
            self::arrayPath2Key($config, $params['host']),
            self::arrayPath2Key($config, $params['user']),
            self::arrayPath2Key($config, $params['pass']),
            self::arrayPath2Key($config, $params['db']),
            self::arrayPath2Key($config, $params['port']),
            self::arrayPath2Key($config, $params['socket'])
        );

        if (!$db->connect_error) {
            $res = $db->query('SELECT version()');
            $row = $res->fetch_array();
            $messages[] = self::success('Connection OK: MySQL', $row[0]);
        } else {
            $messages[] = self::error(htmlspecialchars($db->connect_error));
            $messages[] = self::error('Please check your database settings');
        }

    }

    /**
     *
     */
    protected function arrayPath2Key( $array, $key ) {
        $p = &$array;
        $path = explode('.', $key);
        while ($key = array_shift($path)) {
            $p = &$p[$key];
        }
        return $p;
    }

}
