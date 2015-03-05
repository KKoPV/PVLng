<?php
/**
 * AOP
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

if (!$app->debug = Session::checkRequest('debug')) return;

Yryie::TimeUnit(Yryie::TIME_AUTO);
Yryie::Versions();

/**
 * Define Loader callback to manipulate file content to include
 */
Loader::registerCallback(function($filename) {
    // Insert .aop before file extension, so .../file.php becomes .../file.aop.php
    $parts = explode('.', realpath($filename));
    $filenameAOP = $parts[0] . '.aop.' . $parts[count($parts)-1];

    // Strip root directory and replace directory separators with ~ to get unique names
    $filenameAOP = str_replace(TEMP_DIR, '', $filenameAOP);
    $filenameAOP = str_replace(ROOT_DIR, '', $filenameAOP);
    $filenameAOP = str_replace(DS, '~', $filenameAOP);
    $filenameAOP = trim($filenameAOP, '~');
    $filenameAOP = TEMP_DIR . DS . $filenameAOP;

    if (!file_exists($filenameAOP) OR filemtime($filenameAOP) < filemtime($filename)) {
        // (Re-)Create AOP file
        $code = file_get_contents($filename);

        // Build file content hash to check if AOP relevant code was found
        $hash = md5($code);

        Yryie::Info('Compile: '.$filename);
        Yryie::StartTimer(basename($filenameAOP));
        Yryie::transformCode($code);
        Yryie::StopTimer();

        if ($hash == md5($code)) $code = "<?php include '$filename';";

        if (file_put_contents($filenameAOP, $code)) {
            // File content was changed and AOP file could created
            $filename = $filenameAOP;
            Yryie::Info('Created: '.$filename);
        }
    } else {
        // AOP file still exists and is up-to-date
        $filename = $filenameAOP;
        Yryie::Info('Reuse: '.basename($filename));
    }
    return $filename;
});

/**
 * Register middleware to handle output / save trace file
 */
class YryieMiddleware extends Slim\Middleware {

    /**
     *
     */
    public function call() {
        Yryie::Call(func_get_args());

        $app = $this->app;
        $db  = $app->db;

        // Run inner middleware and application
        $this->next->call();

        // Buffer query count and times
        $qCnt  = $db->getQueryCount();
        $qTime = $db->getQueryTime();

        // Analyse queries to find missing indexes
        foreach ($db->queries as $sql) {
            Yryie::SQL($sql);
            if ($res = $db->query('EXPLAIN '.$sql)) {
                $res = $res->fetch_object();
                Yryie::SQL('INDEX: '.$res->key.' ('.$res->possible_keys.')');
            }
        }

        Yryie::Debug('%d queries in %.3fms (%.3fms each)', $qCnt, $qTime, $qTime/$qCnt);

        if ($app->Response()->headers['Location']) {
            // Redirection
            Yryie::finalizeTimers();
            Yryie::saveToSession();
            return;
        }

        Yryie::finalize();

        $body = $app->Response()->getBody();
        $placeholder = '<div id="YRYIE"></div>';

        if ($app->debug == 'trace') {

            $file = TEMP_DIR . DS . 'trace.' . date('Y-m-d-H:i:s') . '.csv';
            Yryie::$TraceDelimiter = ';';
            Yryie::Save($file);
            $body = str_replace($placeholder, '<p><b>Trace saved as <tt>'.$file.'</tt></b></p>', $body);

            // Trace only once, reset debug state
            Session::set('debug');
        } else {
            // Replace placeholder with debug data
            $body = str_replace($placeholder,
                                Yryie::getCSS() . Yryie::getJS(TRUE, TRUE) . Yryie::Render(),
                                $body);
        }
        Yryie::reset();

        $app->Response()->setBody($body);
    }
}
