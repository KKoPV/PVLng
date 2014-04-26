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
 * Define Loader callback to manipulate file content to include
 */
Loader::registerCallback(function( $filename ) {
    // Insert .AOP before file extension, so .../file.php becomes .../file.AOP.php
    $parts = explode('.', $filename);
    array_splice($parts, -1, 0, 'AOP');
    $filenameAOP = implode('.', $parts);

    if (!file_exists($filenameAOP) OR filemtime($filenameAOP) < filemtime($filename)) {
        // (Re-)Create AOP file
        $code = file_get_contents($filename);

        if (strpos($code, '/* // AOP // */') !== FALSE) {
            // Only files marked as AOP relevant will be analysed

            // Build file content hash to check if AOP relevant code was found
            $hash = sha1($code, TRUE);

            // Single line comments: /// PHP code...
            $code = preg_replace('~^(\s*)///\s+([^*]*?)$~m', '$1$2 /// AOP', $code);

            // Multi line comments start: /* ///
            $code = preg_replace('~^(\s*)/\*\s+///(.*?)$~m', '$1/// >>> AOP$2', $code);
            // Multi line comments end: /// */
            $code = preg_replace('~^(\s*)///\s+\*/$~m', '$1/// <<< AOP', $code);

            if ($hash != sha1($code, TRUE) AND file_put_contents($filenameAOP, $code)) {
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
        Yryie::Versions();

        // Run inner middleware and application
        $this->next->call();

        Yryie::Finalize();

        $body = $this->app->response->getBody();
        $placeholder = '<div id="YRYIE"></div>';

        if ($this->app->debug == 'trace') {

            $file = TEMP_DIR . DS . 'trace.' . date('Y-m-d-H:i:s') . '.csv';
            Yryie::$TraceDelimiter = ';';
            Yryie::Save($file);
            $body = str_replace($placeholder, '<b>Trace saved as '.$file.'</b>', $body);

            // Trace only once, reset debug state
            Session::set('debug', NULL);
        } else {
            // Replace placeholder with debug data
            $body = str_replace($placeholder,
                                Yryie::getCSS() . Yryie::getJS(TRUE, TRUE) . Yryie::Render(),
                                $body);
        }

        $this->app->response->setBody($body);
    }
}

$app->add(new YryieMiddleware());
