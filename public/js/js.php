<?php

ini_set('display_startup_errors',1); ini_set('display_errors',1); error_reporting(-1);

$ACTIVE = TRUE;
#$ACTIVE = 0;

// ---------------------------------------------------------------------------
// Cache dir, must be writeable!
// ---------------------------------------------------------------------------
$CACHEDIR = realpath('../../tmp');

// 0, 10, 62, 95  for  None, Numeric, Normal, High ASCII
$JSENCODING = 62;

// ---------------------------------------------------------------------------
// DON'T CHANGE FROM HERE
// ---------------------------------------------------------------------------

header('Content-Type: application/javascript; charset: UTF-8');

$files = explode('+', $_SERVER['QUERY_STRING']);
$lastModified = 0;

foreach ($files as $id=>$file) {
	$file = __DIR__ . DIRECTORY_SEPARATOR . $file;
	if ($ACTIVE) {
		$lastModified = max($lastModified, filemtime($file));
		$files[$id] = $file;
	} else {
		readfile($file);
	}
}

if (!$ACTIVE) exit;

$cacheFile = $CACHEDIR . DIRECTORY_SEPARATOR . str_replace('+', '~', $_SERVER['QUERY_STRING']);

if (is_file($cacheFile) AND isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) AND
    (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified)) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}

if (!is_file($cacheFile) OR filemtime($cacheFile) < $lastModified) {

	// Recompile

	if ($JSENCODING) {
		require 'class.JavaScriptPacker.php';
	}

	$fh = fopen($cacheFile, 'w');

	foreach ($files as $file) {

		if (strpos($file, '.min.') !== FALSE) {
			fwrite($fh, file_get_contents($file));
		} else {

			$js = file_get_contents($file);
			$l0 = strlen($js);

			if ($JSENCODING) {
				$packer = new JavaScriptPacker($js, $JSENCODING);
				$js = $packer->pack();
			}

			$file = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $file);

			$l1 = strlen($js);
			fwrite($fh, sprintf('/* %s - %d > %d bytes (%.1f%%) */', $file, $l0, $l1, $l1/$l0*100) . "\n" . trim($js) . "\n");

		}

	}

	fclose($fh);

	touch($cacheFile, $lastModified);

}

header('Content-Type: application/javascript; charset=utf-8');
header('Content-Length: ' . filesize($cacheFile));
header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $lastModified)));
header('Expires: ' . date('D, d M Y H:i:s', time() + 60*60*24*365) . ' GMT');
header('Cache-Control: max-age='.(60*60*24*365).', s-maxage='.(60*60*24*365));

// GZip compress content if applicable
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND
    strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') AND
    extension_loaded('zlib') ) {
	ob_start('ob_gzhandler');
}

readfile($cacheFile);
