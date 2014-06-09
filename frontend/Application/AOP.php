<?php
/**
 * AOP
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

if (!($app->debug = Session::checkRequest('debug'))) return;

/**
 * Preload Yryie
 */
require_once LIB_DIR . DS . 'Yryie.php';

/**
 * Define Loader callback to manipulate file content to include
 */
Loader::registerCallback(function( $filename ) {
    // Insert .AOP before file extension, so .../file.php becomes .../file.AOP.php
    $parts = explode('.', realpath($filename));
    array_splice($parts, -1, 0, 'AOP');
    $filenameAOP = implode('.', $parts);

    // Strip root directory and replace directory separators with ~ to get unique names
    $filenameAOP = str_replace(ROOT_DIR, '', $filenameAOP);
    $filenameAOP = str_replace(DS, '~', $filenameAOP);
    $filenameAOP = trim($filenameAOP, '~');
    $filenameAOP = TEMP_DIR . DS . $filenameAOP;

    if (!file_exists($filenameAOP) OR filemtime($filenameAOP) < filemtime($filename)) {
        // (Re-)Create AOP file
        $code = file_get_contents($filename);

        // Only files marked as AOP relevant will be analysed
        if (strpos($code, '/* // AOP // */') !== FALSE) {

            // Build file content hash to check if AOP relevant code was found
            $hash = md5($code);

            Yryie::transformCode($code);

            if ($hash != md5($code) AND file_put_contents($filenameAOP, $code)) {
                // File content was changed and AOP file could created
                $filename = $filenameAOP;
            }
        }
    } else {
        // AOP file still exists and is ut-to-date
        $filename = $filenameAOP;
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
        // Put versions infos on top
        if (!Yryie::loadFromSession()) Yryie::Versions();

        // Run inner middleware and application
        $this->next->call();

        Yryie::SQL(slimMVC\MySQLi::getInstance()->queries);

        Yryie::Debug(
            '%d Queries in %.0f ms / %.1f ms each',
            slimMVC\MySQLi::$QueryCount,
            slimMVC\MySQLi::$QueryTime,
            slimMVC\MySQLi::$QueryTime / slimMVC\MySQLi::$QueryCount
        );

        if ($this->app->response->headers['Location']) {
            // Redirection
            Yryie::finalizeTimers();
            Yryie::saveToSession();
            return;
        }

        Yryie::finalize();

        $body = $this->app->response->getBody();
        $placeholder = '<div id="YRYIE"></div>';

        if ($this->app->debug == 'trace') {

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

        $this->app->response->setBody($body);
    }
}

$app->add(new YryieMiddleware());
