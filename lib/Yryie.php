<?php
/**
 * Yryie
 *
 * Buffer debugging information
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2006-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    3.2.0
 */
class Yryie
{
    /**
     *
     */
    const VERSION = '3.2.0';

    /**
     *
     */
    const MAXSTRLEN         = 100;
    const TIME_SECONDS      = 1;
    const TIME_MICROSECONDS = 2;
    const TIME_AUTO         = 3;

    /**
     * File to write the debug stack during each add()
     *
     * @var string
     */
    public static $TraceFile;

    /**
     *
     * @var string
     */
    public static $TraceDelimiter = "\t";

    /**
     *
     * @var string
     */
    public static $TimerStart = '>>';

    /**
     *
     * @var string
     */
    public static $TimerStop = '<<';

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     *
     */
    public static function transformCode(&$code, $functions = false)
    {
        // Single line comments: /// PHP code...
        $code = preg_replace('~^(\s*)///\s+([^*]*?)$~m', '$1$2 /// AOP', $code);

        // Multi line comments start: /* ///
        $code = preg_replace('~^(\s*)/\*\s+///(.*?)$~m', '$1/// >>> AOP$2', $code);
        // Multi line comments end: /// */
        $code = preg_replace('~^(\s*)///\s+\*/$~m', '$1/// <<< AOP', $code);

        if ($functions &&
            preg_match_all('~function\s+(\w+)[^{]+?{~', $code, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $code = str_replace($match[0], $match[0] . ' \Yryie::Call(func_get_args()); /* AOP */ ', $code);
            }
        }
    }

