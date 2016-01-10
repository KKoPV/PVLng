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

            $class->process($params);

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
    abstract public function process( $params );

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
        $this->messages[] = '<span style="color:green">' . implode(' ', func_get_args()) . ' - <strong>OK</strong></span>';
    }

    /**
     *
     */
    protected function error() {
        $this->error = TRUE;
        $this->messages[] = '<span style="color:red">' . implode(' ', func_get_args()) . ' - <strong>FAILED</strong></span>';
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
    public function process( $params ) {
        // $params == min. version
        $this->info('Require at least PHP', $params);
        if (version_compare(PHP_VERSION, $params, 'ge')) {
            $this->success('Found PHP', PHP_VERSION);
        } else {
            $this->error('Found PHP', PHP_VERSION);
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
    public function process( $params ) {
        foreach ($params as $ext=>$data) {
            if (extension_loaded($ext)) {
                $this->success($data[0]);
            } elseif (!isset($data[1]) OR $data[1]) {
                $this->error(sprintf($this->PHPLink, $ext, $data[0]), ' - Please install extension');
            } else {
                $this->info(sprintf($this->PHPLink, $ext, $data[0]), ' - <i>not installed, but recommended</i>');
            }
        }
    }

    /**
     *
     */
    protected $PHPLink = '<a href="http://php.net/manual/book.%s.php" target="_blank">%s</a>';
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
    public function process( $params ) {
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
    public function process( $params ) {
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
    public function process( $params ) {
        $config = include $params['config'];

        $db = @(new \MySQLi(
            self::arrayPath2Key($config, $params['host']),
            self::arrayPath2Key($config, $params['user']),
            self::arrayPath2Key($config, $params['pass']),
            self::arrayPath2Key($config, $params['db']),
            (int) self::arrayPath2Key($config, $params['port']),
            self::arrayPath2Key($config, $params['socket'])
        ));

        if (!$db->connect_error) {
            $this->success('Connect to MySQL ( Version',
                           $db->query('SELECT version()')->fetch_array(MYSQL_NUM)[0],
                           ')');
        } else {
            $this->error(htmlspecialchars($db->connect_error));
            $this->info('Please check your database settings!');
        }

    }

}
