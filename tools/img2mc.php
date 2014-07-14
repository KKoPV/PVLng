#!/usr/local/bin/php
<?php
/**
 * Add all images to MemCache, if available
 *
 * http://www.kutukupret.com/2011/05/13/nginx-memcached-module-caching-webiste-image-using-memcached/
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2013 Knut Kohl
 * @license    MIT License http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * MemCache host and port
 */
$host = array('127.0.0.1', 11211);

// ---------------------------------------------------------------------------
function scandir_recursive( $base='', &$data=array() ) {
    $array = array_diff(scandir($base), array('.', '..'));

    foreach ($array as $value) {
        if (is_dir($base . '/' . $value)) {
            $data = scandir_recursive($base . '/' . $value, $data);
        } elseif (is_file($base . '/' . $value)) {
            $parts = explode('.', $value);
            $ext = array_pop($parts);
            if (in_array($ext, array('png','gif','jpg','jpeg','ico'))) {
                $data[] = $base . '/' . $value;
            }
        }
    }

    return $data;
}

/**
 * Let's go
 */
try {
    if (!class_exists('Memcache')) {
        throw new Exception('Extension MemCache not installed.');
    }

    $memcache = new Memcache;

    if (!@$memcache->connect($host[0], $host[1])) {
        throw new Exception('Unable to connect to MemCache at '.$host[0].':'.$host[1]);
    }

    $dir = realpath(__DIR__ . '/../public');

    foreach (scandir_recursive($dir) as $id=>$file) {
        $url = str_replace($dir, '', $file);
        printf('[%3d] %-60s - ', $id, $url);
        echo ($memcache->add($url, file_get_contents($file), FALSE, 0) ? 'Ok' : 'exsists'),
             "\n";
    }
} catch (Exception $e) {
    echo "\n", $e->getMessage(), "\n";
}