    /**
     * Add version infos on top of stack
     *
     * @return void
     */
    public static function versions()
    {
        self::add(php_uname(), 'version');
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            self::add($_SERVER['SERVER_SOFTWARE'], 'version');
        }
        self::add('PHP '.PHP_VERSION, 'version');
    } // function Register()

    /**
     * Add content of env. variables to output
     *
     * @param string $vars List of variables to include
     * @return void
     */
    public static function envVars($vars = 'EGPCS')
    {
        $l = strlen($vars);
        for ($i=0; $i<$l; $i++) {
            switch ($vars{$i}) {
                case 'E':
                    !empty($_ENV)    && self::Debug('$_ENV : '.print_r($_ENV, true));
                    break;
                case 'G':
                    !empty($_GET)    && self::Debug('$_GET : '.print_r($_GET, true));
                    break;
                case 'P':
                    !empty($_POST)   && self::Debug('$_POST : '.print_r($_POST, true));
                    break;
                case 'C':
                    !empty($_COOKIE) && self::Debug('$_COOKIE : '.print_r($_COOKIE, true));
                    break;
                case 'S':
                    !empty($_SERVER) && self::Debug('$_SERVER : '.print_r($_SERVER, true));
                    break;
            }
        }
    }

    /**
     * Register error handler
     *
     * @return void
     */
    public static function register()
    {
        set_error_handler(array(__CLASS__, 'HandleError'));
    } // function Register()

    /**
     * Set Active/Inactive
     *
     * Default class state: Inactive
     *
     * @param bool $active
     * @return void
     */
    public static function active($active = null)
    {
        self::Trace();
        if (isset($active)) {
            self::$Active = (bool) $active;
        }
        return self::$Active;
    } // function Active()

    /**
     * Set TimeUnit...
     *
     * @param int $TimeUnit (Yryie::TIME_SECONDS|Yryie::TIME_MICROSECONDS|Yryie::TIME_AUTO)
     * @return void
     */
    public static function timeUnit($TimeUnit)
    {
        self::$TimeUnit = (int) $TimeUnit;
    } // function TimeUnit()

    /**
     * Add info
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function info($message)
    {
        if (!self::$Active) {
            return;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            $message = array_shift($args);
            $message = vsprintf($message, $args);
        }
        self::add($message, 'info');
    } // function Info()

    /**
     * Add code
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function code($message)
    {
        if (!self::$Active) {
            return;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            $message = array_shift($args);
            $message = vsprintf($message, $args);
        }
        self::add($message, 'code');
    } // function Code()

    /**
     * Add state
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function state($message)
    {
        if (!self::$Active) {
            return;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            $message = array_shift($args);
            $message = vsprintf($message, $args);
        }
        self::add($message, 'state');
    } // function State()

    /**
     * Add SQL
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function SQL($sql)
    {
        if (!self::$Active) {
            return;
        }

        if (is_array($sql)) {
            foreach ($sql as $q) {
                self::add($q, 'sql', true);
            }
        } else {
            self::add($sql, 'sql', true);
        }
    } // function SQL()

    /**
     * Add debug
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function debug($message)
    {
        if (!self::$Active) {
            return;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            $message = array_shift($args);
            $message = vsprintf($message, $args);
        }
        self::add($message, 'debug', true);
    } // function Debug()

    /**
     * Add a warning
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function warning($message)
    {
        if (!self::$Active) {
            return;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            $message = array_shift($args);
            $message = vsprintf($message, $args);
        }
        self::add($message, 'warning');
    } // function Warning()

    /**
     * Add an error
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function error($message)
    {
        if (!self::$Active) {
            return;
        }

        if (func_num_args() > 1) {
            $args = func_get_args();
            $message = array_shift($args);
            $message = vsprintf($message, $args);
        }
        self::add($message, 'error');
    } // function Error()

    /**
     * Add an error
     *
     * If more parameters are passed, $message will handeld with printf.
     *
     * @uses add
     * @param string $message
     * @return void
     */
    public static function call($args)
    {
        if (!self::$Active) {
            return;
        }

        $params = array();
        foreach ($args as $arg) {
            $params[] = is_scalar($arg) ? $arg : gettype($arg);
        }
        self::add(implode(', ', $params), 'call');
    } // function Call()

    /**
     * Add a trace
     *
     * @uses add
     * @param int $level Trace level from here
     * @param bool $full Full trace, all remaining levels
     * @param bool $params Include function parameters
     * @return void
     */
    public static function trace($level = 1, $full = false, $params = true)
    {
        if (!self::$Active) {
            return;
        }

        $trace = debug_backtrace();

        if (!isset($trace[$level])) {
            while (!isset($trace[$level])) {
                $level--;
            }
        }

        for ($i=$level; $i>0;
        $i--) {
            array_shift($trace);
        }

        $traces = $full ? $trace : array($trace[0]);

        foreach ($traces as $trace) {
            $msg = isset($trace['file'])
                 ? str_replace(@$_SERVER['DOCUMENT_ROOT'].'/', '', $trace['file'])
                 : '[PHP Kernel]';
            if (isset($trace['line'])) {
                $msg .= ' [' . $trace['line'] . ']';
            }
            $msg .= ' ';

            if (isset($trace['object'])) {
                $msg .= sprintf(
                    '%s[%s]%s%s',
                    get_class($trace['object']), $trace['class'], $trace['type'], $trace['function']
                );
            } elseif (isset($trace['class'])) {
                $msg .= sprintf('%s%s%s', $trace['class'], $trace['type'], $trace['function']);
            } elseif (isset($trace['function'])) {
                $msg .= $trace['function'];
            }

            if ($params) {
                $args = array();
                if (!empty($trace['args'])) {
                    foreach ($trace['args'] as $arg) {
                        $args[] = self::format($arg);
                    }
                }
                $msg .= '(' . implode(', ', $args) . ')';
            }
            self::add($msg, 'trace');
        }
    } // function Trace()

    /**
     * Generic add message
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public static function add($message = '', $type = 'info', $raw = false)
    {
        if (!self::$Active) {
            return;
        }

        $ts = microtime(true);
        $call = self::called(3);

        $data = array( $ts, $type, $call[0], $call[1], $message);

        if (self::$TraceFile && $fh = fopen(self::$TraceFile, 'a')) {
            // overwrite locale settings!!
            $data[0] = sprintf('%.1f ms', ($data[0]-$_SERVER['REQUEST_TIME'])*1000);
            fwrite($fh, str_replace("\n", '\n', implode(self::$TraceDelimiter, $data))."\n");
            fclose($fh);
        }

        $data[] = self::$TimerLevel;
        $data[] = $raw;

        self::$Data[] = $data;

        return $ts;
    } // function add()

    /**
     * Starts a timer
     *
     * @param string $id
     * @param string $name
     * @return void
     */
    public static function startTimer($id, $name = '', $avg = '')
    {
        if (!self::$Active) {
            return;
        }
        if (isset(self::$Timer[$id])) {
            throw new Yryie\Exception('Error: Timer "'.$id.'" ('.$name.') ist still started!');
        }

        if ($name == '') {
            $name = $id;
        }
        $msg = self::$TimerStart . ' ' . $name;
        if ($avg) {
            $msg .= ' (' . $avg . ')';
        }
        self::add($msg, 'timer');
        self::$Timer[$id] = array(microtime(true), $name, $avg);
        self::$TimerLevel++;
    } // function StartTimer()

    /**
     * Stop a timer
     *
     * @param string $id Stop last timer if empty
     * @return void
     */
    public static function stopTimer($id = '')
    {
        if (!count(self::$Timer)) {
            return;
        }

        if ($id == '') {
            // get last id from stack
            $ids = array_keys(self::$Timer);
            $id = end($ids);
        }
        list($start, $name, $avg) = self::$Timer[$id];
        $diff = microtime(true) - $start;
        self::$TimerLevel--;
        self::add(sprintf('%s %s: %s', self::$TimerStop, $name, self::timef($diff)), 'timer');
        unset(self::$Timer[$id]);

        if ($avg != '') {
            if (!isset(self::$AVG[$avg])) {
                self::$AVG[$avg] = array(0,0);
            }
            self::$AVG[$avg][0] += $diff;
            self::$AVG[$avg][1]++;
        }

        return $diff;
    } // function StopTimer()

    /**
     * Get messages
     *
     * @param string $type Get only messages of this type
     * @return array
     */
    public static function get($type = '')
    {
        if ($type != '') {
            $return = array();
            foreach (self::$Data as $data) {
                if ($data[1] == $type) {
                    $return[] = $data;
                }
            }
        } else {
            $return = self::$Data;
        }
        return $return;
    } // function get()

    /**
     * Finalize all timers
     *
     * @return void
     */
    public static function finalizeTimers()
    {
        // stop all open timers
        for ($i=count(self::$Timer); $i>0;
        $i--) {
            self::StopTimer();
        }

        foreach (self::$AVG as $avg => $data) {
            self::add(
                sprintf(
                    'avg. "%1$s": %4$s (%3$d in %2$s)',
                    $avg, self::timef($data[0]), $data[1],
                    self::timef($data[0]/$data[1])
                ),
                'timer'
            );
        }
    } // function finalizeTimers()

    /**
     * Finalize all
     *
     * @return void
     */
    public static function finalize()
    {
        self::finalizeTimers();
        self::Debug(
            'build in %s; %d kByte memory used; %d files included; %d messages collected',
            self::timef((microtime(true)-$_SERVER['REQUEST_TIME']), self::TIME_AUTO),
            memory_get_peak_usage(true)/1024,
            count(get_included_files()),
            count(self::$Data)
        );
    } // function Finalize()

    /**
     * Get all messages as comma separated data
     *
     * @param bool $delta Show delta time since start, not absolut timestamp
     * @return string
     */
    public static function loadFromSession()
    {
        if (isset($_SESSION['Yryie'])) {
            self::$Data = $_SESSION['Yryie'];
            unset($_SESSION['Yryie']);
            self::Debug('-------------------- SESSION --------------------');
        }
        return !empty(self::$Data);
    } // function loadFromSession()

    /**
     * Get all messages as comma separated data
     *
     * @param bool $delta Show delta time since start, not absolut timestamp
     * @return string
     */
    public static function saveToSession()
    {
        if (!empty(self::$Data)) {
            $_SESSION['Yryie'] = self::$Data;
        }
    } // function saveToSession()

    /**
     * Get all messages as comma separated data
     *
     * @param bool $delta Show delta time since start, not absolut timestamp
     * @return string
     */
    public static function CSV()
    {
        while (count(self::$Timer)) {
            self::StopTimer();
        }

        $csv = array(
            sprintf('Time%1$sType%1$sClass%1$sFunction%1$sMessage', self::$TraceDelimiter)
        );

        foreach (self::$Data as $data) {
            if (!$data[0]) {
                continue;
            }

            $data[0] -= $_SERVER['REQUEST_TIME'];

            if (is_array($data[4])) {
                $data[4] = self::format($data[4]);
            }
            // skip empty messages
            if ($data[4] == '') {
                continue;
            }

            // remove timer level
            unset($data[5]);

            $fields = array();
            foreach ($data as $value) {
                if (strpos($value, '"') !== false) {
                    $value = '"' . str_replace('"', '""', $value) . '"';
                }
                if (strpos($value, self::$TraceDelimiter) !== false) {
                    $value = '"' . $value . '"';
                }
                // remove add. white spaces AND newlines
                $fields[] = preg_replace('~\s+~', ' ', trim($value));
            }
            $csv[] = implode(self::$TraceDelimiter, $fields);
        }

        return implode("\n", $csv)."\n";
    } // function CSV()

    /**
     * Save messages as csv to a file
     *
     * @param string $file File name to save to
     * @param bool $delta Show delta time since start, not absolut timestamp
     * @param bool $append Append to the file, if exists
     * @return void
     */
    public static function save($file, $append = false)
    {
        $fh = fopen($file, ($append ? 'a' : 'w'));
        if ($fh) {
            fwrite($fh, self::CSV());
            fclose($fh);
        } else {
            throw new Yryie\Exception('Can\'t write to file: '.$file);
        }
    } // function Save()

    /**
     * Output the messages as HTML table
     *
     * @param bool $delta Show delta time since start, not absolut timestamp
     * @return void
     */
    public static function output($delta = false, $CssJs = false)
    {
        if ($CssJs) {
            echo self::getCSS(), self::getJS();
        }
        echo self::Render($delta);
    } // function Output()

    /**
     * Return messages as HTML table content
     *
     * All rows/cells are taged with classes, so you can format with CSS as you like
     *
     * @param bool $delta Show delta time since start, not absolut timestamp
     * @return string
     */
    public static function render($delta = false)
    {
        $types = array();
        foreach (self::$Data as $row => $data) {
            $types[$data[1]] = $data[1];
        }
        $sTypes = '';
        $cb = '<input type="checkbox" style="margin-left:1.5em" '
             .'onchange="YryieSwitch(\'%s\', this.checked)" checked> %s';
        foreach ($types as $type) {
            if ($type) {
                $sTypes .= sprintf($cb, $type, ucwords($type));
            }
        }
        unset($types);

        $aRows = array();
        // last time stamp
        $lts = $_SERVER['REQUEST_TIME'];

        foreach (self::$Data as $row => $data) {
            @list($time, $type, $class, $func, $msg, $level, $raw) = $data;
            $cls = $row%2 ? 'even' : 'odd';

            if ($type == 'call' || $msg) {
                $ts = $delta
                    ? self::timef($time-$_SERVER['REQUEST_TIME'])
                    : date('H:i:s', $time) . substr(sprintf('%.5f', $data[0]), -6);
                $dts = self::timef($time-$lts);
                $lts = $time;
                $msg = is_array($msg)
                     ? '<pre>' . htmlspecialchars(print_r($msg, true)) . '</pre>'
                     : ( $raw ? '<pre>'.$msg.'</pre>' : htmlspecialchars($msg) );

                $utype = ucwords($type);
                if (!$class) {
                    $class = '&nbsp;';
                }
                if (!$func) {
                    $func = '&nbsp;';
                }

                $aRows[] = sprintf(self::$TplRow, $type, $cls, $ts, $dts, $utype, $class, $func, $level, $msg);
            } else {
                // empty row
                $aRows[] = '<tr class="'.$type.' '.$cls.'"><td colspan="6">&nbsp;</td></tr>';
            }
        }
        return sprintf(self::$TplTable, self::VERSION, $sTypes, implode($aRows));
    } // function Render()

    /**
     * Reset all data
     *
     * @return void
     */
    public static function reset()
    {
        self::$Data = array();
    } // function reset()

    /**
     * Error handler
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     */
    public static function handleError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (!$errno = $errno & error_reporting()) {
            return;
        }

        $errfile = str_replace(@$_SERVER['DOCUMENT_ROOT'].'/', '', $errfile);

        // from PHP 5
        defined('E_STRICT')            || define('E_STRICT', 2048);
        // from PHP 5.2.0
        defined('E_RECOVERABLE_ERROR') || define('E_RECOVERABLE_ERROR', 4096);
        // from PHP 5.3.0
        defined('E_DEPRECATED')        || define('E_DEPRECATED', 8192);
        defined('E_USER_DEPRECATED')   || define('E_USER_DEPRECATED', 16384);

        static $Err2Str = array(
            E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            E_PARSE             => 'Parse Error',
            E_NOTICE            => 'Notice',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error'
        );

        $str = isset($Err2Str[$errno]) ? $Err2Str[$errno] : 'Unknown error: '.$errno;
        $errmsg = sprintf('[%s] %s in %s (%d)'."\n", $str, $errstr, $errfile, $errline);
        self::add($errmsg, 'handler', true);
        self::Trace(2, true, false);
        self::Debug($errcontext);
        if ($errno & (E_ERROR|E_CORE_ERROR|E_USER_ERROR)) {
            die(self::getCSS().self::HTML());
        }
    } // function HandleError()

    /**
     * Get a good default CSS
     *
     * @param bool $withTag With style tags around CSS
     * @return string
     */
    public static function getCSS($withTag = true)
    {
        return ( $withTag ? '<style type="text/css">'."\n" : '' )
             . file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Yryie'.DIRECTORY_SEPARATOR.'yryie.min.css')
             . ( $withTag ? '</style>' : '' );
    } // function getCSS();

    /**
     * Return JS for type select
     *
     * @param bool $withTag With script tags around script
     * @return string
     */
    public static function getJS($withTag = true, $jquery = false)
    {
        $file = $jquery ? 'yryie.jquery.min.js' : 'yryie.min.js';
        return ( $withTag ? '<script>' : '' )
             . file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Yryie'.DIRECTORY_SEPARATOR.$file)
             . ( $withTag ? '</script>' : '' );
    } // function getJS()

    /**
     * Format an array to a string according to the type of data
     *
     * @param array $value Value to format
     * @return string
     */
    public static function format($value, $truncate = true)
    {
        $args = '';
        switch (gettype($value)) {
            case 'integer':
            case 'double':
                $args .= $value;
                break;
            case 'string':
                if ($truncate) {
                    $v = substr($value, 0, self::MAXSTRLEN);
                    if ($v != $value) {
                        $v .= '...';
                    }
                    $value = $v;
                }
                $args .= '\'' . $value . '\'';
                break;
            case 'array':
                $aa = array();
                foreach ($value as $key => $val) {
                    if ($key != 'GLOBALS') {
                        $aa[] = sprintf('\'%s\'=>%s', $key, self::format($val));
                    }
                }
                $args .= 'Array(' . implode(', ', $aa) . ')';
                break;
            case 'object':
                $args .= 'Object(' . get_class($value) . ')';
                break;
            case 'resource':
                $args .= 'Resource('. strstr($value, '#') . ')';
                break;
            case 'boolean':
                $args .= $value ? 'TRUE' : 'FALSE';
                break;
            case 'NULL':
                $args .= 'NULL';
                break;
            default:
                $args .= '[Unknown]';
        }
        return $args;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * Data storage
     *
     * @var array
     */
    protected static $Data = array();

    /**
     *
     */
    protected static $funcParams = ' \Yryie::Call(func_get_args()); /// AOP';

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Active flag
     *
     * @var bool
     */
    private static $Active = true;

    /**
     *
     * @var int
     */
    private static $TimeUnit = self::TIME_SECONDS;

    /**
     * Active timer data
     *
     * @var array
     */
    private static $Timer = array();

    /**
     * Active timer data
     *
     * @var array
     */
    private static $AVG = array();

    /**
     * Active timer level
     *
     * @var int
     */
    private static $TimerLevel = 0;

    /**
     * Table template
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    private static $TplTable = '
        <div id="Yryie">
            <table>
            <thead>
                <tr>
                    <th colspan="2" class="header">
                        <img style="margin-right:8px;cursor:pointer" onclick="YryieToggle()" title="Toggle table rows"
                                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIxSURBVHjaYvz//z8DJQAggBgDC1dm2ziYdL779p8bbNa/fwz/gfjvv79A+j/Dv/9A/t+/QPo/mP4LlONj+/31+vWn5ev6wqYCBBCLibXJJFYObiZxDpIs5v767e8kID0VIIBYvv3+z/Tt3ReSnf4dqA9EAwQQy/+//xlao1VINiCl4yGYBggglv9AP4JA2q7vDLPcOBn27dsHDIZ/DH/+/GFwcHZn2Ld7O4OFnTvDkX1bGUAB/hcYDkFBQeAwAgGAAGJhgMbCZEcOhh+//zNY2joCA/I/wx+ouLWjO1iJtZMX0GBQoEJc8O8PxACAAGKCmZS95ysDBysjw5EDexgOHdjNcGD3NrD4/p2bGRgZGRj2blsHxGsZ9m5dDTHgL8TlAAHEUDrvyn8Q+PXn3/+fv//9//ELgr8B8Xcg/vL9z/9PQPzh25//77/++f/uyx+w+qiaTSCKASCAmP5BXZCx4yMDGwsjw16gn3fv3MqwdeNasPiWjWsYmIFO2LRmGcP6lYsY1i6fD3HBP4gLAAKIIX/6GbCJv//++/8b6goQ/v4LFX/58ff/Z6ArQBgEQkrXgF0AEEBM/6F+Sd38loGFmZFh5/YtDFs3b2BYt3o5WHz1iiVgesmC2QyL5s9kmDd7Kpj/H+oCgABiyJl04j85ILBoJdgFAAHE8vT+w1WZ/b/D/oHSO9A1oHgG+e/v799gGpwHQPniLzRfgMQglq8CEQABxEhpbgQIICYGCgFAgAEAg5qXcfrnux4AAAAASUVORK5CYII=" />
                        Yryie %1$s
                    </th>
                    <th colspan="4" class="hide">
                        Visible types: %2$s
                        <a style="float:right" href="?debug=0">disable</a>
                    </th>
                </tr>
                <tr class="hide">
                    <th colspan="2" class="time">Time</th>
                    <th class="type">Type</th>
                    <th class="class">Class</th>
                    <th class="function">Function</th>
                    <th class="msg">Message</th>
                </tr>
            </thead>
            <tbody>
                %3$s
            </tbody>
            </table>
        </div>
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table row template
     *
     * @var string
     */
    private static $TplRow = '
        <tr class="%1$s %2$s hide">
            <td class="time" style="white-space:nowrap">%3$s</td>
            <td class="time" style="white-space:nowrap">%4$s</td>
            <td class="type">%5$s</td>
            <td class="class">%6$s</td>
            <td class="function">%7$s</td>
            <td class="msg indent%8$s">%9$s</td>
        </tr>';

    /**
     * Format time according to {@link $TimeUnit}
     *
     * @param int $time Timestamp
     * @return string
     */
    private static function timef($time, $format = null)
    {
        switch ($format ?: self::$TimeUnit) {
            case self::TIME_AUTO:
                return $time < 1 ? sprintf('%.3fms', $time*1000) : sprintf('%.3fs', $time);
            case self::TIME_MICROSECONDS:
                return sprintf('%.3fms', $time*1000);
            default:
                return sprintf('%.3fs', $time);
        }
    }

    /**
     * Return backtrace context, class and function
     *
     * @param int $skip Skip level
     * @return array Array( class, function )
     */
    private static function called($skip)
    {
        $bt = debug_backtrace();
        return array(@$bt[$skip]['class'], @$bt[$skip]['function']);
    }
}
