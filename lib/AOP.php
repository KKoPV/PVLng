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
use Core\Session;
use Core\PVLng;
use Yryie\Yryie;

/**
 *
 */
Yryie::setTimeUnit(Yryie::TIME_AUTO);
Yryie::versions(
    'MySQL '.PVLng::getDatabase()->queryOne('SELECT `pvlng_mysql_version`()')
);

/**
 * Define Loader callback to manipulate file content to include
 */
Loader::registerCallback(function ($filename) {
    // Insert .aop before file extension, so .../file.php becomes .../file.aop.php
    $parts = explode('.', realpath($filename));
    array_splice($parts, count($parts)-1, 0, 'aop');
    $filenameAOP = implode('.', $parts);

    // Strip root directory and replace directory separators with ~ to get unique names
    $filenameAOP = str_replace(PVLng::$TempDir, '', $filenameAOP);
    $filenameAOP = str_replace(PVLng::$RootDir, '', $filenameAOP);
    $filenameAOP = str_replace(DIRECTORY_SEPARATOR, '~', $filenameAOP);
    $filenameAOP = PVLng::path(PVLng::$TempDir, trim($filenameAOP, '~'));

    if (!file_exists($filenameAOP) || filemtime($filenameAOP) < filemtime($filename)) {
        // (Re-)Create AOP file
        $code = file_get_contents($filename);

        // Build file content hash to check if AOP relevant code was found
        $hash = md5($code);

        Yryie::startTimer(
            'Compile '
            . str_replace(PVLng::$RootDir.DIRECTORY_SEPARATOR, '', $filename)
            . ' to '.basename($filenameAOP)
        );

        Yryie::transformCode($code);

        if ($hash == md5($code)) {
            // No Yryie commands found, use original file content
            $code = "<?php include '$filename';";
        }

        if (file_put_contents($filenameAOP, $code)) {
            // File content was changed and AOP file could created, strip down
            file_put_contents($filenameAOP, php_strip_whitespace($filenameAOP));
            $filename = $filenameAOP;
        }
        Yryie::stopTimer(); // Compile
    } else {
        // AOP file still exists and is up-to-date
        $filename = $filenameAOP;
        Yryie::info('Reuse %s', basename($filename));
    }

    return $filename;
});

/**
 * Register middleware to handle output / save trace file
 */
class YryieMiddleware extends Slim\Middleware
{
    /**
     *
     */
    public function call()
    {
        $app = $this->app;
        $db  = $app->db;

        Yryie::loadFromSession();

        Yryie::call(func_get_args());

        // Run inner middleware and application
        $this->next->call();

        foreach (Session::$Messages as $msg) {
            Yryie::Debug('Session: '.$msg);
        }

        // Buffer query count and times
        $qCnt  = $db->getQueryCount();
        $qTime = $db->getQueryTime();

        // Analyse queries to find missing indexes
        foreach ($db->queries as $sql) {
            Yryie::SQL($sql);
            try {
                if (($res = $db->query('EXPLAIN '.$sql)) &&
                    ($res = $res->fetch_object()) && $res->possible_keys) {
                    Yryie::SQL('INDEX: '.$res->key.' ('.$res->possible_keys.')');
                }
            } catch (Exception $e) {
                Yryie::error($e->getMessage());
            }
        }

        Yryie::debug('%d queries in %.3fms (~%.0fms each)', $qCnt, $qTime, $qTime/$qCnt);

        if ($app->Response()->headers['Location']) {
            // Redirection
            Yryie::debug('Redirect to %s', $app->Response()->headers['Location']);
            Yryie::finalizeTimers();
            Yryie::saveToSession();
            return;
        }

        Yryie::finalize();

        $body = $app->Response()->getBody();
        $placeholder = '<div id="YRYIE"></div>';

        if ($app->debug == 3) {
            $file = PVLng::path(PVLng::$TempDir, 'trace.'.date('Y-m-d-H:i:s').'.csv');
            Yryie::$TraceDelimiter = ';';
            Yryie::saveToFile($file);
            $body = str_replace($placeholder, '<p><b>Trace saved as <tt>'.$file.'</tt></b></p>', $body);

            // Trace only once, reset debug state
            Session::set('debug');
        } else {
            // Replace placeholder with debug data
            $body = str_replace(
                $placeholder,
                Yryie::getCSS() . Yryie::getJS(true, true) . Yryie::render(),
                $body
            );
        }
        Yryie::reset();

        $app->Response()->setBody($body);
    }
}
