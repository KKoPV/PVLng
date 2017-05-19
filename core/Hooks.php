<?php
/**
 * Hook point implementation
 *
 * Searches files for special comments
 *
 * - inside classes
 * // hook <namenpace> <class> [<method> ]<#>
 *
 * - outside classes
 * // hook <path/to/file from root> <#>
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Define Loader callback to manipulate file content to include
 *
 /
Loader::registerCallback( function($file) {
    // Insert .hook before file extension, so .../file.php becomes .../file.hook.php
    $parts = explode('.', realpath($file));
    $filehook = $parts[0] . '.hook.' . $parts[count($parts)-1];

    // Strip root directory and replace directory separators with ~ to get unique names
    $filehook = str_replace(TEMP_DIR, '', $filehook);
    $filehook = str_replace(ROOT_DIR, '', $filehook);
    $filehook = str_replace(DIRECTORY_SEPARATOR, '~', $filehook);
    $filehook = trim($filehook, '~');
    $filehook = PVLng::path(TEMP_DIR, $filehook);

    if (!file_exists($filehook) OR filemtime($filehook) < filemtime($file)) {
        // (Re-)Create hook file
        $code = file_get_contents($file);

        // Build file content hash to check if AOP relevant code was found
        $hash = md5($code);

        if (preg_match_all('~^[ \t]*'.'// (hook .*?) //\s*$~m', $code, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
              // Replace (mostly) namespace separators and spaces with a hyphen
                $inc = PVLng::path('hook', preg_replace('~[^\w]+~', '-', $match[1])) . '.php';
                Yryie::Info('Look for '.$inc);
                if (file_exists(ROOT_DIR.$inc)) {
                    // Comment line
                    $c = '// ' . str_repeat('-', 75);

                    $icode = PHP_EOL . $c . PHP_EOL
                           . '// >>> '.$match[1] . PHP_EOL . PHP_EOL
                           . preg_replace('~<\?php\s+~is', '', file_get_contents(ROOT_DIR.$inc)) . PHP_EOL . PHP_EOL
                           . '// <<< '.$match[1] . PHP_EOL
                           . $c . PHP_EOL;
                } else {
                    $icode = $match[0] . ' ' . $inc . PHP_EOL;
                }
                $code = str_replace($match[0], $icode, $code);
            }
        }

        // If content was NOT changed just include original file
        if ($hash == md5($code)) {
            $code = "<?php include '$file';";
        }

        // Hooked file content could be written
        if (file_put_contents($filehook, $code)) $file = $filehook;
    } else {
        // Hooked file still exists and is ut-to-date
        $file = $filehook;
    }

    return $file;
});
*/
