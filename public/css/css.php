<?php #ini_set('display_startup_errors',1); ini_set('display_errors',1); error_reporting(-1);

// ---------------------------------------------------------------------------
// Cache dir, must be writeable!
// ---------------------------------------------------------------------------
$CACHEDIR = realpath('../../tmp');

// ---------------------------------------------------------------------------
// DON'T CHANGE FROM HERE
// ---------------------------------------------------------------------------

$file = __DIR__ . DIRECTORY_SEPARATOR . $_SERVER['QUERY_STRING'];

if (is_file($file)) {
  $lastModified = filemtime($file);
} else {
  exit;
}

$cacheFile = $CACHEDIR . DIRECTORY_SEPARATOR . $lastModified
           . str_replace(DIRECTORY_SEPARATOR, '~',
                         str_replace($_SERVER['DOCUMENT_ROOT'], '', $file));

$cacheExists = is_file($cacheFile);

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) AND
    (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) AND
    $cacheExists) {
  header('HTTP/1.1 304 Not Modified');
  exit;
}

if (!$cacheExists) {

  $css = file_get_contents($file);
  $l0 = strlen($css);

  // fix image URLs for each file
  if (preg_match_all('~url\([\'"]?(.*?)[\'"]?\)~i', $css, $matches, PREG_SET_ORDER)) {
    $base = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($file));
    foreach ($matches as $match) {
      if (strpos($match[0], 'data:image') === FALSE AND
          strpos($match[0], '//') === FALSE)
        $css = str_replace($match[0], sprintf('url(%s/%s)', $base, $match[1]), $css);
    }
  }

  $css = preg_replace(array(
           /* remove multiline comments */
           '~/\*.*?\*/~s',
           /* remove tabs, spaces, newlines, etc. */
           '~\r|\n|\t|\s\s+~',
           /* remove whitespace on both sides of colons : , and ; */
           '~\s?([,:;])\s?~',
           /* remove whitespace on both sides of curly brackets {} */
           '~;?\s?([{}])\s?~',
         ), array(
           '',
           '',
           '$1',
           '$1',
         ), $css);

  file_put_contents($cacheFile,
    '/* ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . ' - ' .
    sprintf('%d > %d', $l0, strlen($css)) . ' bytes */' . "\n" . $css
  );
  touch($cacheFile, $lastModified);
}

header('Content-Type: text/css; charset=utf-8');
header('Content-Length: ' . filesize($cacheFile));
header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $lastModified)));
header('Expires: ' . date('D, d M Y H:i:s', time() + (60*60*24*365)) . ' GMT');
header('Cache-Control: max-age='.(60*60*24*365).', s-maxage='.(60*60*24*365));

// GZip compress content if applicable
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND
    strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') AND
    extension_loaded('zlib') ) {
	ob_start('ob_gzhandler');
}

readfile($cacheFile);
