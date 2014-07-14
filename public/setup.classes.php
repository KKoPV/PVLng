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
    public static function run( Array $config ) {

        $i = 1;

        foreach ($config as $class=>$params) {

            $class = 'Setup\\' . $class;
            $class = new $class;

            echo '<h3>', $i++, '. ', $class->getTitle(), '</h3>';

            $messages = array();
            $class->process($params, $messages);

            echo '<ul>';
            foreach ($class->getMessages() as $msg) {
                echo '<li>', $msg, '</li>';
            }
            echo '</ul>';

            if ($class->isError()) return FALSE;
        }

        return TRUE;
    }

}

/**
 *
 */
abstract class SetupTask {

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
    public function isError() {
        return ($this->error === TRUE);
    }

    /**
     *
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     *
     */
    protected $error;

    /**
     *
     */
    protected $messages = array();

    /**
     *
     */
    protected function info() {
        $this->messages[] = implode(' ', func_get_args());
    }

    /**
     *
     */
    protected function success() {
        $this->messages[] = '<span style="color:green">' . implode(' ', func_get_args()) . ' - OK</span>';
    }

    /**
     *
     */
    protected function error() {
        $this->error = TRUE;
        $this->messages[] = '<span style="color:red">' . implode(' ', func_get_args()) . ' - FAILED</span>';
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

/**
 *
 */
class PHPVersion extends SetupTask {

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
        $this->info('Require at least PHP '.$params[0]);
        if (version_compare(PHP_VERSION, $params[0], 'ge')) {
            $this->success('PHP', PHP_VERSION);
        } else {
            $this->error('PHP', PHP_VERSION);
        }
    }

}

/**
 *
 */
class PHPExtensions extends SetupTask {

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
                $this->success($name);
            } else {
                $this->error($name, ': Please install extension:', $ext);
            }
        }
    }

}

/**
 *
 */
class Permissions extends SetupTask {

    /**
     *
     */
    public function getTitle() {
        return 'Check file/directory permissions';
    }

    /**
     *
     */
    public function process( $params, &$messages ) {
        foreach ($params as $test=>$func) {
            if ($func($test)) {
                $this->success(str_replace('_', ' ', $func), '<tt>', $test, '</tt>');
            } else {
                $this->error(str_replace('_', ' ', $func), '<tt>', $test, '</tt>');
            }
        }
    }

}

/**
 *
 */
class Configuration extends SetupTask {

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
            $this->info('<tt>', $params['config'], '</tt> missing');
            $this->info('Try to create from <tt>', $params['default'], '</tt>');
            copy($params['default'], $params['config']);
        }

        if (file_exists($params['config'])) {
            $this->success('<tt>', basename($params['config']), '</tt> exists');
        } else {
            $this->error('Can\'t create <tt>', basename($params['config']), '</tt>');
            $this->error('Please copy <tt>',
                        basename($params['default']),
                        '</tt> to <tt>',
                        basename($params['config']), '</tt>');
        }
    }

}

/**
 *
 */
class MySQLi extends SetupTask {

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
            $this->success('MySQL', $row[0]);
        } else {
            $this->error(htmlspecialchars($db->connect_error));
            $this->info('Please check your database settings');
        }

    }

}
