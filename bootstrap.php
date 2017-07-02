<?php
/**
 * Common bootstap file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

/**
 * Initialize
 */
setlocale(LC_NUMERIC, 'C');

/**
 * http://php.net/manual/de/function.iconv-set-encoding.php#119888
 */
if (PHP_VERSION_ID < 50600) {
    iconv_set_encoding('input_encoding', 'UTF-8');
    iconv_set_encoding('output_encoding', 'UTF-8');
    iconv_set_encoding('internal_encoding', 'UTF-8');
} else {
    ini_set('default_charset', 'UTF-8');
}

mb_internal_encoding('UTF-8');

clearstatcache();

/**
 *
 */
try {
    // Load basic class
    require implode(DIRECTORY_SEPARATOR, [__DIR__, 'core', 'Core', 'PVLng.php']);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
}
