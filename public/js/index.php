<?php

#ini_set('display_startup_errors', 1);
#ini_set('display_errors', 1);
#error_reporting(-1);

define('CACHEDIR', realpath('../../tmp'));

// 0, 10, 62, 95 or 'None', 'Numeric', 'Normal', 'High ASCII'
define('JSENCODING', 62);

$files = array();
$lastModified = 0;
$force = FALSE;

foreach (explode('&', $_SERVER['QUERY_STRING']) as $file) {
  $file = trim($file, '%20');

  if ($file == '~f') { $force = TRUE; continue; }

  $file = (substr($file, 0, 1) == '/')
        ? $_SERVER['DOCUMENT_ROOT'] . $file
        : __DIR__ . DIRECTORY_SEPARATOR . $file;

  if (!is_file($file)) continue;

  $mtime = filemtime($file);
  if ($mtime > $lastModified) $lastModified = $mtime;
  $files[] = $file;
}

$cacheFile = CACHEDIR . DIRECTORY_SEPARATOR
           . substr(md5(implode($files) . $lastModified . JSENCODING), 0, 7) . '.js';

$cacheExists = is_file($cacheFile);

$clientIsCurrent = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
                 ? (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified)
                 : FALSE;

if ($cacheExists AND $clientIsCurrent) {
  header('HTTP/1.1 304 Not Modified');
  exit;
}

if (JSENCODING) {
  require_once 'class.JavaScriptPacker.php';
}

if ($force OR !$cacheExists) {

  // Create the combined js and cache it
  $content = '';

  foreach ($files as $file) {

    $js = file_get_contents($file);

    if (JSENCODING) {
      $packer = new JavaScriptPacker($js, JSENCODING);
      $js = $packer->pack();
    }

    $content .= '/* ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . ' */' . "\n" . $js;

  } 

  file_put_contents($cacheFile, $content);
  touch($cacheFile, $lastModified);
}

header('Content-Type: application/javascript; charset: UTF-8');
header('Content-Length: ' . filesize($cacheFile));
header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $lastModified)));

readfile($cacheFile);