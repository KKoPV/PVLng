<?php

#ini_set('display_startup_errors', 1);
#ini_set('display_errors', 1);
#error_reporting(-1);

define('CACHEDIR', realpath('../../tmp'));

$files = array();
$lastModified = 0;
$force = FALSE;

foreach (explode('&', $_SERVER['QUERY_STRING']) as $file) {
  $file = trim($file, '%20/');

  if ($file == '~f') {
    $force = TRUE;
  } else {
    $file = (substr($file, 0, 1) == '/')
          ? $_SERVER['DOCUMENT_ROOT'] . $file
          : __DIR__ . DIRECTORY_SEPARATOR . $file;

    if (is_file($file)) {
      $mtime = filemtime($file);
      if ($mtime > $lastModified) $lastModified = $mtime;
      $files[] = $file;
    }
  }
}

$cacheFile = CACHEDIR . DIRECTORY_SEPARATOR
           . substr(md5(implode($files) . $lastModified), 0, 7) . '.css';

$cacheExists = is_file($cacheFile);

$clientIsCurrent = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
                 ? (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified)
                 : FALSE;

if ($cacheExists AND $clientIsCurrent) {
  header('HTTP/1.1 304 Not Modified');
  exit;
}

if ($force OR !$cacheExists) {
  // Create the combined css and cache it
  $content = '';
  $l0 = $l1 = 0;

  foreach ($files as $file) {

    $css = file_get_contents($file);
    $l0 += strlen($css);

    // fix image URLs for each file
    if (preg_match_all('~url\([\'"]?(.*?)[\'"]?\)~i', $css, $matches, PREG_SET_ORDER)) {
      $base = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($file));
      foreach ($matches as $match) {
        if (strpos($match[0], '//') === FALSE)
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

    $l1 += strlen($css);
    $content .= '/* ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . ' */' . "\n" . $css . "\n";
  }

  $content .= sprintf('/* %d > %d bytes */', $l0, $l1);

  file_put_contents($cacheFile, $content);
  touch($cacheFile, $lastModified);
}

header('Content-Type: text/css; charset: UTF-8');
header('Content-Length: ' . filesize($cacheFile));
header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $lastModified)));

readfile($cacheFile);