<?php
/**
 * AOP
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

Yryie::TimeUnit(Yryie::TIME_AUTO);
Yryie::Versions();

/**
 * Define Loader callback to manipulate file content to include
 */
Loader::registerCallback(function($filename) {
    // Insert .aop before file extension, so .../file.php becomes .../file.aop.php
    $parts = explode('.', realpath($filename));
    array_splice($parts, count($parts)-1, 0, 'aop');
    $filenameAOP = implode('.', $parts);

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

       # Yryie::Info('Compile: '.$filename);
        Yryie::StartTimer('Compile '.str_replace(ROOT_DIR.DS, '', $filename).' to '.basename($filenameAOP));
        Yryie::transformCode($code);

        if ($hash == md5($code)) $code = "<?php include '$filename';";

        if (file_put_contents($filenameAOP, $code)) {
            // File content was changed and AOP file could created
            $filename = $filenameAOP;
            #Yryie::Info('Created: '.$filename);
        }
        Yryie::StopTimer();
    } else {
        // AOP file still exists and is up-to-date
        $filename = $filenameAOP;
        Yryie::Info('reuse '.basename($filename));
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
        $app = $this->app;
        $db  = $app->db;

        Yryie::loadFromSession();

        Yryie::Call(func_get_args());

        // Run inner middleware and application
        $this->next->call();

        foreach (Session::$Messages as $msg) Yryie::Debug('Session: '.$msg);

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
            Yryie::Debug('Redirect to %s', $app->Response()->headers['Location']);
            Yryie::finalizeTimers();
            Yryie::saveToSession();
            return;
        }

        Yryie::finalize();

        $body = $app->Response()->getBody();
        $placeholder = '<div id="YRYIE"></div>';

        if ($app->debug == 3) {

            $file = TEMP_DIR . DS . 'trace.' . date('Y-m-d-H:i:s') . '.csv';
            Yryie::$TraceDelimiter = ';';
            Yryie::Save($file);
            $body = str_replace($placeholder, '<p><b>Trace saved as <tt>'.$file.'</tt></b></p>', $body);

            // Trace only once, reset debug state
            Session::set('debug');
        } else {
            // Replace placeholder with debug data
            $body = str_replace($placeholder,
                                Yryie::getCSS() . Yryie::getJS(true, true) . Yryie::Render(),
                                $body);
        }
        Yryie::reset();

        $app->Response()->setBody($body);
    }
}
