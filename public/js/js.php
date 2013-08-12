<?php #ini_set('display_startup_errors',1); ini_set('display_errors',1); error_reporting(-1);

$ACTIVE = TRUE;
$ACTIVE = 0;

// ---------------------------------------------------------------------------
// Cache dir, must be writeable!
// ---------------------------------------------------------------------------
$CACHEDIR = realpath('../../tmp');

// 0, 10, 62, 95 or 'None', 'Numeric', 'Normal', 'High ASCII'
$JSENCODING = 62;

// ---------------------------------------------------------------------------
// DON'T CHANGE FROM HERE
// ---------------------------------------------------------------------------

header('Content-Type: application/javascript; charset: UTF-8');

$file = __DIR__ . DIRECTORY_SEPARATOR . $_SERVER['QUERY_STRING'];

if (!$ACTIVE) {
	readfile($file);
	exit;
}

if (is_file($file)) {
	$compress = (strpos($file, '.min.') === FALSE);
	$lastModified = filemtime($file);
} else {
	exit;
}

if ($compress) {
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

	if (JSENCODING) {
		require_once 'class.JavaScriptPacker.php';
	}

	if (!$cacheExists) {

		$js = file_get_contents($file);
		$l0 = strlen($js);

		if ($JSENCODING) {
			$packer = new JavaScriptPacker($js, $JSENCODING);
			$js = $packer->pack();
		}

		file_put_contents($cacheFile,
			'/* ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . ' - ' .
			sprintf('%d > %d', $l0, strlen($js)) . ' bytes */' . "\n" . $js
		);
		touch($cacheFile, $lastModified);
	}
} else {
	$cacheFile = $file;
}

header('Content-Length: ' . filesize($cacheFile));
header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $lastModified)));

// GZip compress content if applicable
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND
    strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') AND
    extension_loaded('zlib') ) {
	ob_start('ob_gzhandler');
}

readfile($cacheFile);